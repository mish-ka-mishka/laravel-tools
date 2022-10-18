<?php

namespace Tools\Services;

class CsvService
{
    static function getCsvAsArray(string $url): array
    {
        $csv = self::getCsvLines($url);

        $result = [];

        $header = $csv[0];

        foreach ($header as $key => $_value) {
            $header[$key] = mb_convert_case($_value, MB_CASE_LOWER, 'utf-8');
        }

        unset($csv[0]);

        foreach ($csv as $line) {
            $newLine = [];

            foreach ($line as $key => $value) {
                $newLine[$header[$key]] = $value;
            }

            $result[] = $newLine;
        }

        return $result;
    }

    private static function getCsvLines(string $url): array
    {
        $content = explode("\r\n", file_get_contents($url));

        $csv = array_map('str_getcsv', $content);

        if (!is_array($csv)) {
            throw new \InvalidArgumentException('Url content could not be converted to array');
        }

        return $csv;
    }
}
