<?php
ini_set('memory_limit', '256M');

function correctImageOrientation($image, $decodedData)
{
    if (function_exists('exif_read_data')) {
        // $stream = fopen('php://memory', 'r+');
        // fwrite($stream, $decodedData);
        // rewind($stream);
        // $exif = @exif_read_data($stream);
        // fclose($stream);

        // Write decoded data to a temporary file
        $tmpFile = tempnam(sys_get_temp_dir(), 'exif_');
        file_put_contents($tmpFile, $decodedData);

        $exif = @exif_read_data($tmpFile);
        // Remove the temp file
        unlink($tmpFile);
        if (!empty($exif['Orientation'])) {
            file_put_contents('/tmp/exif_debug.log', "Orientation: " . print_r($exif, true), FILE_APPEND);
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        } else {
            file_put_contents('/tmp/exif_debug.log', "No orientation data found in EXIF\n", FILE_APPEND);
        }
    } else {
        file_put_contents('/tmp/exif_debug.log', "EXIF data not found or could not be read\n", FILE_APPEND);
    }
    return $image;
}
