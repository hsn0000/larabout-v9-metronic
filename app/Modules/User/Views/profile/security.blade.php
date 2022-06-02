
<div class="card card-custom card-stretch">
    <!--begin::Header-->
    <div class="card-header py-3">
        <div class="card-title align-items-start flex-column">
            <h3 class="card-label font-weight-bolder text-dark">Security</h3>
            <span class="text-muted font-weight-bold font-size-sm mt-1">Additional settings for you account secures</span>
        </div>
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body">
        @if (session('msg_error'))
            <x-alert type="danger" :message="session('msg_error')" dismissible="TRUE"/>
        @endif
        @if (session('msg_success'))
            <x-alert type="success" :message="session('msg_success')" dismissible="TRUE"/>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="row">
            <div class="col-lg-12">
                <h5 class="font-weight-bold mb-6">Two Factor Authentication</h5>
            </div>
        </div>
        <div class="form-group row mb-0">
            <div class="col-lg-12">
                @if($enabled_two_factor)
                    <p class="text-success mt-0">You have enabled two factor authentication.</p>
                @else
                    <p class="text-danger mt-0">You have not enabled two factor authentication.</p>
                @endif

                @if(!$enabled_two_factor || $showingQrCode)
                <p class="form-text">
                    When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. <br>
                    You may retrieve this token from your phone's Google Authenticator application.
                    <br><br>
                    Download Google Authenticator below :
                </p>

                <div class="d-flex">
                    <div class="android mr-5">
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="d-flex text-hover-info">
                            <i class="icon-lg fab fa-google-play mr-2"></i>
                            <span class="text-muted text-hover-info">Google Play</span>
                        </a>
                    </div>
                    <div class="ios">
                        <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="d-flex text-hover-info">
                            <i class="icon-lg fab fa-app-store-ios mr-2"></i>
                            <span class="text-muted text-hover-info">App Store</span>
                        </a>
                    </div>
                </div>
                @endif

                <div class="separator separator-dashed separator-border-2 mb-4 mt-6"></div>

                @if ($showingQrCode)
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p class="font-semibold">
                            {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application.') }}
                        </p>
                    </div>

                    <div class="mt-4 dark:p-4 dark:w-56 dark:bg-white">
                        {!! Auth::user()->twoFactorQrCodeSvg() !!}
                    </div>
                    <div class="separator separator-dashed separator-border-2 mb-4 mt-4"></div>
                @endif

                @if ($showingRecoveryCodes)
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p class="font-semibold">
                            {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                        </p>
                    </div>

                    <div class="max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg mb-5">
                        @foreach (json_decode(decrypt(Auth::user()->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                @endif

                @if(!$enabled_two_factor)
                    <button type="button" class="btn btn-sm btn-info btn-loading btn-enable-two-factor">Enable Two Factor</button>
                @else
                    @if ($showingRecoveryCodes)
                        <button class="btn btn-sm btn-default btn-loading btn-regenerate-code">Regenerate Recovery Codes</button>
                    @else
                        <button class="btn btn-sm btn-default btn-loading btn-show-code">Show Recovery Codes</button>
                    @endif
                    <button class="btn btn-sm btn-danger btn-loading btn-disable-two-factor ml-2">Disable</button>
                @endif
            </div>
        </div>
        <div class="separator separator-dashed separator-border-2 mb-4 mt-4"></div>
        @endif

        <div class="row">
            <label class="col-xl-3"></label>
            <div class="col-lg-12">
                <h5 class="font-weight-bold mb-6">Browser Sessions</h5>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-lg-12">

                <p class="form-text">
                    If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. <br>
                    If you feel your account has been compromised, you should also update your password.
                </p>

                @if (count($browser) > 0)
                    <div class="mt-5 space-y-6">
                        <!-- Other Browser Sessions -->
                        @foreach ($browser as $session)
                            <div class="d-flex items-center mb-5">
                                <div>
                                    @if ($session->agent->isDesktop())
                                        <span class="svg-icon svg-icon-primary svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                        </span>
                                    @else
                                        <span class="svg-icon svg-icon-primary svg-icon-2x">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M8,2.5 C7.30964406,2.5 6.75,3.05964406 6.75,3.75 L6.75,20.25 C6.75,20.9403559 7.30964406,21.5 8,21.5 L16,21.5 C16.6903559,21.5 17.25,20.9403559 17.25,20.25 L17.25,3.75 C17.25,3.05964406 16.6903559,2.5 16,2.5 L8,2.5 Z" fill="#000000" opacity="0.3"/>
                                                    <path d="M8,2.5 C7.30964406,2.5 6.75,3.05964406 6.75,3.75 L6.75,20.25 C6.75,20.9403559 7.30964406,21.5 8,21.5 L16,21.5 C16.6903559,21.5 17.25,20.9403559 17.25,20.25 L17.25,3.75 C17.25,3.05964406 16.6903559,2.5 16,2.5 L8,2.5 Z M8,1 L16,1 C17.5187831,1 18.75,2.23121694 18.75,3.75 L18.75,20.25 C18.75,21.7687831 17.5187831,23 16,23 L8,23 C6.48121694,23 5.25,21.7687831 5.25,20.25 L5.25,3.75 C5.25,2.23121694 6.48121694,1 8,1 Z M9.5,1.75 L14.5,1.75 C14.7761424,1.75 15,1.97385763 15,2.25 L15,3.25 C15,3.52614237 14.7761424,3.75 14.5,3.75 L9.5,3.75 C9.22385763,3.75 9,3.52614237 9,3.25 L9,2.25 C9,1.97385763 9.22385763,1.75 9.5,1.75 Z" fill="#000000" fill-rule="nonzero"/>
                                                </g>
                                            </svg>
                                        </span>
                                    @endif
                                </div>

                                <div class="ml-3">
                                    <div class="text-sm text-gray-600">
                                        {{ $session->agent->platform() }} - {{ $session->agent->browser() }}
                                    </div>

                                    <div>
                                        <div class="text-xs text-gray-500">
                                            <span class="mr-2">{{ $session->ip_address }}</span>

                                            @if ($session->is_current_device)
                                                <span class="text-success">This device</span>
                                            @else
                                                <span class="text-muted">Last active {{ $session->last_active }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <button class="btn btn-sm btn-danger mt-5 btn-logout-other-devices">Log Out Other Browser Sessions</button>
            </div>
        </div>
    </div>
    <!--end::Body-->
</div>

@section('content_js')
<script>
    $(function(){
        var confirm_password = function(params='') {
            confirmModal.find('.modal-title > span').text('Confirm Password');
            confirmModal.find('.modal-body').html('<p class="mb-5">For your security, please confirm your password to continue.</p><input type="password" class="form-control" id="confirm-password" placeholder="Current Password" maxlength="50">');
            confirmModal.find('.modal-footer > .btn-modal-action').attr('data-action', 'confirm-password').attr('data-params', params);
            confirmModal.modal('show');
        }
        @if(!$enabled_two_factor)
        $('.btn-enable-two-factor').click(function(){
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            $.post('{{ route("profile.enable-two-factor") }}', {_token: "{{ csrf_token() }}"}, function(e){
                location.reload();
            }).fail(function(xhr) {
                btn.html(initialText).removeClass("disabled").prop("disabled",false);
                if(xhr.status==401){
                    confirm_password('.btn-enable-two-factor');
                    return false;
                }
                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                alertModal.modal("show");
            });
        });
        @else
        @if ($showingRecoveryCodes)
        $('.btn-regenerate-code').click(function(){
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            $.post('{{ route("profile.generate-two-factor-code") }}', {_token: "{{ csrf_token() }}"}, function(e){
                location.reload();
            }).fail(function(xhr) {
                btn.html(initialText).removeClass("disabled").prop("disabled",false);
                if(xhr.status==401){
                    confirm_password('.btn-regenerate-code');
                    return false;
                }
                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                alertModal.modal("show");
            });
        });
        @else
        $('.btn-show-code').click(function(){
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            $.post('{{ route("profile.show-two-factor-code") }}', {_token: "{{ csrf_token() }}"}, function(e){
                location.reload();
            }).fail(function(xhr) {
                btn.html(initialText).removeClass("disabled").prop("disabled",false);
                if(xhr.status==401){
                    confirm_password('.btn-show-code');
                    return false;
                }
                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                alertModal.modal("show");
            });
        });
        @endif
        $('.btn-disable-two-factor').click(function(){
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            $.post('{{ route("profile.disable-two-factor") }}', {_token: "{{ csrf_token() }}"}, function(e){
                if(!e.status){
                    btn.html(initialText).removeClass("disabled").prop("disabled",false);
                    if(typeof e.code !== 'undefined' && e.code == 'confirm_password'){
                        confirm_password('.btn-disable-two-factor');
                        return false;
                    }
                }
                location.reload();
            }).fail(function(xhr) {
                btn.html(initialText).removeClass("disabled").prop("disabled",false);
                if(xhr.status==401){
                    confirm_password('.btn-disable-two-factor');
                    return false;
                }
                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                alertModal.modal("show");
            });
        });
        @endif

        $('.btn-logout-other-devices').click(function(){
            confirmModal.find('.modal-title > span').text('Log Out Other Browser Sessions');
            confirmModal.find('.modal-body').html('<p class="mb-5">Please enter your password to confirm</p><input type="password" class="form-control" id="confirm-password" placeholder="Current Password" maxlength="50">');
            confirmModal.find('.modal-footer > .btn-modal-action').attr('data-action', 'logout-devices');
            confirmModal.modal('show');
        });

        confirmModal.find('.btn-modal-action').click(function(){
            var $password = $('#confirm-password');
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            if(btn.attr('data-action')=='logout-devices'){
                $.post('{{ route("profile.logout-other-devices") }}', {_token: "{{ csrf_token() }}",password:$password.val()}, function(e){
                    if(!e.status){
                        $password.val('').focus();
                        btn.html(initialText).removeClass("disabled").prop("disabled",false);
                        $('#confirm-error-text').remove();
                        $('<p/>',{
                            id: 'confirm-error-text',
                            class: 'mt-2 text-danger',
                            text: e.message
                        }).insertAfter($('#confirm-password'));
                        return false;
                    }
                    confirmModal.modal('hide');
                    location.reload();
                }).fail(function(xhr) {
                    if(typeof xhr.responseJSON.code !== 'undefined' && xhr.responseJSON.code=='invalid_password'){
                        $password.val('').focus();
                        btn.html(initialText).removeClass("disabled").prop("disabled",false);
                        $('#confirm-error-text').remove();
                        $('<p/>',{
                            id: 'confirm-error-text',
                            class: 'mt-2 text-danger',
                            text: xhr.responseJSON.message
                        }).insertAfter($('#confirm-password'));
                        return false;
                    }
                    confirmModal.modal('hide');
                    alertModal.find(".modal-body").text(typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something error when processing ajax');
                    alertModal.modal("show");
                });
            }
            if(btn.attr('data-action')=='confirm-password'){
                $.post('{{ route("profile.confirm-password") }}', {_token: "{{ csrf_token() }}",password:$password.val(),trigger:btn.attr('data-params')}, function(e){
                    if(!e.status){
                        $password.val('').focus();
                        btn.html(initialText).removeClass("disabled").prop("disabled",false);
                        $('#confirm-error-text').remove();
                        $('<p/>',{
                            id: 'confirm-error-text',
                            class: 'mt-2 text-danger',
                            text: e.message
                        }).insertAfter($('#confirm-password'));
                        return false;
                    }
                    confirmModal.modal('hide');
                    $(e.data.confirmedPassword).trigger('click');
                }).fail(function(xhr) {
                    if(typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON.code=='invalid_password'){
                        $password.val('').focus();
                        btn.html(initialText).removeClass("disabled").prop("disabled",false);
                        $('#confirm-error-text').remove();
                        $('<p/>',{
                            id: 'confirm-error-text',
                            class: 'mt-2 text-danger',
                            text: xhr.responseJSON.message
                        }).insertAfter($('#confirm-password'));
                        return false;
                    }
                    confirmModal.modal('hide');
                    alertModal.find(".modal-body").text(typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something error when processing ajax');
                    alertModal.modal("show");
                });
            }
        });

        confirmModal.on('shown.bs.modal', function (e) {
            var $this=$(this);
            $this.find('#confirm-password').focus();
            $("#confirm-password").keyup(function(e){
                var code = e.key;
                if(code==="Enter"){
                    console.log(code);
                    $this.find('.btn-modal-action').trigger('click');
                }
            });
        });
    });
</script>
@endsection
