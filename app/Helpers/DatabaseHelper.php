<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    public static function createTable($table_name, $fields)
    {
        // Nếu bảng chưa tồn tại thì tạo bảng
        if (!DatabaseHelper::tableIsExists($table_name)) {
            $sql = 'CREATE TABLE ' . $table_name . ' (id INT(11) AUTO_INCREMENT PRIMARY KEY, ';

            foreach ($fields as $key => $field) {
                $sql .= '`' . $field . '` VARCHAR(255) NULL';

                if ($key == count($fields) - 1) { // Nếu là field cuối cùng thì ko thêm dấu , trong câu sql
                    $sql .= ')';
                } else {
                    $sql .= ', ';
                }
            }
           
            try {
                DB::statement($sql);
                return true;
            } catch (\Exception $e) {
                dd($e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    public static function  tableIsExists($table_name)
    {
        // Lấy tất cả các tên tables trong database
        $tables = DB::select('SHOW TABLES');
        $tir = array_column($tables, 'Tables_in_report');

        // Kiểm tra xem table có tồn tại hay không
        return in_array(strtolower($table_name), $tir) ? true : false;
    }
}


