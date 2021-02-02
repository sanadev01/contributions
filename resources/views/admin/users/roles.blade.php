@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Manage Roles for  <u><strong>"{{ $user->name }}"</strong></u>
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
                                    <ul>
                                        @foreach ($roles as $role)
                                            <li class="list-group-item d-flex">
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="radio" name="roles[]" {{ $user->hasRole($role->id) ? 'checked': '' }} value="{{ $role->id }}">
                                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">{{ $role->name }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
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
