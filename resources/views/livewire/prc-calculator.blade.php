<div class="mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb" style="background-color: #f7fbfe;">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Calculator</a></li>
            <li class="breadcrumb-item active" aria-current="page">PRC</li>
        </ol>
    </nav>
    <div class="row">

        <h4 class="col-12 my-4 font-weight-bold font-black"> Tax and Duty Calculator</h4>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">

                    <div class="d-flex align-items-center">
                        <div class="vertical-rectangle mb-0"> &nbsp; &nbsp;</div>
                        <h4 class="col-12 font-weight-bold font-black mb-0">PCR Calculator</h4>
                    </div>

                </div>
                <div class="card-body m-3">
                    <form class="row">
                        <div class="col-md-6"> 
                            <div class="form-group">
                                <label for="prcCostOfProduct">Cost of Product</label>
                                <input type="number" step="0.01" id="prcCostOfProduct" class="form-control pl-3" wire:model="prcCostOfProduct">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="prcShippingCost">
                                    Shiping Cost 
                                </label>
                                <input type="number" step="0.01" id="prcShippingCost" class="form-control pl-3" wire:model="prcShippingCost">
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group">
                                <label for="prcInsurance"> Insurance
                                </label>
                                <input type="number" step="0.01" id="prcInsurance" class="form-control pl-3" wire:model="prcInsurance">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="m-2">
                    <div class="container my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="vertical-rectangle mr-2 mb-0"></div>
                                <h4 class="font-weight-bold mb-0">PRC Result</h4>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-1" for="">Total Tax & Duty</label>
                                <h4 class="font-weight-bold mb-0">${{ $prcTotalTaxAndDuty }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="vertical-rectangle mb-0"> &nbsp; &nbsp;</div>
                        <h4 class="col-12 font-weight-bold font-black mb-0">Non-PCR Calculator</h4>
                    </div>
                </div>

                <div class="card-body m-3">

                    <form class="row">
                        <div class="col-md-6">
                            <div class="form-group">

                                <label for="nonPrcCostOfProduct">
                                    Cost of Product
                                </label>
                                <input type="number" step="0.01" id="nonPrcCostOfProduct" class="form-control pl-3" wire:model="nonPrcCostOfProduct">
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">

                                <label for="nonPrcShippingCost">

                                    Shipping Cost
                                </label>
                                <input type="number" step="0.01" id="nonPrcShippingCost" class="form-control pl-3" wire:model="nonPrcShippingCost">
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="form-group">
                                <label for="nonPrcInsurance">
                                    Insurance
                                </label>
                                <input type="number" step="0.01" id="nonPrcInsurance" class="form-control pl-3" wire:model="nonPrcInsurance">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="m-2">
                    <div class="container my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="vertical-rectangle mr-2 mb-0"></div>
                                <h4 class="font-weight-bold mb-0">PRC Result</h4>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="mr-2 mb-1" for="">Total Tax & Duty</label>
                                <h4 class="font-weight-bold mb-0">${{ $nonPrcTotalTaxAndDuty }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>