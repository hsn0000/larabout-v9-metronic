<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\UserGroup;

class Page extends Model
{
    use HasFactory;

    public function viewdata()
    {
        return [];
    }

    public function total_users_in_group(int $guid)
    {
        return DB::table('users')->where(['guid'=>$guid])->select('guid')->count();
    }

    public function roles($guid = null)
    {
        if(!!$guid)
        {
            $group = UserGroup::find($guid);
        }
        else
        {
            $group = Auth::user()->group();
        }

        $roles = json_decode($group->roles);
        $_roles = ['view' => [], 'create' => [], 'update' => [], 'delete' => []];

        if(is_object($roles))
        {
            foreach($roles as $k => $r)
            {
                $_roles[$k] = explode(',', $r);

            }
        }

        return $_roles;
    }

    public function modules(array $alias, $mod_config = null)
    {
        $mod_config = $mod_config ?: config('modules');

        $_mods = null;
        foreach($mod_config as $mod)
        {
            if(empty($alias['set']))
            {
                $modules[] = $mod;
            }
            else
            {
                if(isset($mod['submenu']))
                {
                    $key = array_search($alias['set'], array_column($mod['submenu'],'parent'));
                    if($key !== false)
                    {
                        $modules = [$mod['submenu'][$key]];
                    }

                    if($alias['set'] == $mod['alias'])
                    {
                        $modules = $mod['submenu'];
                    }
                }
            }

            $modules[] = $_mods;
        }

        $modules = array_filter($modules);

        return ($modules);
    }

    public function blocked_page($alias = null, $role = 'view')
    {
        $roles = $this->roles();

        if(!array_key_exists($role, $roles))
        {
            return abort('404');
        }

        if(!in_array($alias, $roles[$role]))
        {
            return abort('404');
        }
        else
        {
            $show_page = TRUE;
        }
    }

    public function mod_action_roles($alias = '', $role = 'view')
    {
        $roles = $this->roles();

        if(!array_key_exists($role, $roles))
        {
            return FALSE;
        }

        $show_page = TRUE;
        if(!in_array($alias, $roles[$role]))
        {
            $show_page = FALSE;
        }

        return $show_page;
    }

    public function module_roles(Array $alias, $modules = '', $params = 0, $guid = '')
    {
        $modules = $this->modules(['set' => $params > 0 ? $alias['set'] : ''], $modules);
        $decode_modules = (array) json_decode(json_encode($modules));
       
        $template = NULL;

        $template .= $params == 0 ? '<ul class="menu-nav pl-0">' : NULL;
        if(is_array($decode_modules) && count($modules) > 0)
        {
            $roles = [
                ['name' => 'view', 'color' => 'primary', 'text' => 'User can view this page'],
                ['name' => 'create', 'color' => 'success', 'text' => 'User can create the new data'],
                ['name' => 'update', 'color' => 'warning', 'text' => 'User can update existing data'],
                ['name' => 'delete', 'color' => 'danger', 'text' => 'User can delete existing data'],
            ];
            $selected_roles = old('roles') ?: ($guid?$this->roles($guid):[]);
            foreach($decode_modules as $mod)
            {
                if($mod->alias !== 'dashboard')
                {
                    $template .= '<div class="form-group row">';
                    $template .= '<label class="col-xxl-3 col-form-label align-middle'.($mod->parent ? ' pl-'.($params > 1 ? $params * 7 : 5) : '').'">'.($mod->parent ? '<i class="flaticon-more-v2 mr-4"></i>' : '').$mod->name.'</label>';
                    $template .= '<div class="col-9 col-form-label">';
                        $template .= '<div class="row role-options" id="role-'.$mod->alias.'">';
                        foreach($roles as $role)
                        {
                            $allowed_roles = ['view'];
                            if(!empty($mod->url))
                            {
                                $allowed_roles = ['view','create','update','delete'];
                            }

                            if(in_array($role['name'], $allowed_roles))
                            {
                                $template .= '
                                    <div class="col-lg-3">
                                        <label class="option'.(count($selected_roles) > 0 && in_array($mod->alias, $selected_roles[$role['name']]) ? ' bg-'.$role['color'].'-o-30' : '').'">
                                            <span class="option-control">
                                                <span class="checkbox check-'.$role['name'].'" id="'.$role['name'].'-'.$mod->alias.'">
                                                    <input type="checkbox" name="roles['.$role['name'].'][]" value="'.$mod->alias.'" data-role="'.$role['name'].'" data-color="'.$role['color'].'" data-alias="'.$mod->alias.'" data-parent="'.$mod->parent.'"'.(count($selected_roles) > 0 && in_array($mod->alias, $selected_roles[$role['name']]) ? ' checked' : '').'>
                                                    <span></span>
                                                </span>
                                            </span>
                                            <span class="option-label">
                                                <span class="option-head">
                                                    <span class="option-title">'.ucwords($role['name']).'</span>
                                                </span>
                                                <span class="option-body">'.$role['text'].'</span>
                                            </span>
                                        </label>
                                    </div>';
                            }
                            // ID : '.$role['name'].'-'.$mod->alias.' | Alias : '.$mod->alias.' | Parent : '.$mod->parent.'
                        }
                        $template .= '</div>';
                    $template .= '</div>';
                    $template .= '</div>';

                    if(isset($mod->submenu))
                    {
                        $template .= $this->module_roles(['set' => $mod->alias, 'active' => $alias['active']], $modules, $params + 1, $guid);
                    }
                }
            }
        }

        return $template;

    }

