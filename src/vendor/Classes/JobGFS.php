<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/8/21, 9:27 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Classes;

class JobGFS extends JobParent
{
    public $server = 'https://data_ncep_noaa.iamir.net';
    //public $server = 'https://ftpprd.ncep.noaa.gov';
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
        $max_hour = 36;
        $last_file = null;
        if ($files_folder->count()){
            $last_file = $files_folder->sortBy('hour')->last();
            if ($last_file->hour > $max_hour)
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
        $type = iwx("sources.$this->src.type", "direct");
        if ($type == "filter") {
            $type_options = iwx("sources.$this->src.options.$type", []);
            $degree_options = isset($type_options["degrees"][$degree]) && is_array($type_options["degrees"][$degree]) ? array_merge($type_options["degrees"]["global"], $type_options["degrees"][$degree]) : $type_options["degrees"]["global"];
            $filter_url = isset($type_options['base_url']) ? $type_options['base_url'] : "https://nomads.ncep.noaa.gov/cgi-bin";
            $degree_file_render = isset($degree_options["file"]) ? $degree_options["file"] : "filter_gfs_0p25_1hr.pl";
            $filter_url = join(DIRECTORY_SEPARATOR, [$filter_url, $degree_file_render]);
            $query = [];
            $query["file"] = $file_name;
            foreach (isset($degree_options['vars']) ? $degree_options['vars'] : [] as $var) $query[$var] = "on";
            foreach (isset($degree_options['items']) ? $degree_options['items'] : [] as $var_index => $var_value) $query[$var_index] = $var_value;
            $query["dir"] = "/$storage_folder";
            $url = "$filter_url?".http_build_query($query);
        }
        return $this->_after_run($datetime, $rohour, $dl_db, $job_db, $degree, $hour, $r_hour, $c_hour, $base_folder, $storage_folder, $files_folder, $last_file, $aft_hour, $file_name, $base_name, $url, $file_hour);
    }
}
