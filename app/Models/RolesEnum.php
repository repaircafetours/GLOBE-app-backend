<?php

namespace App\Models;

enum RolesEnum: int
{
    case Administrateur = 1;
    case Intendance = 2;
    case Operationnel = 3;
    case Reparateur = 4;

    public static function fromId(int $id): self
    {
        return match ($id) {
            1 => self::Administrateur,
            2 => self::Intendance,
            3 => self::Operationnel,
            4 => self::Reparateur,
            default => throw new \InvalidArgumentException(
                "ID de rôle invalide: {$id}",
            ),
        };
    }

    public static function byId(int $id): ?self
    {
        return match ($id) {
            1 => self::Administrateur,
            2 => self::Intendance,
            3 => self::Operationnel,
            4 => self::Reparateur,
            default => null,
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::Administrateur => "Administrateur",
            self::Intendance => "Intendance",
            self::Operationnel => "Opérationnel",
            self::Reparateur => "Réparateur",
        };
    }
}
