<?php

namespace App\Enum;

enum MessageStateEnum
{

    public const NON_LU = "Non lu";
    public const EN_ATTENTE = "En attente";
    public const A_SUIVRE = "À suivre";
    public const URGENT = "Urgent";
    public const EN_COURS_DE_TRAITEMENT = "En cours de traitement";
    public const ANNULEE = "Message annulée";
    public const VALIDEE = "Message traité";

    public static function getValues(): array
    {
        return [
            self::NON_LU,
            self::EN_ATTENTE,
            self::A_SUIVRE,
            self::URGENT,
            self::EN_COURS_DE_TRAITEMENT,
            self::ANNULEE,
            self::VALIDEE,
        ];
    }

    public static function getColorClasses(): array
    {
        return [
            self::NON_LU => 'info',
            self::EN_ATTENTE => 'primary',
            self::A_SUIVRE => 'secondary',
            self::URGENT => 'danger',
            self::EN_COURS_DE_TRAITEMENT => 'warning',
            self::ANNULEE => 'danger',
            self::VALIDEE => 'success',
        ];
    }

}
