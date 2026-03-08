<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use function App\CPU\translate;
use App\Models\Districtname;
use App\Models\Thananame;

class AddressController extends Controller
{
    public function index()
    {
        return view('admin-views.business-settings.address');
    }
// for district name ------------------------------
    public function district_name_list(){
        $dbData=Districtname::orderBy('district_name_en','ASC')->get();
        return response()->json($dbData);
    }

    // save new/edited district name
    public function save_district_name(Request $request)
    {
        // for Validation
        $request->validate([
            'district_name_en' => 'required',
            'district_name_bn' => 'required',
            'status' => 'required'
        ]);
        Districtname::updateOrCreate(['id' => $request->district_id],
            [
                'district_name_en' => $request->district_name_en,
                'district_name_bn' => $request->district_name_bn,
                'district_shipping_charge' => $request->district_shipping_charge,
                'status' => $request->status
            ]);
        return response()->json(['message'=>'The District name saved successfully !']);


    }


    public function district_names_edit($id){
        $dbData=Districtname::where('id', $id)->first();
        return response()->json($dbData);
    }

    public function district_names_delete($id){
        $dbData=Districtname::where('id', $id)->first();
        $delete=$dbData->delete();

        if($delete){
            return response()->json(['message'=>'The District name Deleted successfully!']);
        }
    }
// end district name ------------------------
// for thana name ------------------------------
    public function thana_name_list(){
        $dbData=Thananame::join('districtnames','thananames.dist_id','=','districtnames.id')->select('thananames.*','districtnames.district_name_en','districtnames.district_name_bn')->orderBy('dist_id','ASC')->get();
        return response()->json($dbData);
    }

    public function select_district_name(){
        $dbData=Districtname::where('status', 'Active')->orderBy('id','DESC')->get();
        return response()->json($dbData);
    }

    // save new/edited thana name
    public function save_thana_name(Request $request)
    {
        $districtName=Districtname::where('id', $request->dist_id)->first();
        // for Validation
        $request->validate([
            'dist_id' => 'required',
            'thana_name_en' => 'required',
            'thana_name_bn' => 'required',
            'status' => 'required'
        ]);
        Thananame::updateOrCreate(['id' => $request->thana_id],
            [
                'dist_id' => $request->dist_id,
                'thana_name_en' => $request->thana_name_en,
                'thana_name_bn' => $request->thana_name_bn,
                'thana_shipping_charge' => $request->thana_shipping_charge,
                'status' => $request->status
            ]);
        return response()->json(['message'=>'The Thana name saved successfully !']);


    }


    public function thana_names_edit($id){
        $dbData=Thananame::where('id', $id)->first();
        return response()->json($dbData);
    }

    public function thana_names_delete($id){
        $dbData=Thananame::where('id', $id)->first();
        $delete=$dbData->delete();

        if($delete){
            return response()->json(['message'=>'The Thana name Deleted successfully!']);
        }
    }
// end thana name ------------------------


}
