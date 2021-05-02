<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 2/4/21, 11:03 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp\Http\Controllers\API\v1\WXGFS;

use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;

trait RequestData
{
    public function requestData(Request $request, $action, &$data)
    {
        if (in_array($action, ['single'])){
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;
        }
        if (in_array($action, ['single', 'range']) && isset($request->level)){
            $data['level'] = $request->level;
        }
        if (in_array($action, ['section']) && isset($request->section)){
            $data['section'] = $request->section;
        }
    }
}
