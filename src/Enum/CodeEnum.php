<?php

namespace App\Enum;

enum CodeEnum: string
{
    /**
     * Liste des abbréviations des codes. C'est ici que tu les rajoutes si t'en as d'autres.
     * key => value
     */

    case CC = "code civil";
    case CPC = "code de procédure civile";
    case CCom = "code de commerce";
    case CPP = "code de procédure pénale";
    case CT = "code du travail";
    case CP = "code pénal";
    case CSI = "code de la sécurité intérieure";

    public static function getCodes(): array
    {
        return array_combine(
            array_column(self::cases(), 'name'),
            array_column(self::cases(), 'value')
        );
    }

}
