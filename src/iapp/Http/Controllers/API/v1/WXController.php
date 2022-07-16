<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 2/4/21, 11:37 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp\Http\Controllers\API\v1;

use iLaravel\Core\iApp\Http\Controllers\API\Controller;
use iLaravel\Core\iApp\Http\Requests\iLaravel as Request;
use iLaravel\iWX\Vendor\Weather;


class WXController extends Controller
{
    use WX\Rules,
        WX\RequestData;

    public $vendor = GFS::class;

    public function __construct(\Illuminate\Http\Request $request)
    {
        parent::__construct($request);
        $this->vendor = new Weather(in_array($request->src, ['gfs', 'ecmwf']) ? $request->src : 'gfs');
    }

    public function single(Request $request, $src, $longitude, $latitude, $level = 0)
    {
        return $this->vendor->single($longitude, $latitude, $request->level ? : $level);
    }

    public function range(Request $request, $src, $level = 0)
    {
        return $this->vendor->range($request->all(), $request->level ? : $level);
    }

    public function multi(Request $request, $src, $level = 0)
    {
        return $this->vendor->multi($request->coordinates, $request->level ? : $level);
    }

    public function section(Request $request, $src, $section,  $level = 0)
    {
        return $this->vendor->section($section, $request->all(), $request->level ? : $level);
    }
}
