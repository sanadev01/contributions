<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Role $role)
    {
        $permissionGroups = Permission::all()->groupBy('group');
        return view('admin.roles.permissions.index',compact('role','permissionGroups'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Role $role)
    {
        $role->permissions()->sync($request->permissions);
        \Cache::forget("roles-permissions-".$role->id);

        session()->flash('alert-success','Permissions Updated');
        return back();
    }
}
