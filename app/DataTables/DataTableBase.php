<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\QueryDataTable;

class DataTableBase {

    /** @var User */
    protected $user;
    /** @var Request */
    protected $request;
    protected $columns = [];
    protected $table_id = '';
    protected $order_by = [[1, 'asc']];
    protected $row_id = null;
    protected $ajax_url = null;

    public function __construct(User $user, Request $request = null) {
        $this->user = $user;
        $this->request = $request;
        $this->setTableId(get_class($this));
        $this->ajax_url = $request->fullUrl();
        $this->setOrderBy();
    }

    public function getBaseQuery(): ?Builder {
        return null;
    }

    public function getColumnDef(): array {
        return [];
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function getTableId(): string {
        return $this->table_id;
    }

    public function setTableId($table_id) {
        $this->table_id = $table_id;
    }

    protected function setOrderBy() {
        if ($this->request->get('order')) {
            $this->order_by = $this->request->get('order');
        }
    }

    public function getOrderBy() {
        return $this->order_by;
    }

    public function table($attributes = []): string {
        $classes = 'table table-hover datatable w-100';
        if (isset($attributes['class'])) {
            $classes .= ' ' . $attributes['class'];
        }
        $table = "<table class='{$classes}' id='{$this->getTableId()}'>";

        $table .= "<thead><tr>";

        foreach ($this->getColumnDef() as $key => $value) {
            $table .= "<th scope='col' class='" . $key . "'>" . $value['title'] . "</th>";
        }

        $table .= "</tr></thead><tbody></tbody></table>";
        return $table;
    }

    public function getDTParameters($parameters = []) {
        return (array_merge([
            "ajax"        => $this->ajax_url,
            "pageLength"  => 1000,
            "aoColumns"   => array_values($this->getColumnDef()),
            "aaSorting"   => $this->getOrderBy(),
			"sDom" => "<'row dt-top-wrapper mb-2'>" .
				"<'#" . $this->getTableId() . ".row dt-wrapper '<'col-sm-12'tr>>" .
				"<'row pagination-wrapper mt-3'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'i><'col-sm-12 col-md-4'p>>",
		], $parameters));
    }

    public function scripts() {
        $dt_parameters = $this->getDTParameters();
        return sprintf('<script type="text/javascript">loadDataTable("%s", %s)</script>' . PHP_EOL, $this->getTableId(), json_encode($dt_parameters));
    }

    public function getData($return = false) {
        $base_query = $this->getBaseQuery();

        /** @var QueryDataTable $data_table */
        $data_table = Datatables::of($base_query);

        $column_def = $this->getColumnDef();

        $raw_columns = collect($column_def)->where('raw', true)->pluck('data')->toArray();

        foreach ($column_def as $column) {

            if (isset($column['content']) && is_callable($column['content'])) {
                $data_table->editColumn($column['data'], $column['content']);
                continue;
            }

            switch ($column['column_type'] ?? 'text') {
                case 'boolean':
                    $data_table->editColumn($column['data'], function ($row) use ($column) {
                        return $row->{$column['data']} ? __('True') : __('False');
                    });
                    break;

                case 'date';
                    $data_table->editColumn($column['data'], function ($row) use ($column) {
                        $format = $column['format'] ?? 'D. M d, Y h:i:s A';
                        return $row->{$column['data']} ? Carbon::parse($row->{$column['data']})->format($format) : '';
                    });
                    break;

                case 'text':
                default:
                    $data_table->editColumn($column['data'], function ($row) use ($column) {
                        return $row->{$column['data']};
                    });
                    break;
            }
        }

        if (!empty($raw_columns)) {
            $data_table->rawColumns($raw_columns);
        }

        if ($this->row_id) {
            $data_table->setRowId($this->row_id);
        }

        if ($return) {
            return $data_table;
        }

        return $data_table->make(true);
    }

}
