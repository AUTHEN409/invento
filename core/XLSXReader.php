<?php

namespace core;

use ZipArchive;
use SimpleXMLElement;

class XLSXReader {
    public static function parse($filePath) {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return false;
        }

        // 1. Read Shared Strings
        $sharedStrings = [];
        $ssData = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssData !== false) {
            $xml = simplexml_load_string($ssData);
            if ($xml) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $str = '';
                        foreach ($val->r as $r) {
                            $str .= (string)$r->t;
                        }
                        $sharedStrings[] = $str;
                    }
                }
            }
        }

        // 2. Read Sheet 1
        $sheetData = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetData === false) {
            $zip->close();
            return false;
        }

        $xml = simplexml_load_string($sheetData);
        $rows = [];

        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $cell) {
                $v = (string)$cell->v;
                if (isset($cell['t']) && (string)$cell['t'] === 's') {
                    $v = $sharedStrings[(int)$v] ?? '';
                }
                $rowData[] = $v;
            }
            if (!empty(array_filter($rowData))) {
                $rows[] = $rowData;
            }
        }

        $zip->close();
        return $rows;
    }
}
