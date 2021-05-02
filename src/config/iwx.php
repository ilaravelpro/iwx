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
        'temp' => 'k',
        'wind' => 'm/s',
    ],
    'values' => [
        'level' => [
            'pascals' => [
                0,40,100,200,300,400,500,600,700,800,900,1000,2000,3000,4000,5000,6000,7000,8000,9000,10000,15000,20000,
                25000,30000,35000,40000,45000,50000,55000,60000,65000,70000,75000,80000,85000,90000,95000,100000
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
        'temp' => [
            'params' => [
                'category' => 0,
                'parameter' => 0,
                'surface' => null
            ]
        ],
    ]
];
?>
