<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 2/8/21, 4:12 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor;

class GFS
{
    use Methods\Construct,
        Methods\SaveLog,
        Methods\GetPath,
        Methods\GetNowFile,
        Methods\GetWX,
        Methods\GetLinks,
        Methods\GetConverts,
        Methods\GetLevel;

    public $model_dl;
    public $model_log;

    public function load($datetime = null, &$level = 0, $degree = 0.25)
    {
        if (!$file = $this->getNowFile($datetime, $degree)) return false;
        $converts = $this->getConverts($file, $level, $degree);
        return [$file, $converts];
    }

    public function single($longitude, $latitude, $level = 0, $datetime = null, $parsers = [])
    {
        return ['data' => array_merge($this->_single($longitude, $latitude, $level, $datetime, $parsers), [
            'links' => $this->getLinks('api.iwx.gfs.single', ["longitude" => $longitude, "latitude" => $latitude], !request('g2j'))
        ])];
    }

    public function commend($storage, $coordinates, $level, $variables = ['UGRD', 'VGRD', 'TMP'])
    {
        $levels = array_map(function ($level) {
            return $level > 0 ? $level . ' mb' : $level;
        }, array_unique(is_array($level) ? $level : [$level]));
        if (($s = array_search(0, $levels)) !== false) {
            unset($levels[$s]);
            $levels = array_merge([
                'surface',
                'planetary boundary layer',
            ], $levels);
        }
        return \iAmirNet\Grib2PHP\Command::run($storage, $coordinates, $levels , $variables);
    }

    public function _single($longitude, $latitude, &$level = 0, $datetime = null, $parsers = [])
    {
        $g2j = request('g2j');
        $start = round(microtime(true), 2);
        $memory = memory_get_usage();
        if ($g2j) {
            list($file, $converts) = $this->load($datetime, $level);
            $wx = $this->getWXG2J($converts, grib2_find_index($longitude, $latitude));
        }else {
            $levelC = $this->getLevel($level);
            $file = $this->getNowFile($datetime, 0.25);
            $convert = $this->commend($file->storage, [$longitude, $latitude], $levelC);
            $wx = $convert->status ? $this->getWX($convert->output, true) : $convert->message;
        }
        return [
            'fl' => $level ? (int)(round(_pa_to_alt($level * ($g2j ? 1 : 100)) / 1000) * 10) : "Surface",
            'hpa' => $level ? ($g2j ? $level / 100 : $level ): "Surface",
            'pa' => $level ? (!$g2j ? $level * 100 : $level ): "Surface",
            'dl_at' => $file->dl_at,
            'ref_at' => $file->ref_at,
            'valid_at' => $file->valid_at,
            'wx' => $wx,
            'memory' => (memory_get_usage() - $memory)  / 1024 / 1024,
            'time' => (round(microtime(true), 2) - $start),
            'longitude' => $longitude,
            'latitude' => $latitude,
        ];
    }

    public function multi(array $coordinates, $level = 0, $datetime = null, $parsers = [])
    {
        return ['data' => array_merge($this->_multi($coordinates, $level, $datetime, $parsers), [
            'links' => $this->getLinks('api.iwx.gfs.multi', ["coordinates" => $coordinates])
        ])];
    }

    public function _multi(array $coordinates, &$level = 0, $datetime = null, $parsers = [])
    {
        list($file, $converts) = $this->load($datetime, $level);
        return [
            'fl' => $level ? (int)(round(_pa_to_alt($level) / 1000) * 10) : "Surface",
            'hpa' => $level ? $level / 100 : "Surface",
            'pa' => $level ?: "Surface",
            'dl_at' => $file->dl_at,
            'ref_at' => $file->ref_at,
            'valid_at' => $file->valid_at,
            'coordinates' => array_map(function ($coordinate) use ($converts) {
                return [
                    'wx' => $this->getWXG2J($converts, grib2_find_index($coordinate['longitude'], $coordinate['latitude'])),
                    'longitude' => $coordinate['longitude'],
                    'latitude' => $coordinate['latitude'],
                ];
            }, $coordinates),
        ];
    }

    public function range($data = null, $level = 0, $datetime = null, $parsers = [])
    {
        unset($data['level']);
        return ['data' => array_merge($this->_range($data, $level, $datetime, $parsers), [
            'links' => $this->getLinks('api.iwx.gfs.range', ["coordinates" => $data])
        ])];
    }

    public function _range($data = null, &$level = 0, $datetime = null, $parsers = [])
    {
        list($file, $converts) = $this->load($datetime, $level);
        $data = $data ?: request()->all();
        $coords['min']['lon'] = _get_value($data, 'minLon', 0);
        $coords['min']['lat'] = _get_value($data, 'minLat', 90);
        $coords['max']['lon'] = _get_value($data, 'maxLon', 359.75003);
        $coords['max']['lat'] = _get_value($data, 'maxLat', -90);
        foreach ($coords as $i => $coord) {
            $coords[$i]['index'] = grib2_find_index($coord['lon'], $coord['lat']);
        }
        $min = $coords['max']['index'] > $coords['min']['index'] ? $coords['min']['index'] : $coords['max']['index'];
        $max = $coords['max']['index'] > $coords['min']['index'] ? $coords['max']['index'] : $coords['min']['index'];
        $length = $max - $min;
        $weather = [];
        foreach ($converts as $key => $convert) {
            switch ($key) {
                case 'wind':
                    $value = [
                        'u' => array_slice(_get_value($convert['content'][0], "data"), $min, $length),
                        'v' => array_slice(_get_value($convert['content'][1], "data"), $min, $length),
                    ];
                    break;
                default:
                    $value = array_slice(_get_value($convert['content'][0], "data"), $min, $length);
                    break;
            }
            if (isset($convert['changeRange']) && is_callable($convert['changeRange'])) {
                $weather[$key] = $convert['changeRange']($value);
            } else
                $weather[$key] = $value;
        }
        unset($data['level']);
        return [
            'fl' => $level ? (int)(round(_pa_to_alt($level) / 1000) * 10) : "Surface",
            'hpa' => $level ? $level / 100 : "Surface",
            'pa' => $level ?: "Surface",
            'dl_at' => $file->dl_at,
            'ref_at' => $file->ref_at,
            'valid_at' => $file->valid_at,
            'coordinates' => $coords,
            'wx' => $weather,
        ];
    }

    public function section($section, $data = null, $level = 0, $datetime = null, $parsers = [])
    {
        unset($data['level']);
        return ['data' => array_merge($this->_section($section, $data, $level, $datetime, $parsers), [
            'links' => $this->getLinks('api.iwx.gfs.section', ['section' => $section])
        ])];
    }

    public function _section($section, $data = null, &$level = 0, $datetime = null, $parsers = [])
    {
        header('Access-Control-Allow-Origin: *');
        list($file, $converts) = $this->load($datetime, $level, _get_value($data, 'degree', 0.25));
        $data = $data ?: request()->all();
        $weather = $level == 0 && $section == 'wind' ? array_filter($converts[$section]['content'], function ($value) {
            return $value['header']['surface1Type'] == 220;
        }) : $converts[$section]['content'];
        unset($data['level']);
        return [
            'fl' => $level ? (int)(round(_pa_to_alt($level) / 1000) * 10) : "Surface",
            'hpa' => $level ? $level / 100 : "Surface",
            'pa' => $level ?: "Surface",
            'dl_at' => $file->dl_at,
            'ref_at' => $file->ref_at,
            'valid_at' => $file->valid_at,
            'section' => $weather,
        ];
    }
}
