<form action="{{ route('profile.update-password') }}" method="post" autocomplete="off" enctype="multipart/form-data">
    @csrf
    <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
                <h3 class="card-label font-weight-bolder text-dark">Change Password</h3>
                <span class="text-muted font-weight-bold font-size-sm mt-1">Change your account password</span>
            </div>
            <div class="card-toolbar">
                <button type="submit" class="btn btn-success mr-2">Save Changes</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
            </div>
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">
            @if(session('msg_error'))
                <x-alert type="danger" :message="session('msg_error')" dismissible="TRUE"/>
            @endif
            @if(session('msg_success'))
                <x-alert type="success" :message="session('msg_success')" dismissible="TRUE"/>
            @endif
            <div class="alert alert-custom alert-notice alert-light-info" role="alert">
                <div class="alert-icon">
                    <span class="svg-icon svg-icon-info svg-icon-2x">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10"/>
                                <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1"/>
                                <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1"/>
                            </g>
                        </svg>
                    </span>
                </div>
                <div class="alert-text">This action will make all devices logout</div>
            </div>
            <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">Current Password</label>
                <div class="col-lg-9 col-xl-6">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" value="" name="password" />
                    @error('password')
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">{{ $message }}</div>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">New Password</label>
                <div class="col-lg-9 col-xl-6">
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" value="" name="new_password" />
                    @error('new_password')
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">{{ $message }}</div>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">Confirm New Password</label>
                <div class="col-lg-9 col-xl-6">
                    <input type="password" class="form-control @error('repassword') is-invalid @enderror" value="" name="repassword" />
                    @error('repassword')
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">{{ $message }}</div>
                        </div>
                    @enderror
                </div>
            </div>
        </div>
        <!--end::Body-->
    </div>
</form>
