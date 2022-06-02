<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\Rules\Password;

use App\Library\Services\Datatables;
use App\Models\User;
use App\Models\UserGroup;

class UserController extends Controller
{
    /* set default mod */  
    protected $mod_alias = 'user';
    protected $mod_active = 'master,user-data,user';

    /**
     * user page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
    */
    public function index(Datatables $datatables, Request $request)
    {
        /*
        * ceck access
        */ 
        $this->page->blocked_page($this->mod_alias);
        /*
        * get data user group
        */ 
        $groups = UserGroup::select('guid','group')->get();
        foreach($groups as $val)
        {
            $_groups[] = [
                'id' => $val->guid,
                'value' => $val->group
            ];
        }
        /*
        * set up databases
        */ 
        $datatables->config([
            'roles' => [
                'delete' => $this->page->mod_action_roles($this->mod_alias, 'delete'),
                'update' => $this->page->mod_action_roles($this->mod_alias, 'update'),
            ],
            'updates' => [
                [
                    'title' => 'Update Group',
                    'data' => $_groups,
                    'width' => '160px',
                    'url' => route('user.update-group')
                ]
            ],
            'delete_url' => route('user.delete')
        ]);

        $datatables->options([
            'url' => route('user.dt')
        ]);

        $table = $datatables->columns([
            ['field' => 'data_id', 'title' => '#', 'selector' => TRUE, 'sortable' => FALSE, 'width' => 20, 'textAlign' => 'center'],
            ['field' => 'name', 'title' => 'Username', 'template' => "return '<div class=\"d-flex align-items-center\">
            <div class=\"symbol symbol-40 flex-shrink-0 mr-2\">
                <div class=\"symbol-label\" style=\"background-image:url(' + data.avatar + ')\"></div>
            </div>
            <div class=\"ml-2\">
                <div class=\"text-dark-75 font-weight-bold line-height-sm\">' + data.name + '</div>
                <a href=\"mailto:'+ data.email +'\" class=\"font-size-sm text-muted text-hover-primary\">' + data.email + '</a>
            </div>'"],
            ['field' => 'guid', 'title' => 'Group'],
            ['field' => 'created_at', 'title' => 'Created'],
            ['field' => '', 'title' => 'Action', 'width' => 60, 'textAlign' => 'center', 'template' => "return '".view('Home::editButton', ['page' => $this->page, 'mod_alias' => $this->mod_alias, 'dt' => TRUE, 'url' => route('user.edit', ['id' => TRUE])])->render()."'"]
        ]);
        /*
        * set viewdata
        */ 
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['table'] = $table;
        $this->viewdata['groups'] = $groups;
        $this->viewdata['page_title'] = 'User';

        return view('User::user.table', $this->viewdata);
    }

    /**
     * generate data table
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
    */
    public function data_table(Request $request)
    {
        /*
        * set define limit page variable
        */ 
        $limit = (int) ($request->pagination['perpage'] && is_numeric($request->pagination['perpage']) ? $request->pagination['perpage'] : 5);
        $page  = (int) ($request->pagination['page'] && is_numeric($request->pagination['page']) ? $request->pagination['page'] : 0);
        $offset = $page > 0 ? (($page - 1) * $limit) : 0;
        /*
        * get data user table
        */ 
        $get_data = UserRepository()->get_users_table();
        /*
        * define variable input query
        */ 
        $query = $request->input('query');
        if(!empty($query))
        {
            /*
            * if input query exist 
            */ 
            if(isset($query['generalSearch']))
            {
                $query = $query['generalSearch'];
                $get_data->whereRaw("u.name like '%".$query."%' OR u.email like '".$query."%'");
            }

            if(isset($query['group']))
            {
                $get_data->whereRaw("u.guid = ".$query['group']);
            }
        }
        /*
        * generate sort filtering data
        */ 
        if(isset($request->sort['field']))
        {
            $get_data->orderBy('u.'.$request->sort['field'], $request->sort['sort']);
        }
        else
        {
            $get_data->orderBy('u.created_at', 'desc');
        }
        /*
        * set define variable data
        */ 
        $total_data = $get_data->count();
        $_data = $rowIds = [];

        if($total_data > 0)
        {
            $no = ($page - 1) * $limit;
            foreach($get_data->limit($limit)->offset($offset)->get() as $key => $val)
            {
                $_data[] = [
                    'no' => ++$no,
                    'data_id' => $val->id,
                    'name' => $val->name,
                    'email' => $val->email,
                    'guid' => $val->group,
                    'avatar' => User::find($val->id)->profile_photo_url,//$val->profile_photo_path ? Storage::url($val->profile_photo_path) : 'https://ui-avatars.com/api/?name='.$val->name.'&color=7F9CF5&background=EBF4FF',
                    'created_at' => format_date($val->created_at, 'full_date_time')
                ];

                $rowIds[] = $val->id;
            }
        }
        /*
        * ajax response datatable
        */ 
        return [
            'meta' => [
                'page' => $page,
                'pages' => ceil($total_data / $limit),
                'perpage' => $limit,
                'total' => $total_data,
                'sort' => $request->sort['sort'] ?? 'asc',
                'field' => $request->sort['field'] ?? 'created_at',
                'rowIds' => $rowIds
            ],
            'data' => $_data
        ];
    }

    /**
     * add user page
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View|\Laravel\Lumen\Application
     * @throws \Exception
     */
    public function add()
    {
        /*
        * check access
        */ 
        $this->page->blocked_page($this->mod_alias, 'create');
        /*
        * set viewdata
        */ 
        $this->viewdata['groups'] = UserGroup::all();
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['page_title'] = 'User > Add';

        return view('User::user.add', $this->viewdata);
    }

    /**
     * save user
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function save(Request $request)
    {         
        /*
        * check access
        */ 
        $this->page->blocked_page($this->mod_alias, 'create');
        /*
        * set validation
        */ 
        $pass_required = (new Password)->length(6)->requireUppercase()->requireSpecialCharacter()->requireNumeric();
        $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('users'),
            ],
            'email' => [
                'required',
                'max:50',
                Rule::unique('users'),
            ],
            'password' => $pass_required,
            'repassword' => $pass_required,
            'group' => 'required|numeric'
        ]);

        /**
         * Insert data
         */
        $insert = [
            'name' => ucwords($request->name),
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'guid' => $request->group,
        ];

        UserRepository()->insert_users($insert);

        return redirect(route('user'))->with(['msg_success' => 'Data with Name '.ucwords($request->name).' has been saved']);
    }

    /**
     * edit user page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
     */
    public function edit($id)
    {
        /*
        * check access
        */ 
        $this->page->blocked_page($this->mod_alias, 'update');
        /*
        * get user data
        */ 
        $data = User::find($id);

        if(!$data)
        {
            /*
            * if data not found
            */ 
            return redirect(route('user'))->with(['msg_error' => 'Data with ID '.$id.' not found!']);
        }

        if($id == Auth::user()->id)
        {
            /*
            * if id is current users
            */ 
            return redirect(route('profile.show'));
        }
        /*
        * set viewdata
        */ 
        $this->viewdata['data'] = $data;
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['groups'] = UserGroup::all();
        $this->viewdata['page_title'] = 'User > Edit';

        return view('User::user.edit', $this->viewdata);
    }

    /**
     * update user
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(Request $request, $params = '')
    {
        /*
        * check access
        */ 
        $this->page->blocked_page($this->mod_alias, 'update');
        /*
        * set validate
        */ 
        $request->validate([
            'id' => 'required:numeric'
        ]);
        /*
        * get user data
        */ 
        $data = User::find($request->id);
        if(!$data)
        {
            /*
            * if data not found
            */ 
            return redirect(route('user'))->with(['msg_error' => 'Data with ID '.$request->id.' not found!']);
        }

        if($params)
        {
            /*
            * if request change password
            */ 
            $pass_required = (new Password)->length(6)->requireUppercase()->requireSpecialCharacter()->requireNumeric();
            $request->validate([
                'password' => $pass_required,
                'repassword' => $pass_required
            ]);

            if($request->password != $request->repassword)
            {
                return redirect(route('user'))->with(['msg_error' => 'The confirm password not match']);
            }

            $update = [
                'password' => Hash::make($request->password),
            ];

            $update_message = 'The password user '.$data->name.' has been updated';
        }
        else
        {
            /*
            * if request not with password
            */ 
            $request->validate([
                'email' => [
                    'required',
                    'max:50',
                    Rule::unique('users')->ignore($request->id, 'id'),
                ],
                'group' => 'required|numeric'
            ]);

            $update = [
                'email' => strtolower($request->email),
                'guid' => $request->group,
            ];

            $update_message = 'Data user '.$data->name.' has been updated';
        }

        /**
         * Update data
         */
        UserRepository()->update_users($request->id, $update);

        return redirect(route('user'))->with(['msg_success' => $update_message]);
    }

    /**
     * update group on user
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update_group(Request $request)
    {
        if(!$request->id)
        {
            /*
            * if request id empty 
            */ 
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid ID'
            ],400);
        }

        if(in_array(Auth::user()->id, $request->id))
        {
            /*
            * if the update is the current user
            */ 
            return response()->json([
                'status' => FALSE,
                'message' => "You are not allowed for change your user group!"
            ],400);
        }

        /**
         * Update data
         */
        $total = 0;
        if($request->update_id > 0)
        {
            foreach($request->id as $id)
            {
                $update = UserRepository()->update_users($id, ['guid' => $request->update_id]);

                if($update)
                {
                    $total += 1;
                }
            }
        }

        return response()->json([
            'status' => TRUE,
            'message' => $total.' Data has been updated'
        ]);
    }

    /**
     * delete user group
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if(!$request->id)
        {
            /*
            * if request id is empty
            */ 
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid ID'
            ],400);
        }

        if(in_array(Auth::user()->id, $request->id))
        {
            /*
            * if the update is the current user
            */ 
            return response()->json([
                'status' => FALSE,
                'message' => "You are not allowed for delete your account!"
            ],400);
        }

        /**
         * Delete data
         */
        $total = 0;
        foreach($request->id as $id)
        {
            $delete = UserRepository()->delete_users($id);

            if($delete)
            {
                $total += 1;
            }
        }

        return response()->json([
            'status' => TRUE,
            'message' => $total.' Data has been deleted'
        ]);
    }
}