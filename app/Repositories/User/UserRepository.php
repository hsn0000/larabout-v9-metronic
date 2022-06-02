<?php
namespace App\Repositories\User;

use App\Repositories\Repository;

class UserRepository extends Repository
{
    /**
     * get users data
     *
     * @param null $key
     * @param null $value
     * @param null $select
     * @return \Illuminate\Support\Collection
     */
    public function get_users($key = null,$value = null,$select = null)
    {
        $select = is_null($select) ? 'users.*' : $select;

        $query =  $this->table('users')
            ->selectRaw($select);

        if (is_array($key))
        {
            $query = $query->where($key);
        }
        elseif(!is_null($key) && !is_null($value))
        {
            $query = $query->where($key,$value);
        }
        elseif (!is_null($key) && is_null($value))
        {
            $query = $query->whereRaw($key);
        }

        return $query;
    }

    /**
     * generate users query for data table
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function get_users_table()
    {
        return $this->table('users as u')
        ->leftJoin('user_group as ug', 'ug.guid', '=', 'u.guid')
        ->selectRaw('u.id,u.name,u.email,u.password,u.created_at,u.profile_photo_path,ug.group');
    }

    /**
     * process add users
     *
     * @param array $data
     * @return bool
     */
    public function insert_users(array $data)
    {
        return $this->table('users')->insertGetId($data);
    }
    
    /**
     * Update users data
     *
     * @param $id
     * @param $data
     * @return bool|int
     */
    public function update_users($id,$data)
    {
        return $this->table('users')->where(['id'=>$id])->update($data);
    }

    /**
     * delete users data
     *
     * @param $id
     * @return bool|int
     */
    public function delete_users($id)
    {
        return $this->table('users')->where(['id'=>$id])->delete();
    }

    /**
     * process add user group
     *
     * @param array $data
     * @return bool
     */
    public function insert_user_group(array $data)
    {
        return $this->table('user_group')->insertGetId($data);
    }

    /**
     * Update user group data
     *
     * @param $id
     * @param $data
     * @return bool|int
     */
    public function update_user_group($id,$data)
    {
        return $this->table('user_group')->where(['guid'=>$id])->update($data);
    }

    /**
     * delete user group data
     *
     * @param $id
     * @return bool|int
     */
    public function delete_user_group($id)
    {
        return $this->table('user_group')->where(['guid'=>$id])->delete();
    }
}
