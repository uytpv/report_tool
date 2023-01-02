<?php

namespace App\Admin\Controllers;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function readFiles($folder, Request $request)
    {

        $folder_path = FileHelper::get_folder_path(storage_path('app/public/' . $folder . '/'));

        // Lấy danh sách các file trong thư mục
        $files = glob($folder_path . '*.csv');

        // Duyệt qua danh sách các file và đọc dữ liệu
        foreach ($files as $file) {
            $handle = fopen($file, 'r');
            while (($data = fgetcsv($handle)) !== false) {
                // Lấy dữ liệu từ mỗi dòng và làm gì đó với nó (ví dụ: in ra màn hình, lưu vào cơ sở dữ liệu,...)
                print_r($data);
            }
            fclose($handle);
        }
    }
}
