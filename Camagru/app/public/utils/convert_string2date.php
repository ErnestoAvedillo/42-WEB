<?php
function convertstring2date($dateString, $inputFormat = 'd-m-Y', $outputFormat = 'Y-m-d')
{
    file_put_contents('/tmp/debug_convert_string2date.log', "Input date string: " . $dateString . "\n", FILE_APPEND);
    try {
        if (empty($dateString)) {
            file_put_contents('/tmp/debug_convert_string2date.log', "Empty date string\n", FILE_APPEND);
            return null; // Manejar fechas vacías
        }
        file_put_contents('/tmp/debug_convert_string2date.log', "Before parsed: " . $dateString . "\n", FILE_APPEND);
        $date = DateTime::createFromFormat($inputFormat, $dateString);
        file_put_contents('/tmp/debug_convert_string2date.log', "Parsed date: " . $date->format($outputFormat) . "\n", FILE_APPEND);
        if (!$date) {
            file_put_contents('/tmp/debug_convert_string2date.log', "Failed to parse date\n", FILE_APPEND);
            return null;
        }
        file_put_contents('/tmp/debug_convert_string2date.log', "returned: " . $date->format($outputFormat) . "\n", FILE_APPEND);
        return $date->format($outputFormat);
    } catch (Exception $e) {
        file_put_contents('/tmp/debug_convert_string2date.log', "Error parsing date: " . $e->getMessage() . "\n", FILE_APPEND);
        return null;
    }
}
