@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/kpi.css') }}">
<style>
  .calculator {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }

  .calculator-header {
    font-size: 1.25rem;
    padding: 10px 20px;
    margin: 0;
  }

  .prc-calculator .calculator-header {
    background: linear-gradient(to right, #2a83be, #5faf5f);
    color: white;
  }

  .non-prc-calculator .calculator-header {
    background-color: #B0BEC5;
    color: white;
  }

  .calculator-body {
    padding: 20px;
  }

  .form-group-prc {
    margin-bottom: 20px;
    position: relative;
    padding-left: 10px;
    background-color: #f2faff;
    border-left: 5px solid #1074b6;
    padding: 10px;
    border-radius: 5px;
  }

  .form-group-non-prc {
    margin-bottom: 20px;
    position: relative;
    padding-left: 10px;
    background-color: #f2faff;
    border-left: 5px solid #888888;
    padding: 10px;
    border-radius: 5px;
  }

  .form-group label {
    margin-bottom: 5px;
    display: block;
  }

  .input-group {
    display: flex;
    align-items: center;
  }

  .input-group-prepend {
    border-right: 0;
    font-size: 1.7rem;
    color: #93c4e5;
  }

  .form-control {
    border-left: 0;
    background-color: white;
    border: none;
    border-bottom: 2px solid black;
    border-radius: 0;
  }

  .form-control:focus {
    box-shadow: none;
  }

  .prc-total-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #00C853;
    text-align: center;
  }

  .non-prc-total-amount {
    font-size: 1.5rem;
    font-weight: bold;
    color: #a8a8a8;
    text-align: center;
  }

  .total-label {
    text-align: center;
    font-family: 'Arial', sans-serif;
    font-size: 24px;
    font-weight: 300;
    color: #9e9e9e;
  }

  .input-row {
    display: flex;
    justify-content: space-between;
  }

  .input-row .form-group {
    flex: 1;
    margin-right: 10px;
  }

  .input-row .form-group:last-child {
    margin-right: 0;
  }
</style>
@endsection
@section('page')
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <div class="calculator prc-calculator">
        <div class="calculator-header">PRC Calculator</div>
        <div class="calculator-body">
          <div class="input-row">
            <div class="form-group">
              <label for="prc-cost-product">Cost of Product</label>
              <div class="input-group form-group-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="prc-cost-product">
              </div>
            </div>
            <div class="form-group">
              <label for="prc-shipping-cost">Shipping Cost</label>
              <div class="input-group form-group-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="prc-shipping-cost">
              </div>
            </div>
            <div class="form-group">
              <label for="prc-insurance">Insurance</label>
              <div class="input-group form-group-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="prc-insurance">
              </div>
            </div>
          </div>
          <div class="total-label">Total Tax and Duty</div>
          <div class="prc-total-amount" id="prc-total-amount">$00.00</div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="calculator non-prc-calculator">
        <div class="calculator-header">Non-PRC Calculator</div>
        <div class="calculator-body">
          <div class="input-row">
            <div class="form-group">
              
              <label for="non-prc-cost-product">Cost of Product</label>
              <div class="input-group form-group-non-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="non-prc-cost-product">
              </div>
            </div>
            <div class="form-group">
              <label for="non-prc-shipping-cost">Shipping Cost</label>
              <div class="input-group form-group-non-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="non-prc-shipping-cost">
              </div>
            </div>
            <div class="form-group">
              <label for="non-prc-insurance">Insurance</label>
              <div class="input-group form-group-non-prc">
                <div class="input-group-prepend mr-2">
                  $
                </div>
                <input type="text" class="form-control" id="non-prc-insurance">
              </div>
            </div>
          </div>
          <div class="total-label">Total Tax and Duty</div>
          <div class="non-prc-total-amount" id="non-prc-total-amount">$00.00</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script>  
  const costOfProductInput = document.getElementById('prc-cost-product');
  const shippingCostInput = document.getElementById('prc-shipping-cost');
  const insuranceInput = document.getElementById('prc-insurance');
  const totalTaxAndDutyElement = document.getElementById('prc-total-amount');
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
    const totalIcms = (totalCostOfTheProduct / (1 - icms)) * icms;
    const prcTotalTaxAndDuty = Math.round((duty + totalIcms) * 100) / 100;
    totalTaxAndDutyElement.textContent = `$${prcTotalTaxAndDuty.toFixed(2)}`;
  } 
  const nonCostOfProductInput = document.getElementById('non-prc-cost-product');
  const nonPrcShippingCostInput = document.getElementById('non-prc-shipping-cost');
  const nonPrcInsuranceInput = document.getElementById('non-prc-insurance');
  const nonPrctotalTaxAndDutyElement = document.getElementById('non-prc-total-amount');
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
    const totalIcms = (totalCostOfTheProduct / (1 - icms)) * icms;
    const nonPrctotalTaxAndDuty = Math.round((duty + totalIcms) * 100) / 100;
    nonPrctotalTaxAndDutyElement.textContent = `$${nonPrctotalTaxAndDuty.toFixed(2)}`;
  }
</script>
@endsection('js')