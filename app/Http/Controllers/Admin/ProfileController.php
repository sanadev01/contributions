<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Shared\Profile\Create;
use App\Models\ProfitPackage;
use App\Repositories\ProfileRepository;


class ProfileController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('admin.profile.index'); 
    }

        /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     * @return void
     */
    public function store(Create $request, ProfileRepository $repository)
    {   
        if ( $repository->store($request) ){
            session()->flash('alert-success', 'profile.Updated');
        }
        
        return back();
    }
}
