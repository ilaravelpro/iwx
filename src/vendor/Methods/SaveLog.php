<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:39 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;

trait SaveLog
{
    public function saveLog($dl_db, array $parser, &$level, $degree = 0.25)
    {
        $levels = iwx('values.level.pascals');
        $index = getClosestKey($level, $levels);
        $level = $levels[$index];
        $count = false;
        $content = null;
        while (!$count) {
            $grib2 = \iAmirNet\Grib2PHP\JsonParser::convert(...array_merge($parser, [$level]));
            if (file_exists($grib2->out)) {
                $content = json_decode(file_get_contents($grib2->out), true);
                if (!$content)
                    unlink($grib2->out);
                $count = !$content ? 0: count($content);
            }else{
                $count = 0;
            }
            if (!$count) {
                $index++;
                if ($index == count($levels))
                    break;
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
            'src' => $dl_db->src,
        ];
        if ($log = $this->model_log::findByOut($grib2->out))
            $log->update($data);
        else
            $log = $this->model_log::create($data);
        return [$log, $content];
    }
}
