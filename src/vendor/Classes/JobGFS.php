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

class JobGFS extends JobParent
{
    public $server = 'https://ftpprd.ncep.noaa.gov';
    public $src = 'gfs';

    public function _run($datetime = null, $rohour = 0, $dl_db = null, $job_db = null, $degree = '0.25') {
        if (!$degree) $degree = '0.25';
        $datetime = $datetime ? : \Carbon\Carbon::now();
        $hour = $rohour ? \Carbon\Carbon::parse($datetime)->roundHour()->hour : \Carbon\Carbon::parse($datetime)->subHours(5)->roundHour()->hour;
        $r_hour = floor($hour / 6);
        $c_hour = $r_hour * 6;
        $hour = str_slice($c_hour, '00', strlen($c_hour));
        $base_folder = "gfs.".\Carbon\Carbon::parse($datetime)->setHour($c_hour)->format('Ymd/H');
        $storage_folder = $base_folder."/atmos";
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
        if (in_array($degree, ['1.00', 1.00]))
            $aft_hour = ceil($aft_hour / 3) * 3;
        $file_name = "gfs.t{$hour}z.pgrb2.".str_replace('.', 'p', $degree).".f".str_slice($aft_hour,'000', strlen($aft_hour));
        $base_name = $storage_folder."/". $file_name;
        $url = $this->server . '/data/nccf/com/gfs/prod/' . $base_name;
        $file_hour = $this->getPath(join(DIRECTORY_SEPARATOR, ['dl', $base_name]));
        return $this->_after_run($datetime, $rohour, $dl_db, $job_db, $degree, $hour, $r_hour, $c_hour, $base_folder, $storage_folder, $files_folder, $last_file, $aft_hour, $file_name, $base_name, $url, $file_hour);
    }
}
