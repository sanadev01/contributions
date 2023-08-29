@extends('admin.consolidation.wizard')

@section('wizard-css')
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
@endsection
@section('wizard-form')
    <p class="h5 dim">@lang('consolidation.Consolidation Message')</p>
    @if( $errors->count() )
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.consolidation.parcels.store') }}" method="post" enctype="multipart/form-data" >
        @csrf

        @admin
        <div class="row">
            <div class="col-md-4">
                <label for="">Select User</label>
                <livewire:components.search-user />
            </div>
        </div>
        @endadmin
        <div class="row justify-content-center my-3">
            <div class="col-md-12">
                <div class="grid-wrapper w-auto border" style="max-height: 80vh; overflow-y:auto;">
                    <livewire:consolidation.parcels :user-id="old('user_id')" />
                </div>
            </div>
        </div>
        @user
            <div class="row col-md-12">
                <div class="col-md-12">
                    <h5>@lang('consolidation.warning_notice')</h5>
                </div>
                <div class="col-md-12">
                    <p>@lang('consolidation.warning_notice_message')</p>
                    <p class="text-danger">@lang('consolidation.warning_notice_message_danger')</p>
                </div>
            </div>
            <div class="row col-md-12">
                    <div class="col-md-6">
                        <input type="checkbox" required name="agree" value="1" id="agree">
                        <label for="agree">I Agree to Terms</label>
                    </div>
                    
                </div>
            </div>
        @enduser
        
        <div class="row justify-content-end">
            <div class="col-md-8 text-right">
                <button class="btn btn-primary btn-lg" type="button" onclick="getWhr()" data-toggle="modal" data-target="#confirm">@lang('consolidation.Save')</button>
            </div>
        </div>
        <div class="modal fade" id="confirm" role="dialog">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                    <div class="col-8">
                        <h4>
                            {{ auth()->user()->name }}
                            <br>
                            {{ auth()->user()->pobox_number }}
                        </h4>
                        <h5>
                            2200 NW, 129th Ave – Suite # 100<br>
                            Miami, FL, 33182<br>
                            United States<br>
                            Ph#: +13058885191<br>
                        </h5>
                        <h4 class="font-weight-bold">
                            To <br>
                            HERCO FREIGHT dba HomeDeliverybr
                        </h4>
                        <h5>
                            Date: {{ \Carbon\Carbon::now()->format('d,M,Y') }}
                        </h5>
                    </div>
                </div>
                <div class="modal-body" style="font-size: 15px;">
                    <p>
                        @lang('consolidation.Authorization')
                    </p>
                    <p>
                        @lang('consolidation.description')
                        <span class="result"></span>
                        @lang('consolidation.description-2')
                    </p>
                    <p>
                        @lang('consolidation.conditions')
                    </p>
                    <p class="mt-5">
                        @lang('consolidation.thank you')<br><br>
                        @lang('consolidation.Respectfully')<br><br>
                        <h4>
                            {{ auth()->user()->name }}
                            <br>
                            {{ auth()->user()->pobox_number }}
                        </h4>
                    </p>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="save"> @lang('consolidation.Authoriz')</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal"> @lang('consolidation.Cancel')</button>
                </div>
              </div>
            </div>
        </div>
    </form>
@endsection
@section('js')

    <script>
        function getWhr(){
            $('input[name="parcels[]"]:checked').each(function() {
                $(".result").append('HD-' + this.value + ',');
            });
        }
        $('#save').click(function() {
            $('#confirm').modal('hide');
        });
    </script>

@endsection