<?php

namespace Frogg;

use League\Csv\Writer;

/**
 * @deprecated Just set a header using the framework
 */
class Csv
{

    /**
     * @param        $list
     * @param string $filename
     * @param string $separator
     * @param string $enclosure
     *
     * @throws \TypeError
     */
    static function export($list, $filename = 'data', $separator = ',', $enclosure = '"')
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');

        $writer = Writer::createFromPath('php://output', 'w');
        $writer->insertAll($list); //using an array
    }

    static function addVal(&$line, $value)
    {
        $line[] = utf8_decode($value);
    }
}
