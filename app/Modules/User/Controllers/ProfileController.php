<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Password;
use Laravel\Fortify\Features;

use App\Library\Services\BrowserSession;
use App\Library\Services\TwoFactor;

class ProfileController extends Controller
{
    /**
     * user profile page
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Exception
    */
    public function index(Request $request)
    {
        /*
        * set viewdata
        */ 
        $this->viewdata['page_title'] = 'Account Settings';
        $this->viewdata['profile_content'] = 'User::profile.account';
        
        return view('User::profile.content', $this->viewdata);
    }

    /**
     * update profile
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function update(Request $request)
    {
        /*
        * set validate
        */ 
        $request->validate([
            'avatar' => 'mimes:jpg,jpeg,png,gif|max:5120',
            'email' => [
                'required',
                'max:50',
                Rule::unique('users')->ignore(Auth::user()->id, 'id'),
            ]
        ]);
        /*
        * define variable
        */ 
        $storage_paths = storage_path('app/public/profile-photos');
        $storage_folder = last(explode('/', $storage_paths));
        $avatar = Auth::user()->profile_photo_path;

        if($request->has('avatar'))
        {
            /**
             * Delete old avatar
             */
            $profile_photo_path = str_replace($storage_folder.'/', '', $avatar);
            if(file_exists($storage_paths.'/'.$profile_photo_path))
            {
                @unlink($storage_paths.'/'.$profile_photo_path);
            }

            $avatar = Str::slug(random_string('alnum', 40)).'.'.request()->avatar->getClientOriginalExtension();
            request()->avatar->move($storage_paths, $avatar);

            $avatar = $storage_folder.'/'.$avatar;
        }

        /**
         * Update data
         */
        $update = [
            'email' => strtolower($request->email),
            'profile_photo_path' => $avatar
        ];

        UserRepository()->update_users(Auth::user()->id, $update);

