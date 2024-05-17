<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/8/21, 9:27 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Classes;

use Carbon\Carbon;
use iLaravel\Core\Vendor\HttpRequest;
use iLaravel\iWX\Vendor\Methods\GetPath;
use Illuminate\Filesystem\Filesystem;

class JobECMWF extends JobParent
{
    public $server = 'https://data_ecmwf.iamir.net/forecasts';
    //public $server = 'https://data.ecmwf.int/forecasts';
    public $src = 'ecmwf';

    public function _run($datetime = null, $rohour = 0, $dl_db = null, $job_db = null, $degree = '0.4') {
        if (!$degree) $degree = '0.4';
        $datetime = $datetime ? : ($rohour ? \Carbon\Carbon::now() : \Carbon\Carbon::now()->subHours(10));
        $hour = \Carbon\Carbon::parse($datetime)->roundHour()->hour;
        $r_hour = floor($hour / 6);
        $c_hour = $r_hour * 6;
        $hour = str_slice($c_hour, '00', strlen($c_hour));
        $type = $c_hour == 6 || $c_hour == 18 ? 'scda' : 'oper';
        $base_folder = \Carbon\Carbon::parse($datetime)->setHour($c_hour)->format('Ymd/H'). 'z';
        $base_file_name = \Carbon\Carbon::parse($datetime)->setHour($c_hour)->format('YmdH0000');
        $storage_folder = "$base_folder/ifs/0p4-beta/$type";
        $files_folder = $this->model_job::getByDataFolderDegree($storage_folder, $degree)->where('src', $this->src);
        $last_file = null;
        if ($files_folder->count()){
            $last_file = $files_folder->sortBy('hour')->last();
            if ($last_file->hour > 36)
                return false;
            $aft_hour = file_exists($last_file->storage) ? $last_file->hour + 1 : $last_file->hour;
        }else{
            $aft_hour = (str_replace('-', '', $c_hour -  \Carbon\Carbon::parse($datetime)->addHour()->format('H')) + $rohour);
        }
        $aft_hour = ceil($aft_hour / 3) * 3;
        $file_name = "$base_file_name-{$aft_hour}h-$type-fc.grib2";
        $base_name = "$storage_folder/$file_name";
        $url = join('/', [$this->server, $base_name]);
        $file_hour = $this->getPath(join(DIRECTORY_SEPARATOR, ['dl', $base_name]));
        return $this->_after_run($datetime, $rohour, $dl_db, $job_db, $degree, $hour, $r_hour, $c_hour, $base_folder, $storage_folder, $files_folder, $last_file, $aft_hour, $file_name, $base_name, $url, $file_hour);
    }
}
