<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:39 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


trait GetWX
{
    public function getWXG2J(array $converts, int $index) {
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

    public function getWX($convert, $single = true) {
        $weather = [];
        foreach ($convert['variables'] as $key => $variable) {
            $key = strtolower($variable['name']);
            if ($single && is_string($single)) {
                $levels = array_column($variable['items'], 'level');
                $levelIndex = array_search($single, $levels);
                $levelIndex = $levelIndex === false ? 0 : $levelIndex;
            }else
                $levelIndex = 0;
            switch (strtolower($variable['name'])) {
                case 'wind':
                    $weather[$key] = $variable;
                    foreach ($variable['items'] as $index => $item) {
                        if (!$single || $index == $levelIndex) {
                            $value = _uv2ddff($item['u'], $item['v']);
                            $value = array_map(function ($wind){ return round($wind, 2); }, $value);
                            $weather[$key]['items'][$index] = array_merge($item, $value);
                        }
                    }
                    break;
                default:
                    $weather[$key] = $variable;
                    break;
            }
            if ($single) {
                $weather[$key] = array_merge($weather[$key], $weather[$key]['items'][$levelIndex]);
                unset($weather[$key]['items']);
                unset($weather[$key]['name']);
                unset($weather[$key]['level']);
                if (count($weather[$key]) == 1)
                    $weather[$key] = $weather[$key]['value'];
            }
            if (isset($convert['changeCommand']) && is_callable($convert['changeCommand'])){
                $weather[$key] = $convert['changeCommand']($weather[$key]);
            }
        }
        return $weather;
    }
}
