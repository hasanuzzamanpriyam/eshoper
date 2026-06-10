@if($web_config['popup_banner'])
    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background: transparent; border: none; box-shadow: none;">
                <div class="modal-header border-0 p-0" style="position: relative;">
                    <button type="button" class="close __close" data-dismiss="modal" aria-label="Close"
                        style="position: absolute; top: -12px; right: -12px; background: white; border-radius: 50%; width: 30px; height: 30px; opacity: 1; z-index: 10; padding: 0; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" onclick="location.href='{{$web_config['popup_banner']['url']}}'">
                    <img class="d-block w-100" style="border-radius: 10px; cursor: pointer;"
                         onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                         src="{{asset('storage/banner')}}/{{$web_config['popup_banner']['photo']}}"
                         alt="">
                </div>
            </div>
        </div>
    </div>
@endif


