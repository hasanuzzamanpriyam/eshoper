<?php

namespace App\Http\Controllers\Payment_Methods;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Districtname;
use App\Models\Thananame;

class AddressNameController extends Controller
{
    //get district name
    public function district_names(){
        $dbData=Districtname::where('status', 'Active')->orderBy('district_name_en','ASC')->get();
        return response()->json($dbData);
    }

    //get thana name
    public function thana_names($distId){
        $dbData=Thananame::where('dist_id', $distId)->where('status', 'Active')->orderBy('id','ASC')->get();
        return response()->json($dbData);
    }
}
