<?php

namespace App\Enum;

/**
 * Cette classe permet de définir des constantes de rôles utilisateurs
 * qui seront affichés dans le crud controller User d'EasyAdmin pour les pages new et edit.
 */
enum UserRoleEnum
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';


    public static function getValues(): array
    {
        return [
            'Utilisateur' => self::ROLE_USER,
            'Admin' => self::ROLE_ADMIN,
            'Super Admin' => self::ROLE_SUPER_ADMIN
        ];
    }
}