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

class Job
{
    public $disk;
    public $model_job;
    public $model_dl;
    public $server;
    use GetPath;

    public function __construct($options = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(60000);
        $disk = isset($options['disk']) ? $options['disk'] : 'wx.gfs';
        $this->disk = config('filesystems.disks.' . $disk, config('filesystems.disks.public'));
        $this->model_dl = imodal('WXGFSDl');
        $this->model_job = imodal('WXGFSJob');
        $this->server = 'https://nomads.ncep.noaa.gov/pub';
        //$this->server = 'https://ftpprd.ncep.noaa.gov';
    }
    public static function delete() {
        return (new self())->_delete();
    }

    public function _delete() {
        $model_job = imodal('WXGFSJob');
        $jobs = $model_job::where('created_at', '<', Carbon::now()->subDays(4)->format('Y-m-d H:i:s'));
        $jobsd = $jobs->get()->groupBy('date_folder')->keys()->toArray();
        $Filesystem = new Filesystem;
        foreach ($jobsd as $job) {
            foreach (['dl', 'json'] as $item) {
                $job_dir = $this->getPath(join(DIRECTORY_SEPARATOR,  [$item, $job]));
                if (is_dir($job_dir))
                    $Filesystem->deleteDirectory($job_dir);
            }
        }
        $jobs->delete();
        $model_dl = imodal('WXGFSDl');
        $dls = $model_dl::where('created_at', '<', Carbon::now()->subDays(3)->format('Y-m-d H:i:s'))->get();
        foreach ($dls as $dl) $dl->delete();
        $model_log = imodal('WXGFSLog');
        $logs = $model_log::where('created_at', '<', Carbon::now()->subDays(3)->format('Y-m-d H:i:s'))->get();
        foreach ($logs as $log) $log->delete();
        return ['status' => true];
    }


    public static function run($datetime = null, $rohour = 0, $dl_db = null, $job_db = null, $degree = '0.25') {
        return (new self())->_run($datetime, $rohour, $dl_db, $job_db, $degree);
    }

    public function _run($datetime = null, $rohour = 0, $dl_db = null, $job_db = null, $degree = '0.25') {
        $datetime = $datetime ? : \Carbon\Carbon::now();
        $hour = $rohour ? \Carbon\Carbon::parse($datetime)->roundHour()->hour : \Carbon\Carbon::parse($datetime)->subHours(5)->roundHour()->hour;
        $r_hour = floor($hour / 6);
        $c_hour = $r_hour * 6;
        $hour = str_slice($c_hour, '00', strlen($c_hour));
        $base_folder = "gfs.".\Carbon\Carbon::parse($datetime)->setHour($c_hour)->format('Ymd/H');
        $files_folder = $this->model_job::getByDataFolderDegree($base_folder, $degree);
        $last_file = null;
        if ($files_folder->count()){
            $last_file = $files_folder->sortBy('hour')->last();
            if ($last_file->hour > 31)
                return false;
            $aft_hour = file_exists($last_file->storage) ? $last_file->hour + 1 : $last_file->hour;
        }else{
            $aft_hour = (str_replace('-', '', $c_hour -  \Carbon\Carbon::parse($datetime)->addHour()->format('H')) + $rohour);
        }
        if (in_array($degree, ['1.00', 1.00]))
            $aft_hour = ceil($aft_hour / 3) * 3;
        $file_name = "gfs.t{$hour}z.pgrb2.".str_replace('.', 'p', $degree).".f".str_slice($aft_hour,'000', strlen($aft_hour));
        $base_name = $base_folder."/atmos/". $file_name;
        $file_hour = $this->getPath(join(DIRECTORY_SEPARATOR, ['dl', $base_name]));
        $dates = [
            'dl_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'ref_at' => \Carbon\Carbon::parse($datetime)->setHour($c_hour)->setMinute(0)->setSecond(0)->format('Y-m-d H:i:s'),
            'valid_at' => \Carbon\Carbon::parse($datetime)->setHour($c_hour)->addHours($aft_hour)->setMinute(0)->setSecond(0)->format('Y-m-d H:i:s'),
            'degree' => $degree
        ];
        $url = $this->server . '/data/nccf/com/gfs/prod/' . $base_name;
        if (file_exists($file_hour)){
            $dl_db = $this->model_dl::create(array_merge([
                'url' => $url,
                'storage' => $file_hour,
            ], $dates));
        }else {
            $job_db = $last_file && !file_exists($last_file->storage) ? $last_file : $this->model_job::create([
                'storage' => $file_hour,
                'date_folder' => $base_folder,
                'file_name' => $file_name,
                'hour' => $aft_hour,
                'degree' => $degree
            ]);
            if (!file_exists($this->getPath(join(DIRECTORY_SEPARATOR, ['dl', $base_folder."/atmos"]))))
                mkdir($this->getPath(join(DIRECTORY_SEPARATOR,  ['dl', $base_folder."/atmos"])), 0775, true);
            $dl_db = null;
            if ($dl_db = $this->model_dl::findByUrl($url)) {
                $file_hour = $dl_db->storage;
            } else
                if (HttpRequest::download($url, $file_hour)){
                    $dl_db = $this->model_dl::create(array_merge([
                        'url' => $url,
                        'storage' => $file_hour,
                    ], $dates));
                }else{
                    $file = $this->_run(\Carbon\Carbon::parse($datetime)->roundHour()->subHours(6), $aft_hour + 6, $dl_db, $job_db, $degree);
                    $file_hour = $file['file'];
                    $dl_db = $file['dl_db'];
                    $job_db = $file['job_db'];
                }
        }
        return ['file' => $file_hour, 'dl_db' => $dl_db, 'job_db' => $job_db];
    }
}
