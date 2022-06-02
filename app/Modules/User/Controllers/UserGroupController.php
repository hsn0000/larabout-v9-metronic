<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\UserGroup;

class UserGroupController extends Controller
{
    /* set default mod */  
    protected $mod_alias = 'user-group';
    protected $mod_active = 'master,user-data,user-group';

    /**
     * user group page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
    */
    public function index(Request $request)
    {
        /*
        * ceck access
        */
        $this->page->blocked_page($this->mod_alias);
        /*
        * set viewdata
        */ 
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['get_data'] = UserGroup::get();
        $this->viewdata['page_title'] = 'User Group';
        
        return view('User::group.table', $this->viewdata);
    }

    /**
     * add user group page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['page_title'] = 'User Group > Add';

        return view('User::group.add', $this->viewdata);
    }

    /**
     * save user group
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
        $request->validate([
            'group' => [
                'required',
                'max:50',
                Rule::unique('user_group'),
            ],
        ]);

        $roles = NULL;
        if(isset($request->roles) && is_array($request->roles))
        {
            foreach($request->roles as $role => $mod)
            {
                $roles[$role] = implode(',', $mod);
            }

            $roles = json_encode($roles);
        }

        /**
         * Insert data
         */
        $insert = [
            'group' => ucwords($request->group),
            'roles' => $roles
        ];

        UserRepository()->insert_user_group($insert);

        return redirect(route('user-group'))->with(['msg_success' => 'Data with Name '.$request->group.' has been saved']);
    }

    /**
     * edit user group page
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
        * get data user group
        */ 
        $data = UserGroup::find($id);

        if(!$data)
        {
            /*
            * if data not found
            */ 
            return redirect(route('user-group'))->with(['msg_error' => 'Data with ID '.$id.' not found!']);
        }
        /*
        * define variable
        */ 
        $modules = config('modules');
        $roles = json_decode($data->roles);
        /*
        * set viewdata
        */ 
        $this->viewdata['data'] = $data;
        $this->viewdata['modules'] = (array) json_decode(json_encode($modules));
        $this->viewdata['roles'] = $roles;
        $this->viewdata['mod_alias'] = $this->mod_alias;
        $this->viewdata['mod_active'] = $this->mod_active;
        $this->viewdata['page_title'] = 'User Group > Edit';

        return view('User::group.edit', $this->viewdata);
    }

    /**
     * update user group
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function update(Request $request)
    {
        /*
        * check access
        */ 
        $this->page->blocked_page($this->mod_alias, 'update');
        /*
        * set validate
        */ 
        $request->validate([
            'id' => 'required:numeric',
            'group' => [
                'required',
                'max:50',
                Rule::unique('user_group')->ignore($request->id, 'guid'),
            ],
        ]);
        /*
        * get user group data
        */ 
        $data = UserGroup::find($request->id);
        if(!$data)
        {
            /*
            * if data not found
            */ 
            return redirect(route('user'))->with(['msg_error' => 'Data with ID '.$request->id.' not found!']);
        }

        $roles = NULL;
        if(isset($request->roles) && is_array($request->roles))
        {
            foreach($request->roles as $role => $mod)
            {
                $roles[$role] = implode(',', $mod);
            }

            $roles = json_encode($roles);
        }

        /**
         * Update data
         */
        $update = [
            'group' => ucwords($request->group),
            'roles' => $roles
        ];

        UserRepository()->update_user_group($request->id, $update);

        return redirect(route('user-group'))->with(['msg_success' => 'Data with ID '.$request->id.' has been updated']);
    }

    /**
     * delete user group
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        /*
        * get data user group
        */ 
        $data = UserGroup::find($id);

        if(!$data)
        {
            /*
            * if data not found
            */ 
            return redirect(route('user-group'))->with(['msg_error' => 'Data with ID '.$id.' not found!']);
        }

        if(Auth::user()->guid == $id)
        {
            /*
            * if current user same with id 
            */ 
            return redirect(route('user-group'))->with(['msg_error' => "You're on this group, can't delete right now"]);
        }

        if($id <= 3)
        {
            /*
            * if user main group
            */ 
            return redirect(route('user-group'))->with(['msg_error' => "This group is main group, can't delete!"]);
        }

        /**
         * Delete data
         */
        UserRepository()->delete_user_group($id);

        return redirect(route('user-group'))->with(['msg_success' => 'The Group '.$data->group.' has been deleted']);
    }

}