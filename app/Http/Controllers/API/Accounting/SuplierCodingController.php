<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SupplierCoding;
use App\User;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Validator;

class SuplierCodingController extends Controller
{
	public function __construct()
    {
        $this->middleware(['permission_api:Add_Supplier'])->only('store');
        $this->middleware(['permission_api:Access_Supplier'])->only(['index']);
        $this->middleware(['permission_api:Edit_Supplier'])->only(['update','changestatus']);
    }

    public function index(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $supplier_codings = SupplierCoding::with(['user','analysis','account'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $supplier_codings], 200);
    }

    public function getallitems(Request $request)
    {
        $user = User::where('api_token', $request->token)->first();
        $supplier_codings = SupplierCoding::with(['user','analysis','account'])->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $supplier_codings], 200);
    }

    public function store(Request $request)
    {
        //dd($request);

        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                // 'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();
        $supplier_coding = new SupplierCoding();
        $supplier_coding->name                       = $request->name;
        $supplier_coding->description                = $request->description;
        $supplier_coding->account                    = $request->account_code;
        $supplier_coding->credit_by_limit_value      = $request->credit_by_limit_value;
        $supplier_coding->analysis_code       		 = $request->analytics_code;
        $supplier_coding->VATed                      = $request->VATed;
        $supplier_coding->status      			 	 = $request->status;
        $supplier_coding->address                    = $request->address;
        $supplier_coding->contact_no                 = $request->contact_no;
        $supplier_coding->email                      = $request->email;
        $supplier_coding->add_deductTax              = $request->add_deductTax;
        $supplier_coding->payment_period             = $request->payment_period;
        $supplier_coding->registration_num           = $request->registration_num;
        $supplier_coding->tax_id                     = $request->tax_id;
        $supplier_coding->tax_file                   = $request->tax_file;
        $supplier_coding->tax_office                 = $request->tax_office;
        $supplier_coding->created_by        		 = $user->id;
        $supplier_coding->save();

        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $supplier_coding], 200);

    }

    public function edit(Request $request){
		
		$supplier_coding = SupplierCoding::with(['user','analysis','account'])->where('id', $request->id)->first();
		return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $supplier_coding], 200);
	}

	public function update(Request $request)
    {
        //dd($request);
    	$supplier_coding = SupplierCoding::where('id', $request->id)->first();
        $t = [];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                // 'stored_at' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }

		$user = User::where('api_token', $request->token)->first();
        // $supplier_coding = new SupplierCoding();
        $supplier_coding->name                       = $request->name;
        $supplier_coding->description                = $request->description;
        $supplier_coding->account                    = $request->account_code;
        $supplier_coding->credit_by_limit_value      = $request->credit_by_limit_value;
        $supplier_coding->analysis_code              = $request->analytics_code;
        $supplier_coding->VATed                      = $request->VATed;
        $supplier_coding->status                     = $request->status;
        $supplier_coding->address                    = $request->address;
        $supplier_coding->contact_no                 = $request->contact_no;
        $supplier_coding->email                      = $request->email;
        $supplier_coding->add_deductTax              = $request->add_deductTax;
        $supplier_coding->payment_period             = $request->payment_period;
        $supplier_coding->registration_num           = $request->registration_num;
        $supplier_coding->tax_id                     = $request->tax_id;
        $supplier_coding->tax_file                   = $request->tax_file;
        $supplier_coding->tax_office                 = $request->tax_office;
        $supplier_coding->updated_by        		 = $user->id;
        $supplier_coding->save();

        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $supplier_coding], 200);

    }

	public function changestatus(Request $request)
    {
		//dd($request);
        $supplier_coding = SupplierCoding::where('id', $request->id)->first();
		$user = User::where('api_token', $request->token)->first();
        $supplier_coding->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
    }
}
