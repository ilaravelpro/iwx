<?php

namespace iLaravel\iWX\Vendor\Classes;

use Carbon\Carbon;
use iLaravel\Core\Vendor\HttpRequest;
use iLaravel\iWX\Vendor\Methods\GetPath;
use Illuminate\Filesystem\Filesystem;

class JobParent
{
    public $disk;
    public $model_job;
    public $model_dl;
    public $server = 'https://ftpprd.ncep.noaa.gov';
    public $src = 'gfs';
    public $name;
    use GetPath;

    public function __construct($options = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(60000);
        $disk = isset($options['disk']) ? $options['disk'] : ('wx.'. $this->src);
        if (env('WX_GFS_LOCAL')) $disk = 'public';
        $this->disk = config('filesystems.disks.' . $disk, config('filesystems.disks.public'));
        $this->model_dl = imodal('WXDl');
        $this->model_job = imodal('WXJob');;
        $this->server = iwx("sources.$this->src.server", $this->server);
        $this->src = iwx("sources.$this->src.name", $this->src);
    }

    public static function delete() {
        return (new self())->_delete();
    }

    public function _delete() {
        $model_job = imodal('WXJob');
        $jobs = $model_job::where('src', $this->src)->where('created_at', '<', Carbon::now()->subDays(4)->format('Y-m-d H:i:s'));
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
        $model_dl = imodal('WXDl');
        $dls = $model_dl::where('src', $this->src)->where('created_at', '<', Carbon::now()->subDays(3)->format('Y-m-d H:i:s'))->get();
        foreach ($dls as $dl) $dl->delete();
        $model_log = imodal('WXLog');
        $logs = $model_log::where('src', $this->src)->where('created_at', '<', Carbon::now()->subDays(3)->format('Y-m-d H:i:s'))->get();
        foreach ($logs as $log) $log->delete();
        return ['status' => true];
    }

    public static function run($datetime = null, $rohour = 0, $dl_db = null, $job_db = null, $degree = null) {
        return (new static())->_run($datetime, $rohour, $dl_db, $job_db, $degree);
    }

    public function _after_run($datetime, $rohour, $dl_db, $job_db, $degree, $hour, $r_hour, $c_hour, $base_folder, $storage_folder, $files_folder, $last_file, $aft_hour, $file_name, $base_name, $url, $file_hour) {
        $dates = [
            'dl_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
            'ref_at' => \Carbon\Carbon::parse($datetime)->setHour($c_hour)->setMinute(0)->setSecond(0)->format('Y-m-d H:i:s'),
            'valid_at' => \Carbon\Carbon::parse($datetime)->setHour($c_hour)->addHours($aft_hour)->setMinute(0)->setSecond(0)->format('Y-m-d H:i:s'),
            'degree' => $degree,
            'src' => $this->src
        ];
        if (file_exists($file_hour)){
            $dl_db = $this->model_dl::create(array_merge([
                'url' => $url,
                'storage' => $file_hour,
                'src' => $this->src
            ], $dates));
        }else {
            $job_db = $last_file && !file_exists($last_file->storage) ? $last_file : $this->model_job::create([
                'storage' => $file_hour,
                'date_folder' => $storage_folder,
                'file_name' => $file_name,
                'hour' => $aft_hour,
                'degree' => $degree,
                'src' => $this->src
            ]);
            if (!file_exists($this->getPath(join(DIRECTORY_SEPARATOR, ['dl', $storage_folder]))))
                mkdir($this->getPath(join(DIRECTORY_SEPARATOR,  ['dl', $storage_folder])), 0775, true);
            $dl_db = null;
            if ($dl_db = $this->model_dl::findByUrl($url)) {
                $file_hour = $dl_db->storage;
            } else
                $dl_db = $this->model_dl::create(array_merge([
                    'url' => $url,
                    'storage' => $file_hour,
                    'src' => $this->src
                ], $dates));
            HttpRequest::download($url, $file_hour);
//                if (HttpRequest::download($url, $file_hour)){
//                    $dl_db = $this->model_dl::create(array_merge([
//                        'url' => $url,
//                        'storage' => $file_hour,
//                        'src' => $this->src
//                    ], $dates));
//                }else{
//                    /*$file = $this->_run(\Carbon\Carbon::parse($datetime)->roundHour()->subHours(6), $aft_hour + 6, $dl_db, $job_db, $degree);
//                    if (!$file)
//                    return $file;
//                    $file_hour = $file['file'];
//                    $dl_db = $file['dl_db'];
//                    $job_db = $file['job_db'];*/
//                    $dl_db = $this->model_dl::create(array_merge([
//                        'url' => $url,
//                        'storage' => $file_hour,
//                        'src' => $this->src
//                    ], $dates));
//                }
        }
        return ['file' => $file_hour, 'dl_db' => $dl_db, 'job_db' => $job_db];
    }
}
