<?php

declare(strict_types=1);

namespace App\InputFilter;

use App\Service\EncryptService;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\Validator\Db\RecordExists;

/** phpcs:disable */
class ApplicantInputFilter extends InputFilter
{
    /** @var AdapterInterface */
    private $dbAdapter;

    /** @var EncryptService */
    private $encryptService;

    public function __construct(
        AdapterInterface $dbAdapter,
        EncryptService $encryptService
    ) {
        $this->dbAdapter      = $dbAdapter;
        $this->encryptService = $encryptService;
    }

    public function init(string $taj = '')
    {
        $this->add([
            'name'        => 'firstname',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\StringLength([
                    'messages' => [
                        Validator\StringLength::TOO_SHORT => 'Legalább %min% karaktert kell tartalmaznia a mezőnek',
                        Validator\StringLength::TOO_LONG  => 'Kevesebb karaktert kell tartalmaznia a mezőnek mint: %max%',
                        Validator\StringLength::INVALID   => 'Hibás mező tipus. Csak szöveg fogadható el.',
                    ],
                    'min'      => 1,
                    'max'      => 255,
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'lastname',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\StringLength([
                    'messages' => [
                        Validator\StringLength::TOO_SHORT => 'Legalább %min% karaktert kell tartalmaznia a mezőnek',
                        Validator\StringLength::TOO_LONG  => 'Kevesebb karaktert kell tartalmaznia a mezőnek mint: %max%',
                        Validator\StringLength::INVALID   => 'Hibás mező tipus. Csak szöveg fogadható el.',
                    ],
                    'min'      => 1,
                    'max'      => 255,
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'email',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\EmailAddress([
                    'messages' => [
                        Validator\EmailAddress::INVALID            => "Érvénytelen típus megadva. Szöveg adható meg.",
                        Validator\EmailAddress::INVALID_FORMAT     => "A bevitel nem érvényes e-mail cím. Használja az alapformátumot pl. email@kiszolgalo.hu",
                        Validator\EmailAddress::INVALID_HOSTNAME   => "'%hostname%' érvénytelen gazdagépnév",
                        Validator\EmailAddress::INVALID_MX_RECORD  => "'%hostname%' úgy tűnik, hogy az e-mail címhez nincs érvényes MX vagy A rekordja",
                        Validator\EmailAddress::INVALID_SEGMENT    => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
                        Validator\EmailAddress::DOT_ATOM           => "'%localPart%' can not be matched against dot-atom format",
                        Validator\EmailAddress::QUOTED_STRING      => "'%localPart%' nem illeszthető idézőjel a szövegbe",
                        Validator\EmailAddress::INVALID_LOCAL_PART => "'%localPart%' nem érvényes az e-mail cím helyi része",
                        Validator\EmailAddress::LENGTH_EXCEEDED    => "A szöveg meghaladja az engedélyezett hosszúságot",
                    ],
                ]),
                new Validator\StringLength([
                    'messages' => [
                        Validator\StringLength::TOO_SHORT => 'Legalább %min% karaktert kell tartalmaznia a mezőnek',
                        Validator\StringLength::TOO_LONG  => 'Kevesebb karaktert kell tartalmaznia a mezőnek mint: %max%',
                        Validator\StringLength::INVALID   => 'Hibás mező tipus. Csak szöveg fogadható el.',
                    ],
                    'min'      => 5,
                    'max'      => 255,
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'address',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\StringLength([
                    'messages' => [
                        Validator\StringLength::TOO_SHORT => 'Legalább %min% karaktert kell tartalmaznia a mezőnek',
                        Validator\StringLength::TOO_LONG  => 'Kevesebb karaktert kell tartalmaznia a mezőnek mint: %max%',
                        Validator\StringLength::INVALID   => 'Hibás mező tipus. Csak szöveg fogadható el.',
                    ],
                    'min'      => 2,
                    'max'      => 255,
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'taj',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\Regex([
                    'pattern' => '/^\d{3} \d{3} \d{3}$/',
                    'messages' => [
                        Validator\Regex::INVALID   => 'Érvénytelen TAJ-szám formátum',
                        Validator\Regex::NOT_MATCH => "Érvénytelen TAJ-szám formátum (000 000 000)",
                        Validator\Regex::ERROROUS  => "Belső hiba történt a (000 000 000) minta használata közben",
                    ]
                ]),
                new NoRecordExists([
                    'table'    => 'applicants',
                    'field'    => 'taj',
                    'adapter'  => $this->dbAdapter,
                    'messages' => [
                        NoRecordExists::ERROR_NO_RECORD_FOUND => 'Nem található ilyen TAJ-száma',
                        NoRecordExists::ERROR_RECORD_FOUND    => 'Ezzel a TAJ-számmal már történt regisztráció',
                    ]
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'phone',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\Regex([
                    'pattern' => '/^36 \d{2} \d{3} \d{4}$/',
                    'messages' => [
                        Validator\Regex::INVALID   => 'Érvénytelen telefonszám formátum',
                        Validator\Regex::NOT_MATCH => "Érvénytelen telefonszám formátum (+36 00 000 0000)",
                        Validator\Regex::ERROROUS  => "Belső hiba történt a (+36 00 000 0000) minta használata közben",
                    ]
                ]),
            ],
            'filters'     => [
                new Filter\StringTrim(),
                new Filter\StripTags(),
            ],
        ]);

        $this->add([
            'name'        => 'birthdayPlace',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\StringLength([
                    'messages' => [
                        Validator\StringLength::TOO_SHORT => 'Legalább %min% karaktert kell tartalmaznia a mezőnek',
                        Validator\StringLength::TOO_LONG  => 'Kevesebb karaktert kell tartalmaznia a mezőnek mint: %max%',
                        Validator\StringLength::INVALID   => 'Hibás mező tipus. Csak szöveg fogadható el.',
                    ],
                    'min'      => 2,
                    'max'      => 255,
                ]),
            ],
        ]);

        $this->add([
            'name'        => 'birthday',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\Date([
                    'message'   => [
                        // Validator\Date::INVALID      => 'Érvénytelen típus lett megadva. Szöveg, egész szám, tömb vagy DateTime várható',
                        Validator\Date::INVALID_DATE => 'Úgy tűnik, hogy nem érvényes a születésnap dátuma',
                        Validator\Date::FALSEFORMAT  => "A megadott dátum nem felel meg a kívánt formátumnak (éééé.hh.nn)",
                    ],
                    'format' => 'Y.m.d',
                    'strict' => true,
                ]),
            ],
        ]);

        $this->add([
            'name'        => 'place',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new RecordExists([
                    'table'    => 'places',
                    'field'    => 'id',
                    'adapter'  => $this->dbAdapter,
                    'messages' => [
                        RecordExists::ERROR_NO_RECORD_FOUND => 'Nem található ilyen helyszín',
                    ]
                ])
            ]
        ]);

        $this->add([
            'name'        => 'appointment',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező az időpont választás',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new RecordExists([
                    'table'    => 'appointments',
                    'field'    => 'id',
                    'adapter'  => $this->dbAdapter,
                    'messages' => [
                        RecordExists::ERROR_NO_RECORD_FOUND => 'Nem található ilyen időpont',
                    ]
                ])
            ]
        ]);

        $this->add([
            'name'        => 'privacy',
            'allow_empty' => false,
            'validators'  => [
                new Validator\NotEmpty([
                    'messages' => [
                        Validator\NotEmpty::IS_EMPTY => 'Kötelező a mező kitöltése',
                        Validator\NotEmpty::INVALID  => 'Hibás mező tipus',
                    ],
                ]),
                new Validator\Callback([
                    'messages' => [
                        Validator\Callback::INVALID_VALUE    => 'Csak elfogadás után tudjuk fogadni a regisztrációs űrlapot',
                        Validator\Callback::INVALID_CALLBACK => 'Ismeretlen hiba',
                    ],
                    'callback' => function ($value) {
                        return $value === true || $value === "true";
                    },
                ]),
            ],
            'filters'     => [
                new Filter\Boolean([
                    'casting' => false,
                ]),
            ],
        ]);
    }
}
/** phpcs:enable */
