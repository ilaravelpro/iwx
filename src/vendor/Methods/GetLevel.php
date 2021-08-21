<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:43 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


use Carbon\Carbon;

trait GetLevel
{
    public function getLevelG2J($level, $h = false)
    {
        $levels = iwx('values.level.pascals');
        $index = getClosestKey($level, $levels);
        $level = $levels[$index];
        if ($h) $level /= 100;
        return $level;
    }

    public function getLevel($level)
    {
        if (!$level > 0) return $level;
        $levels = iwx('values.level.hecto_pascals');
        $index = getClosestKey($level, $levels);
        $level = $levels[$index];
        return $level;
    }
}
