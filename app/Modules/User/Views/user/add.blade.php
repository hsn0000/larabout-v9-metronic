@extends('layouts.template.app')

@section('content_body')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form class="form" method="post" action="{{ route('user.save') }}" autocomplete="off">
                    @csrf
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                                <span class="card-icon">
                                    <i class="flaticon-users-1 text-primary"></i>
                                </span>
                                <h3 class="card-label"> {{ $page_title }}
                                <small>Credential</small></h3>
                            </div>
                            <div class="card-toolbar">
                                @if($page->mod_action_roles($mod_alias, 'create'))
                                <button type="submit" class="btn btn-sm btn-warning font-weight-bold">
                                    <i class="flaticon2-hourglass-1"></i>Save
                                </button>
                                @endif
                                <a href="{{ route('user') }}" class="btn btn-sm btn-danger font-weight-bold ml-2"><i class="flaticon-close"></i> Cancel</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @if (session('msg_error'))
                                        <div class="alert alert-danger">
                                            {{ session('msg_error') }}
                                        </div>
                                    @endif
                                    @if (session('msg_success'))
                                        <div class="alert alert-success">
                                            {{ session('msg_success') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-xxl-2 col-form-label required">Group</label>
                                        <div class="col-xxl-4 col-md-6">
                                            <select name="group" class="form-control selectpicker @error('group') is-invalid @enderror">
                                                <option value="">-- Select Group --</option>
                                                @foreach ($groups as $val)
                                                <option value="{{ $val->guid }}" {{ $val->guid == old('group') ? 'selected' : ''}}>{{ $val->group }}</option>
                                                @endforeach
                                            </select>
                                            @error('group')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-xxl-2 col-form-label required">Username</label>
                                        <div class="col-xxl-4 col-md-6">
                                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') ?: '' }}">
                                            <input type="hidden" name="id" readonly class="form-control" value="{{ request()->id }}">
                                            @error('name')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-xxl-2 col-form-label required">Email</label>
                                        <div class="col-xxl-4 col-md-6">
                                            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') ?: '' }}">
                                            @error('email')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-xxl-2 col-form-label required">New Password</label>
                                        <div class="col-xxl-4 col-md-6">
                                            <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" value="">
                                            @error('password')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-xxl-2 col-form-label required">Confirm Password</label>
                                        <div class="col-xxl-4 col-md-6">
                                            <input class="form-control @error('repassword') is-invalid @enderror" type="password" name="repassword" value="">
                                            @error('repassword')
                                                <div class="fv-plugins-message-container">
                                                    <div class="fv-help-block">{{ $message }}</div>
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
