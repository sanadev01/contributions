<div>
    <div class="row text-center">
        <div class="col-md-12">
            <h2> @lang('affiliate-dashboard.Referral Link') <b> @lang('affiliate-dashboard.OR')</b></h2>
        </div>
    </div>
    
    <div class="row mb-1 justify-content-center">
        <div class="col-md-6">
            <div class="d-flex">
                <input type="text" class="form-control w-75" id="link" name="link" value="{{ route('register',['ref'=>$reffer_code] ) }}" readonly>
                <button onclick="copyToClipboard()" class="btn btn-primary w-25">
                    Copy
                </button>
            </div>
        </div>
    </div>
</div>
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