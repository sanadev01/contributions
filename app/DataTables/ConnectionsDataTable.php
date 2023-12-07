<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ConnectionsDataTable extends DataTableBase {

    protected $order_by = [[2, 'desc']];

    public function getBaseQuery(): ?Builder {

        $this->columns = [
            'u.id',
            'u.parent_id',
            'u.name',
            'm.code',
            'u.is_active',
            'u.seller_id',
            'spt.last_updated_at'
        ];

        return DB::table('users AS u')
            ->join('marketplaces AS m', 'm.id', 'u.marketplace_id')
            ->join('sp_tokens AS spt', 'spt.user_id', 'u.id')
            ->where('u.parent_id', $this->user->id)
            ->select($this->columns);
    }

    public function getColumnDef(): array {
        return [
            'seller_id'       => [
                'title'       => __('Seller ID'),
                'data'        => 'seller_id',
                'name'        => 'u.seller_id',
                'column_type' => 'text'
            ],
            'code'            => [
                'title'       => __('Marketplace'),
                'data'        => 'code',
                'name'        => 'm.code',
                'column_type' => 'text'
            ],
            'last_updated_at' => [
                'title'       => __('Last Updated At'),
                'data'        => 'last_updated_at',
                'name'        => 'spt.last_updated_at',
                'column_type' => 'date',
            ],
            'is_active'       => [
                'title'       => __('Active'),
                'data'        => 'is_active',
                'name'        => 'u.is_active',
                'column_type' => 'boolean',
                'raw'         => 'true'
            ],
            'action'          => [
                'title'      => __('Action'),
                'data'       => 'action',
                'name'       => 'action',
                'searchable' => false,
                'orderable'  => false,
                'raw'        => 'true',
                'content'    => function ($row) {
                    $class = $row->is_active ? 'btn-secondary' : 'btn-primary';
                    $label = $row->is_active ? __('Deactivate') : __('Activate');

                    return '<a class="btn ' . $class . ' btn-sm btn-status" href="javascript:;" data-url="' .
                        url('status-change') . '?account_id=' . $row->id . '">' . $label . '</a>';
                }
            ],
        ];
    }
}
