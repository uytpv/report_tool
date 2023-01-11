<?php

namespace App\Admin\Controllers;

use App\Helpers\DatabaseHelper;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class FileController extends Controller
{
    public function readFiles($folder, Request $request)
    {
        $folder_path = FileHelper::get_folder_path(storage_path('app/public/' . $folder . '/'));

        // Lấy danh sách các file trong thư mục
        $files = glob($folder_path . '*.csv');

        // Duyệt qua danh sách các file và đọc dữ liệu
        foreach ($files as $fileIndex => $file) {
            $csv = Reader::createFromPath($file, 'r');

            // Xác định số dòng
            $num_rows = $csv->count();

            // Lấy dữ liệu từ file .csv:
            $records = $csv->setHeaderOffset(0)->getRecords();

            // Lấy tên các trường từ dòng đầu tiên của file .csv:
            $headers = $csv->getHeader();

            // Xử lý các ký tự đặc biệt đúng chuẩn MySQL
            $headers = array_map(function ($header) {
                return preg_replace('/[^a-zA-Z0-9_]/', '', $header);
            }, $headers);

            // Tạo bảng trong database
            if (DatabaseHelper::createTable($folder, $headers)) {
                $number_of_chunk = 10000;
                $times = $num_rows / $number_of_chunk;
                $chunks = array_chunk(iterator_to_array($records), $times); // Chia mảng thành các mảng con, mỗi mảng con có 2 phần tử

                foreach ($chunks as $chunk) {
                    foreach ($chunk as $row) {
                        $values = array_values($row);
                        $data = array_combine($headers, $values);
                        DB::table($folder)->insert($data);
                    }
                    sleep(1);
                }

                // set_time_limit(0); // tăng thời gian xử lý lên 300 giây
                // // Insert data từ các dòng còn lại vào database
                // foreach ($records as $rowIndex => $row) {
                //     $values = array_values($row);
                //     $data = array_combine($headers, $values);
                //     DB::table($folder)->insert($data);
                //     if ($rowIndex == $num_rows - 1) {
                //         rename($file, $file . '.done');
                //     }
                //     if ($rowIndex == 20000) {
                //         sleep(2);
                //     }
                // }
                return back()->withSuccess('Thành công' . 'Import files thành công');
            } else {
                return back()->withError('Error', 'Có lỗi xảy ra');
            }
        }
    }
}
