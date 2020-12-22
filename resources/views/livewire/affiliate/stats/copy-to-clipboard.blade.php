
    <div class="card-header">
        <h2 class="col-md-3 offset-4"> @lang('affiliatedashboard.Referral Link') <b> @lang('affiliatedashboard.OR')</b></h2>
    </div>
    <div class="card-content mb-4">
        <div class="mt-1">
            <div class="controls row mb-1 align-items-center">
                <div class="col-md-4 offset-3">
                    <input type="text" class="form-control" id="link" name="link" value="{{ route('register',['ref'=>$reffer_code] ) }}" readonly>
                    <div class="help-block"></div>
                </div>
                <div class="col-md-2">
                    <div class="">
                        <button onclick="copyToClipboard()" class="btn btn-primary">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <livewire:affiliate.stats.barcode :reffer_code="$reffer_code"/>
@section('js')

    <script>
        function copyToClipboard() {
          var copyText = document.getElementById("link");
          copyText.select();
          copyText.setSelectionRange(0, 99999)
          document.execCommand("copy");
        }
    </script>
@endsection