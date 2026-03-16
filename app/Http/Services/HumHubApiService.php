<?php

namespace App\Http\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class HumHubApiService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config("services.humhub.url"), "/");
        $this->apiKey = config("services.humhub.key");
    }

    /**
     * Récupère tous les utilisateurs (paginé).
     *
     * @param int $page  Numéro de page (>= 0)
     * @param int $limit Nombre de résultats par page (1-50, défaut 20)
     * @return array{total: int, page: int, results: array}
     */
    public function getAllUsers(int $page = 1, int $limit = 20): array
    {
        return $this->get("/user", ["page" => $page, "limit" => $limit]);
    }

    /**
     * Récupère un utilisateur HumHub par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getUserById(int $id): ?array
    {
        return $this->get("/user/{$id}");
    }

    /**
     * Récupère un utilisateur HumHub par son nom d'utilisateur.
     *
     * @param string $username
     * @return array|null
     */
    public function getUserByUsername(string $username): ?array
    {
        return $this->get("/user/get-by-username", ["username" => $username]);
    }

    /**
     * Récupère un utilisateur HumHub par son adresse e-mail.
     *
     * @param string $email
     * @return array|null
     */
    public function getUserByEmail(string $email): ?array
    {
        return $this->get("/user/get-by-email", ["email" => $email]);
    }

    /**
     * Récupère tous les groupes (paginé).
     *
     * @param int $page
     * @param int $limit
     * @return array{total: int, page: int, results: array}
     */
    public function getAllGroups(int $page = 1, int $limit = 20): array
    {
        return $this->get("/user/group", ["page" => $page, "limit" => $limit]);
    }

    /**
     * Récupère un groupe par son ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getGroupById(int $id): ?array
    {
        return $this->get("/user/group/{$id}");
    }

    /**
     * Récupère les membres d'un groupe.
     *
     * @param int $groupId
     * @return array{total: int, page: int, results: array}
     */
    public function getGroupMembers(int $groupId): array
    {
        return $this->get("/user/group/{$groupId}/member");
    }

    /**
     * Effectue une requête GET authentifiée vers l'API HumHub.
     * Retourne null si la réponse est 404, lance une exception pour les autres erreurs.
     *
     * @param string $endpoint
     * @param array  $query
     * @return array|null
     */
    private function get(string $endpoint, array $query = []): ?array
    {
        $response = Http::withBasicAuth("api_token", $this->apiKey)
            ->acceptJson()
            ->get($this->baseUrl . $endpoint, $query);

        if ($response->status() === 404) {
            return null;
        }

        $response->throw(); // Lève une exception pour les 4xx/5xx

        return $response->json();
    }

    /**
     * Récupère tous les utilisateurs HumHub en dépilant toutes les pages.
     *
     * @return array<int, array> Tableau indexé par l'id HumHub
     */
    public function getAllUsersIndexed(): array
    {
        $page = 1;
        $indexed = [];

        do {
            $response = $this->get("/user", ["page" => $page, "limit" => 50]);
            $results = $response["results"] ?? [];

            foreach ($results as $user) {
                $indexed[$user["id"]] = $user;
            }

            $total = $response["total"] ?? 0;
            $fetched = $page * 50;
            $page++;
        } while ($fetched < $total);

        return $indexed;
    }
}
