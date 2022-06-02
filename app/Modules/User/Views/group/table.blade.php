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
                            <i class="flaticon-users-1 text-primary"></i>
                        </span>
                        <h3 class="card-label">{{ $page_title }}
                        <small>Settings for Roles User</small></h3>
                    </div>
                    <div class="card-toolbar">
                        @if($page->mod_action_roles($mod_alias, 'create'))
                        <a href="{{ route('user-group.add') }}" class="btn btn-sm btn-primary font-weight-bold">
                            <i class="flaticon2-plus-1"></i>Add Group
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

                            <div class="table-responsive">
                                <table class="table table-head-custom table-head-bg table-vertical-center">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;" rowspan="2">#</th>
                                            <th class="" style="width: 15%;" rowspan="2">Group</th>
                                            <th class="text-center" style="width: 10%;" rowspan="2">Users</th>
                                            <th class="text-center" colspan="4">Roles Module</th>
                                            <th class="text-center" style="width: 10%;"  rowspan="2">Action</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Create</th>
                                            <th class="text-center">Update</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($get_data as $key => $val)
                                        @php
                                            $roles = json_decode($val->roles);
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ ++$key }}</td>
                                            <td>{{ $val->group }}</td>
                                            <td class="text-center">{{ custom_numfor($page->total_users_in_group($val->guid)) }}</td>
                                            <td class="text-center">{{ isset($roles->view) ? count(explode(',',$roles->view)) : 0 }}</td>
                                            <td class="text-center">{{ isset($roles->create) ? count(explode(',',$roles->create)) : 0 }}</td>
                                            <td class="text-center">{{ isset($roles->update) ? count(explode(',',$roles->update)) : 0 }}</td>
                                            <td class="text-center">{{ isset($roles->delete) ? count(explode(',',$roles->delete)) : 0 }}</td>
                                            <td class="text-center">
                                                {{ view('Home::editButton', ['page' => $page, 'mod_alias' => $mod_alias, 'url' => route('user-group.edit', ['id' => $val->guid])]) }}
                                                {{ view('Home::deleteButton', ['page' => $page, 'mod_alias' => $mod_alias, 'url' => route('user-group.delete', ['id' => $val->guid])]) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