        return redirect(route('profile.show'))->with(['msg_success' => "Your account has been updated"]);
    }

    /**
     * change password
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function change_pass(Request $request)
    {
        if(!Features::enabled(Features::updatePasswords()))
        {
            return redirect()->route('profile.show');
        }
        /*
        * set viewdata
        */ 
        $this->viewdata['page_title'] = 'Account Settings';
        $this->viewdata['profile_content'] = 'User::profile.change_pass';

        return view('User::profile.content', $this->viewdata);
    }

    /**
     * update password
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function update_pass(Request $request) 
    {
        if(!Features::enabled(Features::updatePasswords()))
        {
            return redirect()->route('profile.show');
        }
        /*
        * set validate
        */ 
        $pass_required = (new Password)->length(6)->requireUppercase()->requireSpecialCharacter()->requireNumeric();
        $request->validate([
            'password' => 'required|min:6|max:20',
            'new_password' => $pass_required,
            'repassword' => $pass_required
        ]);

        if(!Hash::check($request->password, Auth::user()->password))
        {
            return redirect(route('profile.change-password'))->with(['msg_error' => 'Current password not match']);
        }

        if($request->new_password != $request->repassword)
        {
            return redirect(route('profile.change-password'))->with(['msg_error' => 'The confirm password not match']);
        }

        /**
         * Update data
         */
        $update = [
            'password' => Hash::make($request->new_password),
            'updated_at' => \Carbon\Carbon::now()
        ];

        UserRepository()->update_users(Auth::user()->id, $update);

        return redirect(route('profile.change-password'))->with(['msg_success' => "Your password has been changed"]);
    }

    /**
     * security account
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function security(BrowserSession $browser, TwoFactor $two_factor, Request $request)
    {
        /*
        * define variable
        */ 
        $enabled_two_factor = $two_factor->getEnabledProperty();
        /*
        * set viewdata
        */ 
        $this->viewdata['enabled_two_factor'] = session('enabled_two_factor') || $enabled_two_factor ? TRUE : FALSE;
        $this->viewdata['showingQrCode'] = session('enabled_two_factor') || $two_factor->showingQrCode ? TRUE : FALSE;
        $this->viewdata['showingRecoveryCodes'] = session('showingRecoveryCodes') || $two_factor->showingRecoveryCodes ? TRUE : FALSE;
        $this->viewdata['browser'] = $browser->getSessionsProperty();
        $this->viewdata['page_title'] = 'Account Settings';
        $this->viewdata['profile_content'] = 'User::profile.security';

        return view('User::profile.content', $this->viewdata);
    }

    /**
     * enable two factor
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function enable_two_factor(TwoFactor $two_factor)
    {
        if(!Features::canManageTwoFactorAuthentication())
        {
            return response()->json([
                'status' => FALSE,
                'message' => 'Disabled feature'
            ],404);
        }

        $two_factor->enableTwoFactorAuthentication();

        if($two_factor->showingQrCode)
        {
            session()->flash('enabled_two_factor', TRUE);
        }

        if($two_factor->showingRecoveryCodes)
        {
            session()->flash('showingRecoveryCodes', TRUE);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'OK'
        ]);
    }

    /**
     * generate two factor code
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function generate_two_factor_code(TwoFactor $two_factor)
    {
        if(!Features::canManageTwoFactorAuthentication())
        {
            return response()->json([
                'status' => FALSE,
                'message' => 'Disabled feature'
            ],404);
        }

        $two_factor->regenerateRecoveryCodes();

        if($two_factor->showingRecoveryCodes)
        {
            session()->flash('showingRecoveryCodes', TRUE);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'OK'
        ]);
    }

    /**
     * show two factor code
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function show_two_factor_code(TwoFactor $two_factor)
    {
        if(!Features::canManageTwoFactorAuthentication())
        {
            return response()->json([
                'status' => FALSE,
                'message' => 'Disabled feature'
            ],404);
        }

        $two_factor->showRecoveryCodes();

        if($two_factor->showingRecoveryCodes)
        {
            session()->flash('showingRecoveryCodes', TRUE);
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'OK'
        ]);
    }
    
    /**
     * disable two factor
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function disable_two_factor(TwoFactor $two_factor)
    {
        if(!Features::canManageTwoFactorAuthentication())
        {
            return response()->json([
                'status' => FALSE,
                'message' => 'Disabled feature'
            ],404);
        }

        $two_factor->disableTwoFactorAuthentication();

        return response()->json([
            'status' => TRUE,
            'message' => 'OK'
        ]);
    }

    /**
     * logout other device
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function logout_other_devices(BrowserSession $browser, Request $request)
    {
        /*
        * set validation
        */ 
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:3|max:20'
        ]);

        if($validator->fails()) {
            /*
            * if validate is fails
            */ 
            return response()->json([
                'status' => false,
                'code' => 'invalid_password',
                'message' => $validator->messages()->first(),
            ],400);
        }

        if (! Hash::check($request->password, Auth::user()->password))
        {
            /*
            * if password wrong
            */ 
            return response()->json([
                'status' => FALSE,
                'code' => 'invalid_password',
                'message' => 'This password does not match our records.'
            ],400);
        }
        /*
        * count session
        */ 
        $total_session = count($browser->getSessionsProperty());

        if($total_session == 1)
        {
            session()->flash('msg_error', "Can't delete your session here");
            return response()->json([
                'status' => TRUE,
                'message' => "Can't delete your session here"
            ]);
        }
        /*
        * session ready to be remove
        */ 
        $browser->logoutOtherBrowserSessions();
      
        session()->flash('msg_success', 'The sessions on the other devices already has been deleted');

        return response()->json([
            'status' => TRUE,
            'message' => 'OK'
        ]);
    }
    
    /**
     * confirm password
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
    */
    public function confirm_password(TwoFactor $two_factor, Request $request)
    {
        /*
        * set validation
        */ 
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|max:20'
        ]);

        if($validator->fails()) {
            /*
            * if falidation fails
            */ 
            return response()->json([
                'status' => false,
                'code' => 'invalid_password',
                'message' => $validator->messages()->first(),
            ],400);
        }

        if (! Hash::check($request->password, Auth::user()->password))
        {
            /*
            * if password wrong
            */ 
            return response()->json([
                'status' => FALSE,
                'code' => 'invalid_password',
                'message' => 'This password does not match our records.'
            ],400);
        }

        $two_factor->confirmPassword();

        session()->flash('confirmedPassword', $request->trigger);

        return response()->json([
            'status' => TRUE,
            'message' => 'OK',
            'data' => [
                'confirmedPassword' => $request->trigger
            ]
        ]);
    }

}