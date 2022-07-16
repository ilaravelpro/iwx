<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:43 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


trait Construct
{
    public $disk;
    public $src;
    public function __construct($src = 'gfs', $options = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(60000);
        $disk = isset($options['disk']) ? $options['disk'] : ('wx.'. $src);
        $this->disk = env('WX_GFS_LOCAL') ? config('filesystems.disks.public') : config('filesystems.disks.' . $disk, config('filesystems.disks.public'));
        $this->model_dl = imodal('WXDl');
        $this->model_log = imodal('WXLog');
        $this->src = $src ? : 'gfs';
    }
}
