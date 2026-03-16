<?php

namespace App\Http\Services;

use App\Http\Services\Logs\VolunteerLoggerService;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Collection;

class VolunteerService
{
    private VolunteerLoggerService $logger;
    private HumHubApiService $humHub;

    public function __construct(
        VolunteerLoggerService $logger,
        HumHubApiService $humHub,
    ) {
        $this->logger = $logger;
        $this->humHub = $humHub;
    }

    public function save(Volunteer $volunteer): void
    {
        $isNew = !$volunteer->id;
        $volunteer->save();

        if (!$isNew) {
            $old = $this->getFromId($volunteer->id);
            $this->logger->log($volunteer, $old);
        } else {
            $this->logger->log($volunteer, new Volunteer());
        }
    }

    /**
     * Returns the old version of the current volunteer. If it has not already been inserted in
     * database, returns a new empty volunteer.
     *
     * @param Volunteer $volunteer
     * @return Volunteer The database instance of the requested volunteer, or an empty instance if it does not exist
     */
    public function getFromVolunteer(Volunteer $volunteer): Volunteer
    {
        if (!$volunteer->id) {
            return new Volunteer();
        }
        return $this->getFromId($volunteer->id);
    }

    public function getFromId(int $id): Volunteer
    {
        return Volunteer::find($id);
    }

    /**
     * @return Collection<int, Volunteer>
     */
    public function getAll(): Collection
    {
        return Volunteer::all();
    }

    public function delete(Volunteer $volunteer): void
    {
        $this->logger->logDelete($volunteer);
        $volunteer->delete();
    }

    /**
     * Récupère le profil HumHub d'un bénévole via son e-mail.
     * Retourne null si aucun utilisateur correspondant n'existe dans HumHub.
     *
     * @param Volunteer $volunteer
     * @return array|null
     */
    public function getHumHubProfile(Volunteer $volunteer): ?array
    {
        if (!$volunteer->email) {
            return null;
        }
        return $this->humHub->getUserByEmail($volunteer->email);
    }

    /**
     * Récupère le profil HumHub d'un bénévole via son ID HumHub stocké localement.
     *
     * @param Volunteer $volunteer
     * @return array|null
     */
    public function getHumHubProfileById(Volunteer $volunteer): ?array
    {
        if (!$volunteer->humhub_id) {
            return null;
        }
        return $this->humHub->getUserById($volunteer->humhub_id);
    }

    /**
     * Récupère tous les groupes HumHub auxquels appartient un bénévole.
     * Nécessite que le champ humhub_id soit renseigné sur le modèle Volunteer.
     *
     * @param Volunteer $volunteer
     * @return array
     */
    public function getHumHubGroups(Volunteer $volunteer): array
    {
        if (!$volunteer->humhub_id) {
            return [];
        }

        $allGroups = $this->humHub->getAllGroups();
        $memberOf = [];

        foreach ($allGroups["results"] ?? [] as $group) {
            $members = $this->humHub->getGroupMembers($group["id"]);
            $ids = array_column($members["results"] ?? [], "id");

            if (in_array($volunteer->humhub_id, $ids, true)) {
                $memberOf[] = $group;
            }
        }

        return $memberOf;
    }

    /**
     * Retourne tous les bénévoles avec leur profil HumHub intégré,
     * en ne faisant qu'une seule requête (paginée) à l'API distante.
     *
     * @return Collection<int, Volunteer> Chaque Volunteer a un attribut dynamique 'humhub_profile' (array|null)
     */
    public function getAllWithHumHubProfiles(): Collection
    {
        $volunteers = Volunteer::all();
        $humhubUsers = $this->humHub->getAllUsersIndexed();

        return $volunteers->each(function (Volunteer $volunteer) use (
            $humhubUsers,
        ) {
            $volunteer->setAttribute(
                "humhub_profile",
                $humhubUsers[$volunteer->humhub_id] ?? null,
            );
        });
    }
}
