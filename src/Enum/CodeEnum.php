<?php

namespace App\Enum;

enum CodeEnum
{
    /**
     * Liste des abbréviations des codes. C'est ici que tu les rajoutes si t'en as d'autres.
     * key => value
     */
//    private array $codes = [
//        'CC' => 'code civil',
//        'CPC' => 'code de procédure civile',
//        'CCom' => 'code de commerce',
//        'CPP' => 'code de procédure pénale',
//        'CT' => 'code du travail',
//        'CP' => 'code pénal',
//        'CSI' => 'code de la sécurité intérieure',
//    ];

    public const CC = "code civil";
    public const CPC = "code de procédure civile";
    public const CCom = "code de commerce";
    public const CPP = "code de procédure pénale";
    public const CT = "code du travail";
    public const CP = "code pénal";
    public const CSI = "code de la sécurité intérieure";

    public function getCodes(): array {
        return [
            "CC" => self::CC,
            "CPC" => self::CPC,
            "CCom" => self::CCom,
            "CPP" => self::CPP,
            "CT" => self::CT,
            "CSI" => self::CSI,
            "CP" => self::CP,
        ];
    }

}
