<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:39 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


trait GetWX
{
    public function getWX(array $converts, int $index) {
        $weather = [];
        foreach ($converts as $key => $convert) {
            switch ($key) {
                case 'wind':
                    $value = _uv2ddff(_get_value($convert['content'][0], "data.$index"), _get_value($convert['content'][1], "data.$index"));
                    $value = array_map(function ($wind){ return round($wind, 2); }, $value);
                    break;
                default:
                    $value = round(_get_value($convert['content'][0], "data.$index"), 2);
                    break;
            }
            if (isset($convert['change']) && is_callable($convert['change'])){
                $weather[$key] = $convert['change']($value);
            }else
                $weather[$key] = $value;
        }
        return $weather;
    }
}
