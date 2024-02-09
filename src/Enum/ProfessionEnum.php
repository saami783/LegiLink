<?php

namespace App\Enum;

/**
 * Cette classe permet de définir des constantes de profession utilisateur
 * qui seront affichés dans le crud controller User d'EasyAdmin pour les pages new et edit.
 */
enum ProfessionEnum
{

    public const TEACHER = 'teacher';
    public const STUDENT = 'student';
    public const OTHER = 'other';

    public static function getValues(): array
    {
        return [
            'Professeur' => self::TEACHER,
            'Eleve' => self::STUDENT,
            'Autre' => self::OTHER
        ];
    }
}