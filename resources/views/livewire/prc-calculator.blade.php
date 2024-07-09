<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1>Tax and Duty Calculator</h1>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>PRC Calculator</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="prcCostOfProduct">Cost of Product:</label>
                            <input type="number" step="0.01"  id="prcCostOfProduct" class="form-control" wire:model="prcCostOfProduct">
                        </div>
                        <div class="form-group">
                            <label for="prcShippingCost">Shipping Cost (Freight):</label>
                            <input type="number" step="0.01"  id="prcShippingCost" class="form-control" wire:model="prcShippingCost">
                        </div>
                        <div class="form-group">
                            <label for="prcInsurance">Insurance:</label>
                            <input type="number" step="0.01"  id="prcInsurance" class="form-control" wire:model="prcInsurance">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <h4>Total Tax and Duty: {{ $prcTotalTaxAndDuty }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Non-PRC Calculator</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="nonPrcCostOfProduct">Cost of Product:</label>
                            <input type="number" step="0.01"  id="nonPrcCostOfProduct" class="form-control" wire:model="nonPrcCostOfProduct">
                        </div>
                        <div class="form-group">
                            <label for="nonPrcShippingCost">Shipping Cost (Freight):</label>
                            <input type="number" step="0.01"  id="nonPrcShippingCost" class="form-control" wire:model="nonPrcShippingCost">
                        </div>
                        <div class="form-group">
                            <label for="nonPrcInsurance">Insurance:</label>
                            <input type="number" step="0.01"  id="nonPrcInsurance" class="form-control" wire:model="nonPrcInsurance">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <h4>Total Tax and Duty: {{ $nonPrcTotalTaxAndDuty }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
