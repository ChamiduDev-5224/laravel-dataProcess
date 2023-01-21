<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSource;
use App\Models\Users;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DataProcessController extends Controller
{
    //DataSource table data rendering
    public function dataProcess(){


         DataSource::orderBy('id')->chunk(10, function ($lists) {
             foreach ($lists as $list) {
                //list data share to each functions
                 $this->userTb($list);
                 $this->orderTb();
                 $this->propertiseTb($list);
                 $this->propertyAminitieTb($list);
             }
         });
         return '<center><h1>Whole process was completed..</h1></center>';

    }

    private function userTb($list)
    {
        $replaced_org=Str::replace(".LTD.","LTD",$list->organisation);

       //processing data for users table
        DB::table('users')->upsert(

                   ['name' => trim($list->name),
                    'phone' => $list->tell,
                    'slug' => Str::kebab(trim($list->name)),
                    'organisation'=>$replaced_org,
                    'email' =>$list->name.'@mail.com',
                    'password' => Str::random(100),
                    'address' => $list->address,
                    'created_at'=>$list->current_time,
                    'updated_at'=>$list->current_time], ['email']
            );
            return "UserTable process completed..";
    }


    private function orderTb()
    {  print_r("hellooo");
        //user table data redering
        Users::chunk(10, function ($users) {
            foreach ($users as $user) {
                //orderId generate
                $orderId = rand(0, 9999999);

                //data insert to order table
                DB::table('orders')
                ->upsert(['order_id' => '#' .$orderId,
                        'user_id' => $user->id,
                        'package_id' => rand(0,999999),
                        'purchase_date' =>Carbon::now()->toDateString(),
                        'expired_day' => 120,
                        'expired_date' => Carbon::now()->addDays(120)->toDateString(),
                        'payment_status' => 1,
                        'amount_usd' => 0,
                        'amount_real_currency' => 0,
                        'currency_type' => 'INR',
                        'currency_icon' => '₹',
                        'status' => 0],['user_id']);
                    }
        });
        return "orders table process completed..";
    }

    private function propertiseTb($list){

        // remove comma and replace it
        $area= str_replace(",", "", $list->sqft);
        $price= str_replace(",", "", $list->price);
       //upsert data to propertise table
        DB::table('properties')
        ->upsert([
            'id' => $list->id,
            'user_type' => rand(0,1),
            'admin_id' => 0||4,
            'user_id' => rand(0,100),
            'property_type_id' => rand(0,4),
            'city_id' => 3,
            'listing_package_id' => 0,
            'property_purpose_id' =>0,
            'title' => trim($list->address),
            'slug' => Str::kebab(trim($list->address)),
            'views' => rand(20,800),
            'address' => trim($list->address),
            'phone' => $list->tell,
            'website' => $list->Page_URL,
            'description' => $list->description,
            'number_of_room' => $list->bed,
            'number_of_bedroom' => $list->bed,
            'number_of_bathroom' => $list->bath,
            'area' => floatval($area),
            'price' => floatval($price),
            'seo_title' => trim($list->address),
            'seo_description' => trim($list->address),
        ], [
            'id'
        ]);
        return "propertise table process completed..";
    }

    private function propertyAminitieTb($list)
    {
        //asign empty array
        $facilityArr = [];
        $idList = [];

       // facilities data fetch to array
        $facilityArr = explode(',', $list->facilities);
        $facilityArr = array_map('trim', $facilityArr);

        foreach ($facilityArr as $arr) {
            //checking if there existing id
            if (DB::table('aminities')->where('aminity', $arr)->exists()) {
                //not existing
                $tbId = DB::table('aminities')
                    ->select('id')
                    ->where('aminity', $arr)
                    ->first()->id;

                array_push($idList, $tbId);
            }
        }

        //Upsert data to property_aminities table
        foreach ($idList as $id) {
            DB::table('property_aminities')
         ->upsert(['property_id' => $list->id,
              'aminity_id' => $id], ['id']);
        }
        return "property_aminities table process completed..";
    }

}
