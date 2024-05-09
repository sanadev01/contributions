<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;

class TableExportController extends Controller
{
    public function exportSQLTable($table)
    {
        if (!$this->tableExists($table)) {
            return new Response('Table not found', 404);
        }

        set_time_limit(180);

        $tableData = DB::table($table)->get();

        $sqlContent = $this->generateSQLContent($table, $tableData);

        $headers = [
            'Content-Type' => 'text/sql',
            'Content-Disposition' => "attachment; filename=\"$table.sql\"",
        ];

        return response()->stream(function () use ($sqlContent) {
            echo $sqlContent;
        }, 200, $headers);
    }

    private function tableExists($table)
    {
        try {
            DB::table($table)->first();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    private function generateSQLContent($table, $data)
    {
        $sql = '';

        foreach ($data as $row) {
            $values = implode(', ', array_map(function ($value) {
                return is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            }, (array) $row));

            $sql .= "INSERT INTO $table VALUES ($values);\n";
        }

        return $sql;
    }
}

