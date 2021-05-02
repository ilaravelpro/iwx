<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 9:08 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp\Http\Controllers\API\v1\WXGFS;

use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

trait Rules
{
    public function rules(Request $request, $action)
    {
        $rules = [];
        switch ($action) {
            case 'single':
                $rules = [
                    'latitude' => "required|latitude",
                    'longitude' => "required|longitude",
                    'level' => 'nullable|numeric|min:0|max:100000'
                ];
                break;
            case 'multi':
                $rules = [
                    'coordinates.*.latitude' => "required|latitude",
                    'coordinates.*.longitude' => "required|longitude",
                    'level' => 'nullable|numeric|min:0|max:100000'
                ];
                break;
            case 'range':
                $rules = [
                    'minLat' => "nullable|latitude",
                    'minLon' => "nullable|longitude",
                    'maxLat' => "nullable|latitude",
                    'maxLon' => "nullable|longitude",
                    'level' => 'nullable|numeric|min:0|max:100000'
                ];
                break;
            case 'section':
                $rules = [
                    'minLat' => "nullable|latitude",
                    'minLon' => "nullable|longitude",
                    'maxLat' => "nullable|latitude",
                    'maxLon' => "nullable|longitude",
                    'section' => "required|in:wind,temp",
                    'degree' => "nullable|double",
                    'level' => 'nullable|numeric|min:0|max:100000'
                ];
                break;
        }
        return $rules;
    }
}
