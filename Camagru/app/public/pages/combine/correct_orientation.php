<?php
ini_set('memory_limit', '256M');

function correctImageOrientation($image, $decodedData)
{
    if (function_exists('exif_read_data')) {
        // Write decoded data to a temporary file
        $tmpFile = tempnam(sys_get_temp_dir(), 'exif_');

        $exif = @exif_read_data($tmpFile);
        // Remove the temp file
        unlink($tmpFile);
        if (!empty($exif['Orientation'])) {
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
            error_log("No orientation data found in EXIF\n", 3, '/tmp/exif_debug.log');
        }
    } else {
        error_log("EXIF data not found or could not be read\n", 3, '/tmp/exif_debug.log');
    }
    return $image;
}
