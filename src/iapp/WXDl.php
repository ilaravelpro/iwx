<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 8/22/20, 11:25 AM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\iApp;

use Illuminate\Database\Eloquent\Model;

class WXDl extends Model
{
    use \iLaravel\Core\iApp\Modals\Modal;

    protected $table = 'wx_dls';

    protected $guarded = [];

    protected $casts = [
        'dl_at' => 'timestamp',
        'ref_at' => 'timestamp',
        'valid_at' => 'timestamp',
    ];

    protected static function boot()
    {
        parent::boot();
        parent::deleted(function (self $event) {
            self::resetRecordsId();
            if (file_exists($event->storage)) {
                unlink($event->storage);
            }
        });
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

    public static function findByUrl($url)
    {
        return static::where('url', $url)->first();
    }

    public static function findByStorage($url)
    {
        return static::where('storage', $url)->first();
    }
}
