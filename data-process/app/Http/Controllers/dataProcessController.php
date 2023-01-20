<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSource;
use Illuminate\Support\Str;


class DataProcessController extends Controller
{
    //DataSource table data rendering
    public function dataProcess(){


         DataSource::orderBy('id')->chunk(10, function ($lists) {
             foreach ($lists as $list) {
                //list data share to each functions
                 $this->userTb($list);
                 return '<center><h1>whole process was completed..</h1></center>';
             }
         });

    }

    private function userTb($list)
    {
        $replaced_org=Str::replace(".LTD.","LTD",$list->organisation);

  //processing data for users table
        DB::table('users')->upsert(

                [
                    'name' => trim($list->name),
                    'phone' => $list->tell,
                    'slug' => Str::kebab(trim($list->name)),
                    'organisation'=>$replaced_org,
                    'email' =>$list->name.'@mail.com',
                    'password' => Str::random(100),
                    'address' => $list->address,
                    'created_at'=>$list->current_time,
                    'updated_at'=>$list->current_time

                ], ['email']
            );
            return "UserTable process completed..";
    }
}
