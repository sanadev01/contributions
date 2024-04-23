<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ActivityLogExportController extends Controller
{
    public function exportActivityLogSQL()
    {
        $activityLogs = DB::table('activity_log')->where('created_at', '>=', '2024-03-15')->get();

        $sqlContent = $this->generateSQLContent($activityLogs);

        $headers = [
            'Content-Type' => 'text/sql',
            'Content-Disposition' => 'attachment; filename="activity_log.sql"',
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

            $sql .= "INSERT INTO activity_log VALUES ($values);\n";
        }

        return $sql;
    }
}


