<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 10/29/20, 6:35 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

function iwx_path($path = null)
{
    $path = trim($path, '/');
    return __DIR__ . ($path ? "/$path" : '');
}

function iwx($key = null, $default = null)
{
    return iconfig('wx' . ($key ? ".$key" : ''), $default);
}
