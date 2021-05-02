<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 3/9/21, 8:39 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

namespace iLaravel\iWX\Vendor\Methods;


trait GetConverts
{
    public function getConverts($file, &$level, $degree = 0.25) {
        $in = $file->storage;
        $db = $file;
        $out = $this->getPath(join(DIRECTORY_SEPARATOR, ['json', trim(explode('dl', $in)[1], ' \\ \/')]));
        $converts = [];
        foreach (iwx('parsers', []) as $index => $parser) {
            list($parser['log'], $parser['content']) = $this->saveLog($db, array_merge([$in, $out], array_values($parser['params'])), $level, $degree);
            $parser['level'] = $level;
            $converts[$index] = $parser;
        }
        return $converts;
    }
}
