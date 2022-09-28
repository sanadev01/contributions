<tr @if ($product->quantity <= 0) style="background: red;" @endif>
    <td>
        <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
            <input type="checkbox" onchange="handleChange(this)" name="orders[]" class="bulk-orders"
                value="{{ $product->id }}" @if ($product->status == 'pending') disabled @endif>
            <span class="vs-checkbox vs-checkbox-sm">
                <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                </span>
            </span>
            <span class="h3 mx-2 text-primary my-0 py-0"></span>
        </div>
    </td>
    <td>{{ optional($product->created_at)->format('m/d/Y') }}</td>
    @admin
        <td>{{ $product->user->name }}</td>
    @endadmin
    <td>{{ $product->name }}</td>
    <td>{{ $product->price }}</td>
    <td>{{ $product->quantity }}</td>
    <td>{{ $product->sku }}</td>
    <td>{{ $product->barcode }}</td>
    <td>{{ $product->weight }}</td>
    <td>{{ $product->measurement_unit }}</td>
    <td>{{ $product->quantity *  $product->price}}</td>
    <td>{{ $product->exp_date ? date('Y-m-d', strtotime($product->exp_date)) : '' }}</td>
    <td>{{ $product->description }}</td>
    <td>
        <select class="form-control{{ !auth()->user()->isAdmin()? 'btn disabled': '' }} {{ $product->getStatusClass() }}"
            @if (auth()->user()->isAdmin()) wire:change="$emit('updated-status',{{ $product->id }},$event.target.value)" @else disabled="disabled" @endif>
            <option class="bg-info" value="pending" {{ $product->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option class="bg-success" value="approved" {{ $product->status == 'approved' ? 'selected' : '' }}>Approved</option>
        </select>
    </td>

    <td class="d-flex">
        <div class="btn-group">
            <div class="dropdown">
                <button title="Action" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="btn btn-primary btn-sm waves-effect waves-light">
                    <i class="feather icon-edit-1 mr-0"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropright">

                    <button data-toggle="modal" data-target="#hd-modal"
                        data-url="{{ route('admin.inventory.product.show', $product) }}"
                        class="btn dropdown-item w-100" title="Show Product Details">
                        <i class="feather icon-list"></i> View Product
                    </button>

                    @if ($product->status == 'approved')
                        <a href="{{ route('admin.inventory.product-order.show', $product) }}"
                            title="Create Sale Order" class="dropdown-item w-100">
                            <i class="feather icon-truck"></i> Sale Order
                        </a>
                    @endif

                    @can('update', $product)
                        <a href="{{ route('admin.inventory.product.edit', $product) }}" title="Edit"
                            class="dropdown-item w-100">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                    @endcan

                    @can('delete', $product)
                        <form action="{{ route('admin.inventory.product.destroy', $product) }}" class="d-flex"
                            method="post" onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button class="dropdown-item w-100 text-danger">
                                <i class="feather icon-trash-2"></i> Delete
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </td>
</tr>
