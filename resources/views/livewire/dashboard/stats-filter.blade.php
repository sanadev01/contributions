<div>

    <div class="row col-12 mt-4">
        <div class=" col-11 text-left mb-2">
            <div class="row my-3">
                <div class="col-md-4">
                    <label for="">@lang('dashboard.Start Date')</label>
                    <input type="date" class="form-control" wire:model="startDate">
                </div>
                <div class="col-md-4">
                    <label for="">@lang('dashboard.End Date')</label>
                    <input type="date" class="form-control" wire:model="endDate">
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-green order-card  border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row">
                        <div class="col-9">
                            <h6 class="white height-30">@lang('dashboard.Today Orders')</h6>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.4984 2.65185e-06C19.4243 2.65185e-06 25.005 5.59059 25.005 12.5311C25.005 19.4272 19.3932 25 12.4584 24.9956C5.59905 24.9956 -0.0660144 19.3517 0.000581219 12.3668C0.0671768 5.56839 5.63013 -0.00443785 12.4984 2.65185e-06ZM1.68323 12.5133C1.70099 18.4947 6.55803 23.3393 12.5161 23.3259C18.4964 23.3126 23.3357 18.468 23.3312 12.4956C23.3312 6.52309 18.4786 1.67407 12.5072 1.67851C6.52695 1.67851 1.66547 6.5453 1.68323 12.5133Z"
                                    fill="white" />
                                <path
                                    d="M18.3146 9.5426C18.3013 9.60921 18.2391 9.64917 18.1947 9.69358C16.5609 11.3543 14.9271 13.0151 13.2933 14.6758C12.4098 15.5728 11.5307 16.4698 10.6517 17.3668C10.5585 17.4644 10.5007 17.5177 10.3764 17.389C9.19102 16.1678 8.00562 14.9511 6.81134 13.7389C6.68702 13.6145 6.68259 13.5435 6.81134 13.4236C7.12212 13.1261 7.41958 12.8197 7.7126 12.5044C7.81471 12.3934 7.87243 12.4112 7.9701 12.5089C8.76037 13.3259 9.56396 14.1385 10.3542 14.96C10.4874 15.0977 10.554 15.0843 10.6783 14.9556C12.7961 12.793 14.9182 10.6394 17.036 8.48132C17.147 8.36587 17.2091 8.34811 17.3246 8.47688C17.6131 8.79216 17.9239 9.08967 18.2214 9.40051C18.2569 9.44047 18.3102 9.47155 18.3146 9.5426Z"
                                    fill="white" />
                            </svg>

                        </div>
                    </div>
                    <h2>
                        <span class="white">{{ $orders['currentDayTotal'] }}</span>
                    </h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right">{{ $orders['currentDayConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-yellow order-card border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row">
                        <div class="col-9">
                            <h6 class="white height-30">@lang('dashboard.Total Month Order', ['month' => $orders['monthName']])</h6>
                        </div>
                        <div class="col-3 d-flex justify-content-end">
                            <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.4984 2.65185e-06C19.4243 2.65185e-06 25.005 5.59059 25.005 12.5311C25.005 19.4272 19.3932 25 12.4584 24.9956C5.59905 24.9956 -0.0660144 19.3517 0.000581219 12.3668C0.0671768 5.56839 5.63013 -0.00443785 12.4984 2.65185e-06ZM1.68323 12.5133C1.70099 18.4947 6.55803 23.3393 12.5161 23.3259C18.4964 23.3126 23.3357 18.468 23.3312 12.4956C23.3312 6.52309 18.4786 1.67407 12.5072 1.67851C6.52695 1.67851 1.66547 6.5453 1.68323 12.5133Z"
                                    fill="white" />
                                <path
                                    d="M18.3146 9.5426C18.3013 9.60921 18.2391 9.64917 18.1947 9.69358C16.5609 11.3543 14.9271 13.0151 13.2933 14.6758C12.4098 15.5728 11.5307 16.4698 10.6517 17.3668C10.5585 17.4644 10.5007 17.5177 10.3764 17.389C9.19102 16.1678 8.00562 14.9511 6.81134 13.7389C6.68702 13.6145 6.68259 13.5435 6.81134 13.4236C7.12212 13.1261 7.41958 12.8197 7.7126 12.5044C7.81471 12.3934 7.87243 12.4112 7.9701 12.5089C8.76037 13.3259 9.56396 14.1385 10.3542 14.96C10.4874 15.0977 10.554 15.0843 10.6783 14.9556C12.7961 12.793 14.9182 10.6394 17.036 8.48132C17.147 8.36587 17.2091 8.34811 17.3246 8.47688C17.6131 8.79216 17.9239 9.08967 18.2214 9.40051C18.2569 9.44047 18.3102 9.47155 18.3146 9.5426Z"
                                    fill="white" />
                            </svg>
                        </div>
                    </div>
                    <h2><span class="white">{{ $orders['currentmonthTotal'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')
                        <span class="f-right white">{{ $orders['currentmonthConfirm'] }}</span>
                    </h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-pink order-card  border-radius-15">
                <div class="card-block   border-radius-15">
                    <div class="row">
                        <h6 class="col-9 white height-30">@lang('dashboard.Current Year')</h6>
                        <div class="col-3 d-flex justify-content-end">
                            <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.4984 2.65185e-06C19.4243 2.65185e-06 25.005 5.59059 25.005 12.5311C25.005 19.4272 19.3932 25 12.4584 24.9956C5.59905 24.9956 -0.0660144 19.3517 0.000581219 12.3668C0.0671768 5.56839 5.63013 -0.00443785 12.4984 2.65185e-06ZM1.68323 12.5133C1.70099 18.4947 6.55803 23.3393 12.5161 23.3259C18.4964 23.3126 23.3357 18.468 23.3312 12.4956C23.3312 6.52309 18.4786 1.67407 12.5072 1.67851C6.52695 1.67851 1.66547 6.5453 1.68323 12.5133Z"
                                    fill="white" />
                                <path
                                    d="M18.3146 9.5426C18.3013 9.60921 18.2391 9.64917 18.1947 9.69358C16.5609 11.3543 14.9271 13.0151 13.2933 14.6758C12.4098 15.5728 11.5307 16.4698 10.6517 17.3668C10.5585 17.4644 10.5007 17.5177 10.3764 17.389C9.19102 16.1678 8.00562 14.9511 6.81134 13.7389C6.68702 13.6145 6.68259 13.5435 6.81134 13.4236C7.12212 13.1261 7.41958 12.8197 7.7126 12.5044C7.81471 12.3934 7.87243 12.4112 7.9701 12.5089C8.76037 13.3259 9.56396 14.1385 10.3542 14.96C10.4874 15.0977 10.554 15.0843 10.6783 14.9556C12.7961 12.793 14.9182 10.6394 17.036 8.48132C17.147 8.36587 17.2091 8.34811 17.3246 8.47688C17.6131 8.79216 17.9239 9.08967 18.2214 9.40051C18.2569 9.44047 18.3102 9.47155 18.3146 9.5426Z"
                                    fill="white" />
                            </svg>

                        </div>
                    </div>
                    <h2><span class="white"> {{ $orders['currentYearTotal'] }} </span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['currentYearConfirm'] }}</span></h6>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
            <div class="card_block bg-c-blue order-card  border-radius-15">
                <div class="card-block border-radius-15">
                    <div class="row">
                        <h6 class="col-9  white height-30">@lang('dashboard.Total Orders')</h6>
                        <div class="col-3 d-flex justify-content-end">
                            <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M12.4984 2.65185e-06C19.4243 2.65185e-06 25.005 5.59059 25.005 12.5311C25.005 19.4272 19.3932 25 12.4584 24.9956C5.59905 24.9956 -0.0660144 19.3517 0.000581219 12.3668C0.0671768 5.56839 5.63013 -0.00443785 12.4984 2.65185e-06ZM1.68323 12.5133C1.70099 18.4947 6.55803 23.3393 12.5161 23.3259C18.4964 23.3126 23.3357 18.468 23.3312 12.4956C23.3312 6.52309 18.4786 1.67407 12.5072 1.67851C6.52695 1.67851 1.66547 6.5453 1.68323 12.5133Z"
                                    fill="white" />
                                <path
                                    d="M18.3146 9.5426C18.3013 9.60921 18.2391 9.64917 18.1947 9.69358C16.5609 11.3543 14.9271 13.0151 13.2933 14.6758C12.4098 15.5728 11.5307 16.4698 10.6517 17.3668C10.5585 17.4644 10.5007 17.5177 10.3764 17.389C9.19102 16.1678 8.00562 14.9511 6.81134 13.7389C6.68702 13.6145 6.68259 13.5435 6.81134 13.4236C7.12212 13.1261 7.41958 12.8197 7.7126 12.5044C7.81471 12.3934 7.87243 12.4112 7.9701 12.5089C8.76037 13.3259 9.56396 14.1385 10.3542 14.96C10.4874 15.0977 10.554 15.0843 10.6783 14.9556C12.7961 12.793 14.9182 10.6394 17.036 8.48132C17.147 8.36587 17.2091 8.34811 17.3246 8.47688C17.6131 8.79216 17.9239 9.08967 18.2214 9.40051C18.2569 9.44047 18.3102 9.47155 18.3146 9.5426Z"
                                    fill="white" />
                            </svg>

                        </div>
                    </div>
                    <h2><span class="white">{{ $orders['totalOrders'] }}</span></h2>
                    <h6 class="white">@lang('dashboard.Completed Orders')<span
                            class="f-right white">{{ $orders['totalCompleteOrders'] }}</span></h6>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>
