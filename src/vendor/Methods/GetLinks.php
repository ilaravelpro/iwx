<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/11/21, 12:27 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


trait GetLinks
{
    public function getLinks($route, $params = [], $h = false) {
        return array_map(function ($pa) use($route, $params, $h){
            $text = $pa ? ($pa && !$h ? $pa / 100 : $pa)."hPa OR ". ($pa ? (int) (round(_pa_to_alt($pa, $h) / 1000) * 10) : $pa) . "FL" : "Surface";
            return [
                'text' => $text,
                'value' => route($route, array_merge($params, ['level' => $pa]))
            ];
        }, iwx('values.level.' . ($h ? 'hecto_pascals' : 'pascals')));
    }
}
