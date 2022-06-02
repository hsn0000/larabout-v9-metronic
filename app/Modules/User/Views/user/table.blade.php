@extends('layouts.template.app')

@section('content_body')
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="card card-custom gutter-b">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-icon">
                            <i class="flaticon2-user text-primary"></i>
                        </span>
                        <h3 class="card-label"> {{ $page_title }}
                    </div>
                    <div class="card-toolbar">
                        @if($page->mod_action_roles($mod_alias, 'create'))
                        <a href="{{ route('user.add') }}" class="btn btn-sm btn-primary font-weight-bold">
                            <i class="flaticon2-plus-1"></i>Add Data
                        </a>
                        @endif
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

                            <div class="mb-7">
                                <div class="row align-items-center">
                                    <div class="col-lg-9 col-xl-8">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 col-xxl-6 col-lg-8 my-2 my-md-0">
                                                <div class="d-xxl-flex">
                                                    <button class="btn btn-light-success font-weight-bold mr-2 mb-4 mb-lg-2 mb-xl-0 mb-xxl-0" type="button" id="kt_datatable_reload"><i class="flaticon2-refresh"></i> Reload Data</button>
                                                    <div class="input-icon flex-grow-1">
                                                        <input type="text" class="form-control" placeholder="Search..." id="dt_search_query" name="search" autocomplete="off" />
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-4 my-2 my-md-0">
                                                <select class="form-control selectpicker" id="kt_datatable_search_group" title="">
                                                    <option value="" data-icon="select-icon flaticon-users">All Group</option>
                                                    @foreach ($groups as $val)
                                                    <option value="{{ $val->guid }}">{{ $val->group }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! $table->table(['class' => 'datatable datatable-bordered datatable-head-custom show-table', 'selector' => TRUE]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('lib_js')

@endsection

@section('content_js')
{!! $table->scripts() !!}
<script>
    $(function(){
        var dt = $('#kt-datatable').KTDatatable();
        $("#kt_datatable_search_group").on("change", function () {
            dt.search($(this).val().toLowerCase(), "group");
        });
    });
</script>
@endsection
