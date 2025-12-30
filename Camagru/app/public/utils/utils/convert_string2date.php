<?php
function convertstring2date($dateString, $inputFormat = 'd-m-Y', $outputFormat = 'Y-m-d')
{
    try {
        if (empty($dateString)) {
            return null; // Manejar fechas vacías
        }
        $date = DateTime::createFromFormat($inputFormat, $dateString);
        if (!$date) {
            return null;
        }
        return $date->format($outputFormat);
    } catch (Exception $e) {
        return null;
    }
}
