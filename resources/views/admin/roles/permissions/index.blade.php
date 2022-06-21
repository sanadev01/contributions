@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Manage Permissions
                        </h4>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('admin.roles.permissions.store',$role) }}" method="POST">
                                @csrf
                                <table class="table table-responsive-md table-bordered">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="checkall">
                                            </th>
                                            <th>
                                                Slug
                                            </th>
                                            <th>
                                                Description
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permissions as $permission)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="permission-box" {{ $role->hasPermission($permission->id) ? 'checked' : '' }} name="permissions[]" value="{{$permission->id}}">
                                                </td>
                                                <td>
                                                    {{ $permission->slug }}
                                                </td>
                                                <td>
                                                    {{ $permission->description }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row mt-2">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-lg btn-primary">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('js')
    <script>

        $(function(){
           
            $('#checkall').on('change',function(){
                if ( $(this).prop('checked') ){
                    $('.permission-box').prop('checked',true);
                }else{
                    $('.permission-box').prop('checked',false);
                }
            })
        })
    </script>
@endsection