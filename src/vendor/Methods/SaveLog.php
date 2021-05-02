<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:39 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;

trait SaveLog
{
    public function saveLog($dl_db, array $parser, &$level, $degree = 0.25) {
        $levels = iwx('values.level.pascals');
        $index = getClosestKey($level, iwx('values.level.pascals'));
        $level = $levels[$index];
        $count = false;
        $content = null;
        while (!$count) {
            $grib2 = \iAmirNet\Grib2PHP\Parser::convert(...array_merge($parser, [$level]));
            $content = json_decode(file_get_contents($grib2->out), true);
            $count = count($content);
            if (!$count){
                $index++;
                $level = $levels[$index];
            }
        }
        $data = [
            'storage_in' => $grib2->in,
            'storage_out' => $grib2->out,
            'degree' => $degree,
            'dl_at' => $dl_db->dl_at,
            'ref_at' => $dl_db->ref_at,
            'valid_at' => $dl_db->valid_at,
        ];
        if ($log = $this->model_log::findByOut($grib2->out))
            $log->update($data);
        else
            $log = $this->model_log::create($data);
        return [$log, $content];
    }
}
