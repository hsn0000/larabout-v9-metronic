<?php

namespace App\Repositories;
use DB;

use Illuminate\Support\Collection;

class Repository
{
    /**
     * manual load table w/o model
     *
     * @param string $table_name
     * @return \Illuminate\Database\Query\Builder
     */
    function table(string $table_name) { 
        return DB::table($table_name);
    }

}