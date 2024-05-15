<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\POSTNL\ExportExcelPOSTNLManifestService;
use App\Repositories\Warehouse\POSTNLContainerRepository;
use App\Http\Requests\Warehouse\PostnlContainer\CreateContainerRequest;
use App\Http\Requests\Warehouse\PostnlContainer\UpdateContainerRequest;

class POSTNLContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(POSTNLContainerRepository $postnl_containerRepository)
    {
        $containers = $postnl_containerRepository->get();

        return view('admin.warehouse.postnlContainers.index', compact('containers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warehouse.postnlContainers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateContainerRequest $createContainerRequest, POSTNLContainerRepository $postnl_containerRepository)
    {
        if ( $container = $postnl_containerRepository->store($createContainerRequest) ){
            session()->flash('alert-success', 'PostNL Container Creation Successfull. Please Scan Packages');
            return redirect()->route('warehouse.postnl_containers.index');
        }
        session()->flash('alert-danger', $postnl_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($container)
    {
        $container = Container::find($container);

        if ( $container->response != 0 ){
            abort(405);
        }

        return view('admin.warehouse.postnlContainers.edit',compact('container'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContainerRequest $updateContainerRequest, POSTNLContainerRepository $postnl_containerRepository)
    {
        //dd($updateContainerRequest);
        $container = Container::find($updateContainerRequest->id);

        if ( $container->response != 0 ){
            abort(405);
        }

        if ( $container = $postnl_containerRepository->update($container, $updateContainerRequest) ){
            session()->flash('alert-success', 'Container Updae Successfull, Please Scann Packages');
            return redirect()->route('warehouse.postnl_containers.index');
        }
        session()->flash('alert-danger', $postnl_containerRepository->getError());
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($container, POSTNLContainerRepository $postnl_containerRepository)
    {
        $container = Container::find($container);
        if ( $container->respone != 0 ){
            abort(403,'Cannot Delete Container registered on POST NL.');
        }
        if ( $container = $postnl_containerRepository->delete($container) ){
            session()->flash('alert-success', 'Container Deleted');
            return redirect()->route('warehouse.postnl_containers.index');
        }

        session()->flash('alert-danger', $postnl_containerRepository->getError());
        return back()->withInput();
    }

    public function download_exceltManifest(Container $container)
    {
        $exportPostNLManifestService = new ExportExcelPOSTNLManifestService($container);
        return $exportPostNLManifestService->handle();
    }
}
