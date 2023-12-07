<?php

use \Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\VarDumper as Dumper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

function nap($seconds) {
    if (!$seconds) return;
    $progress = progress_bar($seconds);
    $i = 0;
    while ($i++ < $seconds) {
        $progress->advance();
        sleep(1);
    }
    print "\n";
}

function console_log() {
    if (app()->runningInConsole()) {
        print "\e[1;33m" . Carbon::now() . " : \e[0m";
        foreach (func_get_args() as $arg) {
            print_r($arg);
            print " ";
        }
        print "\n";
    } else {
        foreach (func_get_args() as $arg) {
            Log::info(print_r($arg, true));
        }
    }
}

function console_error() {
    print "\e[1;31m" . Carbon::now() . " : \e[41;97m ";
    foreach (func_get_args() as $arg) {
        print_r($arg);
        print " ";
    }
    print "\e[0m\n";
}

function progress_bar($maximum): ProgressBar {
    $output = new ConsoleOutput();
    $progress_bar = new ProgressBar($output, $maximum);
    $progress_bar->start();
    return $progress_bar;
}

function is_local(): bool {
    return config('app.env') === 'local';
}

function is_development(): bool {
    return config('app.env') === 'development';
}

function is_production(): bool {
    return config('app.env') === 'production';
}

function storage(): StorageHelper {
    return new StorageHelper();
}

function dump_log() {
    array_map(function ($x) {
        (new Dumper)->dump($x);
    }, func_get_args());
}

function to_array($value) {
    return is_array($value) ? $value : [$value];
}

function required_label($label): string {
    return __($label) . ' *';
}

function parse_empty($row, $index = null, $default = null) {
    if ($index && !isset($row[$index])) {
        return $default;
    }

    $value = $index ? $row[$index] : $row;
    if ($value == '--' || $value == '#N/A') {
        return $default;
    }

    return $value;
}

function parse_date($date, $format = null): ?Carbon {
    if (!$date || $date == '0000-00-00') return NULL;

    try {
        $date = $format ? Carbon::createFromFormat($format, $date) : Carbon::parse($date);
    } catch (\Exception $ex) {
        return NULL;
    }

    if ($date->toDateString() == '1970-01-01') {
        return NULL;
    }

    return $date;
}

function clean_sku($sku) {
    return str_replace("\\", "\\\\", html_entity_decode($sku, ENT_QUOTES | ENT_HTML5));
}

function get_class_name($object) {
    $class_name = is_object($object) ? get_class($object) : $object;

    if (preg_match("@\\\\([\w]+)$@", $class_name, $matches)) {
        $class_name = $matches[1];
    }

    return $class_name;
}

function log_info($message) {
    Log::info($message);
}

function log_error($message) {
    Log::error($message);
}

function is_menu_parent($menu) {
    return (isset($menu['parent']) && $menu['parent'] === 'true');
}

function raw_query($query) {
    $base_query = str_replace('%', '%%', $query->toSql());
    return vsprintf(str_replace(['?'], ['\'%s\''], $base_query), $query->getBindings());
}

function exec_insert_update($select_q, $columns, $to_table) {
    $updated_columns = array_filter(array_map(function ($column) {
        if (trim($column, '`') !== 'created_at') {
            return $column . ' = VALUES(' . $column . ')';
        }
    }, $columns));

    $query =
        'INSERT INTO ' . $to_table .
        ' (' . implode(', ', $columns) . ') ' .
        raw_query($select_q) .
        ' ON DUPLICATE KEY ' .
        ' UPDATE ' . implode(', ', $updated_columns);

    DB::transaction(function () use ($query) {
        DB::connection()->getPdo()->exec($query);
    }, 3);
}

function app_name($company_settings, $default = '') {
    if ($company_settings && $company_settings->company_name) {
        return $company_settings->company_name;
    }

    return config('app.name', 'RJMTaxExemption');
}

function app_logo($company_settings, $logo): ?string {
    $custom = $company_settings ? $company_settings->$logo : [];
    $custom_path = $custom['path'] ?? null;

    if ($custom_path) {
        return asset('storage/' . $custom_path);
    }

    if (in_array($logo, ['logo_light', 'logo_dark'])) {
        return null;
    }

    $ext = '.png';
    $path = 'images/logo/';

    if ($logo == 'favicon') {
        $ext = '.ico';
        $path = 'images/';
    }

    $logo = str_replace('_', '-', $logo);
    return asset($path . $logo . $ext);
}

function app_logo_wd($company_settings, $logo, $default) {
    $custom = $company_settings ? $company_settings->$logo : [];
    return ($custom['width'] ?? 0) ?: $default;
}

function app_logo_hg($company_settings, $logo, $default) {
    $custom = $company_settings ? $company_settings->$logo : [];
    return ($custom['height'] ?? 0) ?: $default;
}
