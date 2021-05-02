<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/22/20, 11:25 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp;


class WXGFSJob extends \iLaravel\Core\iApp\Model
{
    protected $table = 'wx_gfs_jobs';

    protected static function boot()
    {
        parent::boot();
        parent::deleted(function (self $event) {
            self::resetRecordsId();
        });
    }

    public function dl() {
        return $this->belongsTo(imodal('WXGFSDl'), 'storage');
    }

    public static function getByDataFolderDegree($date_folder, $degree = '0.25')
    {
        return static::where('date_folder', $date_folder)->where('degree', $degree)->get();
    }

    public static function findByDataName($date_folder, $file_name)
    {
        return static::where('date_folder', $date_folder)->where('file_name', $file_name)->first();
    }
}
