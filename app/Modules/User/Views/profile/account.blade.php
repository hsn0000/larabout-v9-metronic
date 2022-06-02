<form action="{{ route('profile.update') }}" method="post" autocomplete="off" enctype="multipart/form-data">
    @csrf
    <div class="card card-custom card-stretch">
        <!--begin::Header-->
        <div class="card-header py-3">
            <div class="card-title align-items-start flex-column">
                <h3 class="card-label font-weight-bolder text-dark">Account Information</h3>
                <span class="text-muted font-weight-bold font-size-sm mt-1">Update your account informaiton</span>
            </div>
            <div class="card-toolbar">
                <button type="submit" class="btn btn-success mr-2">Save Changes</button>
                <button type="reset" class="btn btn-secondary">Cancel</button>
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
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">Avatar</h5>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label"></label>
                <div class="col-lg-9 col-xl-6">
                    <div class="image-input image-input-outline" id="kt_profile_avatar" style="">
                        <div class="image-input-wrapper" style="background-image: url({{ Auth::user()->profile_photo_url }})"></div>
                        <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                            <i class="fa fa-pen icon-sm text-muted"></i>
                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg, .gif" />
                            <input type="hidden" name="profile_avatar_remove" />
                        </label>
                        <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                            <i class="ki ki-bold-close icon-xs text-muted"></i>
                        </span>
                    </div>
                    <span class="form-text text-muted">Allowed file types: png, jpg, jpeg, gif. Max size : 5MB</span>
                    @error('avatar')
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">{{ $message }}</div>
                        </div>
                    @enderror

                    @if (Auth::user()->profile_photo_path)
                        <button type="button" class="btn btn-sm btn-danger mt-2 btn-loading btn-remove-avatar">Remove Avatar</button>
                    @endif
                </div>
            </div>
            <div class="separator separator-dashed separator-border-2 mb-4 mt-4"></div>
            @endif

            <div class="row">
                <label class="col-xl-3"></label>
                <div class="col-lg-9 col-xl-6">
                    <h5 class="font-weight-bold mb-6">Contact Info</h5>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-xl-3 col-lg-3 col-form-label">Email Address</label>
                <div class="col-lg-9 col-xl-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="la la-at"></i>
                            </span>
                        </div>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ Auth::user()->email }}" name="email" placeholder="Email" />
                    </div>
                    @error('email')
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

@section('content_js')
<script>
    $(function(){
        $('.btn-remove-avatar').click(function(){
            var btn = $(this),
            initialText = btn.attr("data-initial-text"),
            loadingText = btn.attr("data-loading-text");
            btn.html(loadingText).addClass("disabled").prop("disabled",true);
            $.post("{{ route('profile.remove-avatar') }}",{_token: "{{csrf_token()}}"}, function(e){
                if(!e.status){
                    alertModal.find(".modal-title > span").text("ERROR");
                    alertModal.find(".modal-body").text(x.responseJSON.message ? x.responseJSON.message : "Something error when processing the data");
                    alertModal.modal("show");
                }
                location.reload();
            }).fail(function(xhr) {
                alertModal.find(".modal-body").text(xhr.responseJSON.message);
                alertModal.modal("show");
            });
        });
    })
</script>
@endsection