    public function module_sidebar(Array $alias, $modules = '', $params = 0)
    {
        $modules = $this->modules(['set' => $params > 0 ? $alias['set'] : ''], $modules);
        $decode_modules = (array) json_decode(json_encode($modules));

        $template = NULL;

        $template .= $params == 0 ? '<ul class="menu-nav">' : NULL;
        if(is_array($decode_modules) && count($modules) > 0)
        {
            $roles = $this->roles();
            foreach($decode_modules as $mod)
            {
                $show_menu = FALSE;
                if(in_array($mod->alias, $roles['view']) || $mod->alias == 'dashboard')
                {
                    $show_menu = TRUE;
                }

                if($show_menu == TRUE)
                {
                    $li_class = $toggle_hover = NULL;
                    if(isset($mod->submenu))
                    {
                        $li_class = ' menu-item-submenu';
                        $toggle_hover = ' data-menu-toggle="hover"';
                    }

                    if(in_array($mod->alias, explode(',',$alias['active'])))
                    {
                        $li_class = ' menu-item-active menu-item-open';
                    }

                    $template .= '<li class="menu-item'.$li_class.'" aria-haspopup="true"'.$toggle_hover.'>';

                    $template .= '<a href="'.($mod->url? url($mod->url) :'javascript:;').'" class="menu-link'.(isset($mod->submenu) ? ' menu-toggle' : '').'">';

                    if($mod->icon)
                    {
                        $template .= '<span class="svg-icon menu-icon">';
                            $template .= $mod->icon;
                        $template .= '</span>';
                    }
                    else
                    {
                        if(isset($mod->submenu))
                        {
                            $template .= '<i class="menu-bullet menu-bullet-line"><span></span></i>';
                        }
                        else
                        {
                            $template .= '<i class="menu-bullet menu-bullet-dot"><span></span></i>';
                        }
                    }

                    $template .= '<span class="menu-text">'.$mod->name.'</span>';

                    if(isset($mod->submenu))
                    {
                        $template .= '<i class="menu-arrow"></i>';
                    }

                    $template .= '</a>';

                    if(isset($mod->submenu))
                    {
                        $template .= '<div class="menu-submenu"><ul class="menu-subnav">';
                        $template .= $this->module_sidebar(['set' => $mod->alias, 'active' => $alias['active']], $modules, 1);
                        $template .= '</ul></div>';
                    }

                    $template .= '</li>';
                }
            }
        }
        $template .= $params == 0 ? '</ul>' : NULL;

        return $template;
    }

}
