<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/22/20, 11:25 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp;

use iLaravel\Core\iApp\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\Filesystem;

class WXLog extends Model
{
    use \iLaravel\Core\iApp\Modals\Modal;

    protected $table = 'wx_logs';

    protected $guarded = [];

    protected $casts = [
        'dl_at' => 'timestamp',
        'ref_at' => 'timestamp',
        'valid_at' => 'timestamp',
    ];

    protected static function boot()
    {
        self::deleted(function (self $event) {
            if (file_exists($event->storage_out)) {
                @unlink($event->getAttribute('storage_out'));
            }
            self::resetRecordsId();
        });
        parent::boot();
    }
    
    public function getDlAtAttribute($value)
    {
        return format_datetime($value, $this->datetime, 'time');
    }

    public function getRefAtAttribute($value)
    {
        return format_datetime($value, $this->datetime, 'time');
    }

    public function getValidAtAttribute($value)
    {
        return format_datetime($value, $this->datetime, 'time');
    }

    public static function findByIn($in)
    {
        return static::where('storage_in', $in)->first();
    }

    public static function findByOut($out)
    {
        return static::where('storage_out', $out)->first();
    }
}
