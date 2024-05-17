<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/2/20, 7:32 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

return [
    'routes' => [
        'api' => [
            'status' => true
        ]
    ],
    'database' => [
        'migrations' => [
            'include' => true
        ],
    ],
    'sources' => [
        'gfs' => [
            'title' => 'American',
            'name' => 'gfs',
            'server' => 'https://ftpprd.ncep.noaa.gov',
            'type' => 'filter',
            'options' => [
                "filter" => [
                    "base_url" => "https://nomads.ncep.noaa.gov/cgi-bin",
                    "degrees" => [
                        "global" => [
                            "vars" => ["all_lev", "var_GUST", "var_TMP", "var_UGRD", "var_VGRD"],
                            "items" => ["leftlon" => 0, "rightlon" => 360, "toplat" => 90, "bottomlat" => -90],
                        ],
                        "0.25" => [
                            "file" => "filter_gfs_0p25_1hr.pl"
                        ],
                        "1.00" => [
                            "file" => "filter_gfs_1p00.pl"
                        ],
                    ]
                ],
            ],
            'models' => [
                'job' => \iLaravel\iWX\Vendor\Classes\JobGFS::class
            ]
        ],
        'ecmwf' => [
            'title' => 'European',
            'name' => 'ecmwf',
            'server' => 'https://data.ecmwf.int/forecasts',
            'models' => [
                'job' => \iLaravel\iWX\Vendor\Classes\JobECMWF::class
            ]
        ],
    ],
    'units' => [
        'level' => 'p',
        'tmp' => 'k',
        'wind' => 'm/s',
    ],
    'values' => [
        'level' => [
            'pascals' => [
                0,40,100,200,300,400,500,600,700,800,900,1000,2000,3000,4000,5000,6000,7000,8000,9000,10000,15000,20000,
                25000,30000,35000,40000,45000,50000,55000,60000,65000,70000,75000,80000,85000,90000,95000,100000
            ],
            'hecto_pascals' => [
                0,40,50,60,70,80,90,100,150,200,
                250,300,350,400,450,500,550,600,650,700,750,800,850,900,950,1000
            ]
        ]
    ],
    'parsers' => [
        'wind' => [
            'params' => [
                'category' => 2,
                'parameter' => "wind",
                'surface' => null
            ]
        ],
        'tmp' => [
            'params' => [
                'category' => 0,
                'parameter' => 0,
                'surface' => null
            ]
        ],
    ]
];
?>
