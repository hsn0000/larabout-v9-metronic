<?php

use App\Repositories\Repository;
use App\Repositories\User\UserRepository;

if  ( ! function_exists('Repository'))
{
    /**
     * @return Repository
     */
    function Repository()
    {
        return new Repository();
    }
}

if  ( ! function_exists('UserRepository'))
{
    /**
     * @return UserRepository
     */
    function UserRepository()
    {
        return new UserRepository();
    }
}
