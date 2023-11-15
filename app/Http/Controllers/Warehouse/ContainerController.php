<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\Container\CreateContainerRequest;
use App\Http\Requests\Warehouse\Container\UpdateContainerRequest;
use App\Models\Warehouse\Container;
use App\Repositories\Warehouse\ContainerRepository;
use Illuminate\Http\Request;
use App\Models\User;

class ContainerController extends Controller
{
    public function index()
    {
        return view('admin.warehouse.containers.index');
    }

    public function create()
    {
        $anjunApi = (setting('anjun_api', null, User::ROLE_ADMIN));
        $bcnApi = (setting('bcn_api', null, User::ROLE_ADMIN));
        $anjunChinaApi = (setting('china_anjun_api', null, User::ROLE_ADMIN));
        $correioApi = (setting('correios_api', null, User::ROLE_ADMIN));

        return view('admin.warehouse.containers.create', compact('anjunApi','bcnApi','anjunChinaApi','correioApi'));
    }

    public function store(CreateContainerRequest  $createContainerRequest, ContainerRepository $containerRepository)
    {
        if ( $container = $containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.containers.index');
        }
        session()->flash('alert-danger', $containerRepository->getError());
        return back()->withInput();
    }

    public function edit(Container $container)
    {
        if ( $container->isRegistered() ){
            abort(405);
        }

        return view('admin.warehouse.containers.edit',compact('container'));
    }

    public function update(UpdateContainerRequest $updateContainerRequest, Container $container, ContainerRepository $containerRepository)
    {
        if ( $container->isRegistered() ){
            abort(405);
        }
        
        if ( $container = $containerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Saved Please Scann Packages');
            return redirect()->route('warehouse.containers.index');
        }
        session()->flash('alert-danger', $containerRepository->getError());
        return back()->withInput();
    }

    public function destroy(Container $container, ContainerRepository $containerRepository)
    {
        if ( $container->isRegistered() ){
            abort(403,'Cannot Delete Container registered on Correios.');
        }
        if ( $container = $containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.containers.index');
        }

        session()->flash('alert-danger', $containerRepository->getError());
        return back()->withInput();
    }
}
