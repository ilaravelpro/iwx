<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:43 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


use Carbon\Carbon;

trait GetNowFile
{
    public function getNowFile($datetime = null, $degree = 0.25) {
        if ($this->src == 'ecmwf') $degree = 0.4;
        if (env('WX_GFS_LOCAL')) return $this->model_dl::all()->where('src', $this->src)->where('degree', $degree)->first();
        $datetime = $datetime ? Carbon::parse($datetime) : Carbon::now();
        $date_time = $datetime->addHour()->roundHour();
        if (in_array($degree, ['1.00', 1.00, '0.4', 0.4]))
            $date_time = $date_time->setHour(ceil($date_time->hour / 3) * 3);
        return $this->model_dl::where('src', $this->src)->where('valid_at', '=',$date_time->format('Y-m-d H:i:s'))->where('degree', $degree)->get()->last();
    }
}
