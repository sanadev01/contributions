@extends('layouts.master')
<style>
    .preview {
        width: 500px;
        height: 500px;
        border: 1px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #webcam-video {
        display: none;
    }
</style>
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('parcel.Create Parcel')</h4>
                        <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> @lang('parcel.Back to List')</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
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
                            <form novalidate="" action="{{ route('admin.parcels.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @admin
                                <div class="row mt-1">
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                            <livewire:components.search-user />
                                            @error('pobox_number')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endadmin
                                <div class="row mt-1">
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.Sender Inside') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="merchant" value="{{ old('merchant') }}" placeholder="">
                                            @error('merchant')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.Carrier Inside') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ old('carrier') }}" placeholder=""  name="carrier">
                                            @error('carrier')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.Tracking Inside')<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="tracking_id" id="trackingInput" value="{{ old('tracking_id') }}" placeholder="" maxlength="22">
                                            @error('tracking_id')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    @can('addShipmentDetails', App\Models\Order::class)
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.External Customer Reference')<span class="text-danger"></span></label>
                                            <input type="text" class="form-control" value="{{ old('customer_reference') }}" placeholder=""  name="customer_reference" maxlength ="22">
                                            @error('customer_reference')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endcan
                                    <div class="col-12 col-sm-4">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        @admin
                                                        <label>@lang('parcel.Arrival Date')<span class="text-danger">*</span></label>
                                                        @endadmin
                                                        @user
                                                        <label>@lang('parcel.Order Date')<span class="text-danger">*</span></label>
                                                        @enduser
                                                        <input type="text" name="order_date" class="form-control order_date_picker datepicker" value="{{ old('order_date') }}" required="" placeholder="@user @lang('parcel.Order Date') @enduser @admin @lang('parcel.Arrival Date') @endadmin"/>
                                                        @error('order_date')
                                                            <div class="help-block text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('parcel.Invoice')</label>
                                            <input type="file" name="invoiceFile" {{ auth()->user()->isUser() ? 'required': ''  }} class="form-control" placeholder="@lang('parcel.Choose Invoice File')">
                                            @error('record')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @can('addWarehouseNumber', App\Models\Order::class)
                                    <div class="row">
                                        <div class="form-group col-12">
                                            <div class="controls">
                                                <label>@lang('parcel.Warehouse Number') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" required name="whr_number" value="{{ old("whr_number") }}" placeholder="">
                                                @error('whr_number')
                                                    <div class="help-block text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
                                @can('addShipmentDetails', App\Models\Order::class)
                                    <livewire:order.shipment-info />
                                    <h4 class="mt-2">@lang('parcel.Shipment Images') </h4>

                                    <!-- Button to trigger the modal -->
                                    <button class="btn btn-primary" id="openUploadModalBtn" type="button">Upload Image</button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background: #b8c2cc;">
                                                    <h5 class="modal-title" id="uploadModalLabel">Shipment Images</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <button id="webcamBtn" class="btn btn-secondary mb-3 text-dark" type="button">Upload via Webcam</button>
                                                    <button id="fileBtn" class="btn btn-secondary mb-3 text-dark" type="button">Upload from PC</button>
                                                    <div class="preview" id="preview">
                                                        <video id="webcam-video" width="500" height="500" autoplay></video>
                                                        <canvas id="canvas" width="400" height="400" style="display:none;"></canvas>
                                                    </div>
                                                    <input type="file" accept="image/*" name="images[]" id="fileInput" class="d-none">
                                                    <button id="takePhotoBtn" class="btn btn-secondary mt-3" style="display:none;" type="button">Take Photo</button>
                                                    {{-- <button id="uploadImageBtn" class="btn btn-primary mt-3" style="display:none;" type="button">Upload Image</button> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row mt-1">
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('parcel.Image 1')<span class="text-danger">*</span></label>
                                                <div class="btn-group" role="group" aria-label="Upload options">
                                                    <button type="button" class="btn btn-primary" id="uploadBtn1" data-input="fileInput1">Upload via Webcam</button>
                                                    <input type="file" accept="image/*" name="images[]" id="fileInput1" class="d-none">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('parcel.Image 2')<span class="text-danger">*</span></label>
                                                <div class="btn-group" role="group" aria-label="Upload options">
                                                    <button type="button" class="btn btn-primary" id="uploadBtn2" data-input="fileInput2">Upload via Webcam</button>
                                                    <input type="file" accept="image/*" name="images[]" id="fileInput2" class="d-none">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('parcel.Image 3')<span class="text-danger">*</span></label>
                                                <div class="btn-group" role="group" aria-label="Upload options">
                                                    <button type="button" class="btn btn-primary" id="uploadBtn3" data-input="fileInput3">Upload via Webcam</button>
                                                    <input type="file" accept="image/*" name="images[]" id="fileInput3" class="d-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                    

                                @endcan


                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('parcel.Save Parcel')
                                        </button>
                                        <button type="reset" class="btn btn-outline-danger waves-effect waves-light">@lang('parcel.Reset')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('openUploadModalBtn').addEventListener('click', function(event) {
            event.preventDefault();
            $('#uploadModal').modal('show');
        });
    
        const webcamBtn = document.getElementById('webcamBtn');
        const fileBtn = document.getElementById('fileBtn');
        const fileInput = document.getElementById('fileInput');
        const preview = document.getElementById('preview');
        const webcamVideo = document.getElementById('webcam-video');
        const canvas = document.getElementById('canvas');
        const takePhotoBtn = document.getElementById('takePhotoBtn');
        const uploadImageBtn = document.getElementById('uploadImageBtn');
        let stream;
    
        webcamBtn.onclick = async function() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    webcamVideo.srcObject = stream;
                    webcamVideo.style.display = 'block';
                    canvas.style.display = 'none';
                    takePhotoBtn.style.display = 'block';
                    uploadImageBtn.style.display = 'none';
                } catch (error) {
                    console.error('Error accessing webcam:', error);
                    alert('Unable to access the webcam. Please check your browser settings.');
                }
            } else {
                alert('Your browser does not support the webcam feature.');
            }
        };
    
        takePhotoBtn.onclick = function() {
            canvas.style.display = 'block';
            canvas.getContext('2d').drawImage(webcamVideo, 0, 0, canvas.width, canvas.height);
            webcamVideo.style.display = 'none';
            takePhotoBtn.style.display = 'none';
            uploadImageBtn.style.display = 'block';
        };
    
        fileBtn.onclick = function() {
            fileInput.click();
        };
    
        fileInput.onchange = function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    canvas.style.display = 'block';
                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                    webcamVideo.style.display = 'none';
                    takePhotoBtn.style.display = 'none';
                    uploadImageBtn.style.display = 'block';
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        };
    
        // uploadImageBtn.onclick = function() {
        //     const dataUrl = canvas.toDataURL('image/png');
        //     // Here you can add your AJAX request to send dataUrl to the server.
        //     console.log(dataUrl); // for demonstration
        //     alert('Image uploaded!'); // replace this with actual upload logic
        // };
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            function setupButtonHandler() {
                document.querySelectorAll('.btn-group button').forEach(button => {
                    button.addEventListener('click', function () {
                        const option = this.getAttribute('data-option');
                        const inputId = this.getAttribute('data-input');
                        const fileInput = document.getElementById(inputId);
    
                        if (option === 'camera') {
                            fileInput.setAttribute('capture', 'environment');
                        } else {
                            fileInput.removeAttribute('capture');
                        }
                        fileInput.click();
                    });
                });
            }
    
            setupButtonHandler();
        });
    </script> --}}

@endsection
