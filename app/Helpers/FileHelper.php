<?php

namespace App\Helpers;

class FileHelper
{
    public static function get_folder_path($folder_path)
    {
        if (env('APP_ENV') === 'local') {
            return str_replace('/', '\\', $folder_path);;
        } else {
            return str_replace('\\', '/', $folder_path);
        }
    }
}
