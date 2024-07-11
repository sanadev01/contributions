@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">
<style>
  .vertical-rectangle {
    background-color: #1074B6;
    width: 3.1px;
    height: 16.8px;
    gap: 0px;
    opacity: 0px;
  }

  .form-group:hover input {
    border-left: 5px solid #1074B6;
    ;
    background-color: #F2FAFF;
  }

  .form-control {
    background-color: #F8F8F8;

  }

  .form-group:hover label {
    color: #1074B6;
  }
</style>
@endsection
@section('page')
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
            <h4 class="col-12 font-weight-bold font-black mb-0">PRC Calculator</h4>
          </div>
        </div>
        <div class="card-body m-3">
          <form class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="prcCostOfProduct">Cost of Product</label>
                <input type="number" step="0.01" id="prcCostOfProduct" class="form-control pl-3">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="prcShippingCost">Shipping Cost</label>
                <input type="number" step="0.01" id="prcShippingCost" class="form-control pl-3">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="prcInsurance">Insurance</label>
                <input type="number" step="0.01" id="prcInsurance" class="form-control pl-3">
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
                <h4 class="font-weight-bold mb-0" id="prcTotalTaxAndDuty">$0.00</h4>
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
            <h4 class="col-12 font-weight-bold font-black mb-0">Non-PRC Calculator</h4>
          </div>
        </div>
        <div class="card-body m-3">
          <form class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nonPrcCostOfProduct">Cost of Product</label>
                <div class="input-group">
                  <input type="number" step="0.01" id="nonPrcCostOfProduct" class="form-control pl-3">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="nonPrcShippingCost">Shipping Cost</label>
                <div class="input-group">
                  <input type="number" step="0.01" id="nonPrcShippingCost" class="form-control pl-3">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="nonPrcInsurance">Insurance</label>
                <div class="input-group">
                  <input type="number" step="0.01" id="nonPrcInsurance" class="form-control pl-3">
                </div>
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
                <h4 class="font-weight-bold mb-0">Non-PRC Result</h4>
              </div>
              <div class="d-flex align-items-center">
                <label class="mr-2 mb-1" for="">Total Tax & Duty</label>
                <h4 class="font-weight-bold mb-0" id="nonPrcTotalTaxAndDuty">$0.00</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script>
  // Get input elements
  const costOfProductInput = document.getElementById('prcCostOfProduct');
  const shippingCostInput = document.getElementById('prcShippingCost');
  const insuranceInput = document.getElementById('prcInsurance');
  const totalTaxAndDutyElement = document.getElementById('prcTotalTaxAndDuty');

  // Add event listeners
  costOfProductInput.addEventListener('keyup', calculatePRC);
  shippingCostInput.addEventListener('keyup', calculatePRC);
  insuranceInput.addEventListener('keyup', calculatePRC);
 

  function calculatePRC() { 
    
    const costOfProduct = parseFloat(costOfProductInput.value) || 0;
    const shippingCost = parseFloat(shippingCostInput.value) || 0;
    const insurance = parseFloat(insuranceInput.value) || 0;

    const totalCost = shippingCost + costOfProduct + insurance;
    const duty = totalCost > 50 ? (totalCost * 0.60 - 20) : totalCost * 0.2;
    const totalCostOfTheProduct = totalCost + duty;
    const icms = 0.17;
    const totalIcms = totalCostOfTheProduct * icms;

    const prcTotalTaxAndDuty = Math.round((duty + totalIcms) * 100) / 100;
    totalTaxAndDutyElement.textContent = `$${prcTotalTaxAndDuty.toFixed(2)}`;
  }

  
  const nonCostOfProductInput = document.getElementById('nonPrcCostOfProduct');
  const nonPrcShippingCostInput = document.getElementById('nonPrcShippingCost');
  const nonPrcInsuranceInput = document.getElementById('nonPrcInsurance');
  const nonPrctotalTaxAndDutyElement = document.getElementById('nonPrcTotalTaxAndDuty');

  nonCostOfProductInput.addEventListener('keyup', calculateNonPrc);
  nonPrcShippingCostInput.addEventListener('keyup', calculateNonPrc);
  nonPrcInsuranceInput.addEventListener('keyup', calculateNonPrc);

  function calculateNonPrc() {
    const costOfProduct = parseFloat(nonCostOfProductInput.value) || 0;
    const shippingCost = parseFloat(nonPrcShippingCostInput.value) || 0;
    const insurance = parseFloat(nonPrcInsuranceInput.value) || 0;

    const totalCost = shippingCost + costOfProduct + insurance;
    const duty = totalCost * 0.60;
    const totalCostOfTheProduct = totalCost + duty;
    const icms = 0.17;
    const totalIcms = totalCostOfTheProduct * icms;

    const nonPrctotalTaxAndDuty = Math.round((duty + totalIcms) * 100) / 100;
 
    nonPrctotalTaxAndDutyElement.textContent = `$${nonPrctotalTaxAndDuty.toFixed(2)}`;
  }
</script>
@endsection('js')