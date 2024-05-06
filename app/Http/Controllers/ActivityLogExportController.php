<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ActivityLogExportController extends Controller
{
    public function exportActivityLogSQL()
    {
        $activityLogs = DB::table('deposits')->get();

        $sqlContent = $this->generateSQLContent($activityLogs);

        $headers = [
            'Content-Type' => 'text/sql',
            'Content-Disposition' => 'attachment; filename="deposits.sql"',
        ];

        return response()->stream(function () use ($sqlContent) {
            echo $sqlContent;
        }, 200, $headers);
    }

    private function generateSQLContent($data)
    {
        $sql = '';

        foreach ($data as $row) {
            $values = implode(', ', array_map(function ($value) {
                return is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            }, (array) $row));

            $sql .= "INSERT INTO deposits VALUES ($values);\n";
        }

        return $sql;
    }
}


