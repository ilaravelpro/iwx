<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:38 PM
 * Copyright (c) 2021. Powered by iamir.net
 */
namespace iLaravel\iWX\Vendor\Methods;

trait GetPath
{
    public function getPath($path = null) {
        $paths = [$this->disk['root']];
        if ($this->disk['driver'] == 'local'){
            $paths[] = 'wx';
            $paths[] = 'gfs';
        }
        if ($path) $paths[] = $path;
        return join(DIRECTORY_SEPARATOR, $paths);
    }
}
