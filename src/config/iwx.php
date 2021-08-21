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
