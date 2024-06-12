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
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('admin.users.permissions.store',$user) }}" method="POST">
                                <div class="row">
                                    @csrf
                                    @foreach ($permissionGroups as $group=>$permissions)
                                        <div class="col-md-4 mb-2">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" id="{{str_slug($group)}}" class="group-header" data-target=".group-{{str_slug($group)}}">
                                                        <span class="vs-checkbox">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="">
                                                            <h3>
                                                                {{ $group }}
                                                            </h3>
                                                        </span>
                                                    </div>
                                                    
                                                </li>
                                                @foreach ($permissions as $permission)
                                                    <li class="list-group-item d-flex">
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <input class="group-{{str_slug($group)}} child" data-parent="#{{str_slug($group)}}" type="checkbox" name="permissions[]" {{ $user->hasPermissionById($permission->id) ? 'checked': '' }} value="{{ $permission->id }}">
                                                            <span class="vs-checkbox">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                            <span class="">{{ $permission->name }}</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
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
            $('.group-header').on('change',function(){
                if ( $(this).prop('checked') ){
                    $( $(this).data('target') ).each(function(){
                        $(this).prop('checked',true);
                    })
                }else{
                    $( $(this).data('target') ).each(function(){
                        $(this).prop('checked',false);
                    })
                }
            })

            $('.child').on('click',function(){
                if ( $(this).prop('checked') ){
                    $( $(this).data('parent') ).prop('checked',true)
                }
            })

            $('.child').each(function(){
                if ( $(this).prop('checked') ){
                    $( $(this).data('parent') ).prop('checked',true)
                }
            })
        })
    </script>
@endsection