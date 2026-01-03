<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{translate('ready_to_Leave')}}?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">{{translate('Select_Logout_below_if_you_are_ready_to_end_your_current_session')}}.</div>
            <div class="modal-footer">
                <form action="{{route('admin.auth.logout')}}" method="post">
                    @csrf
                    <button class="btn btn-danger" type="button" data-dismiss="modal">{{translate('cancel')}}</button>
                    <button class="btn btn--primary" type="submit">{{translate('logout')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- old alert for order --}}
{{-- <div class="modal" id="popup-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <center>
                            <h2 class="__color-8a8a8a">
                                <i class="tio-shopping-cart-outlined"></i> {{ translate('you_have_new_order') }}, {{ translate('check_please') }}.
                            </h2>
                            <hr>
                            <button onclick="check_order()" class="btn btn--primary">{{ translate('ok') }}, {{ translate('let_me_check') }}</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

{{-- new alert for order --}}
<div id="alert-container" class="bottom-right-alert">
    <div class="alert alert-dismissible fade show" role="alert">
        <div class="row">
            <div class="col-12">
                <center>
                    <h2 class="__color-8a8a8a" style="font-size: 14px; color: green;">
                        <i class="tio-shopping-cart-outlined"></i> {{ translate('you_have_new_order') }}, {{ translate('check_please') }}.
                    </h2>
                    <hr class="py-0 my-1">
                    <button style="font-size: 14px;" onclick="check_order()" class="btn btn-sm btn--primary px-md-2">{{ translate('ok') }}, {{ translate('let_me_check') }}</button>
                </center>
            </div>
        </div>
        <span class="close text-danger px-1 pt-0 pb-1 mr-1 mt-1" data-dismiss="alert" aria-label="Close" style="font-size:18px;cursor: pointer;">
            <span aria-hidden="true">&times;</span>
        </span>
    </div>
</div>
