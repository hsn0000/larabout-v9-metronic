@extends('layouts.template.app')
@section('lib_css')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>
<style>
.flex-stack{ justify-content: space-between; align-items: center;} .me-4{ margin-right: 1rem !important;} .w-30px{ width: 30px !important;}
.text-gray-400{ color: #474761 !important;} .text-gray-600{ color: #6D6D80 !important;} .text-gray-800{ color: #CDCDDE !important;} .fw-bolder{ font-weight: 600 !important;} .fs-4{ font-size: 1.25rem !important;} .me-3{ margin-right: 0.75rem !important;} .separator.separator-dashed{ border-bottom: 1px dashed #323248;} .card .card-header .card-toolbar{ display: flex; align-items: center; margin: 0.5rem 0; flex-wrap: wrap;}
.dataTables_wrapper .dataTable thead th{ border-bottom-color: #dee2e6 !important;}
</style>
@endsection
@section('content_body')
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-custom bgi-no-repeat bgi-size-cover gutter-b"
                    style="background-color: #1B283F; background-position: calc(100% + 0.5rem) calc(100% + 0.5rem); background-size: 600px auto; background-image: url({{ asset('template/media/svg/patterns/rhone-2.svg') }})">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex py-5 flex-column align-items-start flex-grow-1">
                                    <div class="flex-grow-1">
                                        <p class="text-success font-weight-bolder font-size-h3">Welcome to
                                            {{ config('app.name') }}</p>
                                        <p class="text-success opacity-75 font-weight-bold mt-3">Create something useful</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!--end::Container-->
</div>
<!--end::Entry-->
@endsection

@section('content_js')
<script>
// $(document).ready(function() {
//     $('.carousel').carousel()
// })
</script>
@endsection