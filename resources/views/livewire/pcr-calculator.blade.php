<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center">
            <h1>Tax and Duty Calculator</h1>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>PCR Calculator</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="pcrCostOfProduct">Cost of Product:</label>
                            <input type="number" step="0.01"  id="pcrCostOfProduct" class="form-control" wire:model="pcrCostOfProduct">
                        </div>
                        <div class="form-group">
                            <label for="pcrShippingCost">Shipping Cost (Freight):</label>
                            <input type="number" step="0.01"  id="pcrShippingCost" class="form-control" wire:model="pcrShippingCost">
                        </div>
                        <div class="form-group">
                            <label for="pcrInsurance">Insurance:</label>
                            <input type="number" step="0.01"  id="pcrInsurance" class="form-control" wire:model="pcrInsurance">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <h4>Total Tax and Duty: {{ $pcrTotalTaxAndDuty }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Non-PCR Calculator</h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="nonPcrCostOfProduct">Cost of Product:</label>
                            <input type="number" step="0.01"  id="nonPcrCostOfProduct" class="form-control" wire:model="nonPcrCostOfProduct">
                        </div>
                        <div class="form-group">
                            <label for="nonPcrShippingCost">Shipping Cost (Freight):</label>
                            <input type="number" step="0.01"  id="nonPcrShippingCost" class="form-control" wire:model="nonPcrShippingCost">
                        </div>
                        <div class="form-group">
                            <label for="nonPcrInsurance">Insurance:</label>
                            <input type="number" step="0.01"  id="nonPcrInsurance" class="form-control" wire:model="nonPcrInsurance">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <h4>Total Tax and Duty: {{ $nonPcrTotalTaxAndDuty }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
