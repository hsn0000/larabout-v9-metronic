@extends('layouts.template.app')

@section('content_body')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <form class="form" method="post" action="{{ route('user-group.save') }}" autocomplete="off">
            @csrf
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon-users-1 text-primary"></i>
                        </span>
                        <h3 class="card-label"> {{ $page_title }}
                    </div>
                    <div class="card-toolbar">
                        @if($page->mod_action_roles($mod_alias, 'create'))
                        <button type="submit" class="btn btn-sm btn-warning font-weight-bold">
                            <i class="flaticon2-hourglass-1"></i>Save
                        </button>
                        @endif
                        <a href="{{ route('user-group') }}" class="btn btn-sm btn-danger font-weight-bold ml-2"><i class="flaticon-close"></i> Cancel</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (session('msg_error'))
                                <x-alert type="danger" :message="session('msg_error')" dismissible="TRUE"/>
                            @endif
                            @if (session('msg_success'))
                                <x-alert type="success" :message="session('msg_success')" dismissible="TRUE"/>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label class="col-xxl-2 col-form-label required">Group Name</label>
                                <div class="col-xxl-10">
                                    <input class="form-control @error('group') is-invalid @enderror" type="text" name="group" value="{{ old('group') ?: NULL }}">
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
                            <div class="separator separator-dashed separator-border-2 mb-4"></div>
                            <div class="h4">Modules</div>
                            <div class="separator separator-dashed separator-border-2 mb-10 mt-4"></div>
                            {!! $page->module_roles(['set' => '', 'active' => '']) !!}
                    </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col text-right">
                            @if($page->mod_action_roles($mod_alias, 'create'))
                            <button type="submit" class="btn btn-sm btn-warning font-weight-bold">
                                <i class="flaticon2-hourglass-1"></i>Save
                            </button>
                            @endif
                            <a href="{{ route('user-group') }}" class="btn btn-sm btn-danger font-weight-bold ml-2"><i class="flaticon-close"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection
