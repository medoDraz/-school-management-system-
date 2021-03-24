<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use App\Bank;
use App\User;
use Illuminate\Http\Request;
use Validator;

class BankController extends Controller
{
    
    public function index(Request $request)
    {
        if($request->limit){
            $bancks = Bank::with(['user'])->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
            return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $bancks], 200);

        } else {
            $bancks = Bank::with(['user'])->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->get();
            return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $bancks], 200);

        }
    }

    public function getallbanks(Request $request)
    {
        $bancks = Bank::with('user')->where('status','Active')->latest()->get();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $bancks], 200);
    }
   
    public function create(){}

    public function store(Request $request)
    {
        // dd($request);
        $t = [
            
        ];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'shortcut' => 'required',
                'branch' => 'required',
                'accountle' => 'required',
                // 'account' => 'required',
                'swift_code' => 'required',
                'iban' => 'required',
                'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $user = User::where('api_token', $request->token)->first();
        $bank = new Bank();
        $bank->name = $request->name;
        $bank->shortcut = $request->shortcut;
        $bank->branch = $request->branch;
        $bank->account_num_le = $request->accountle;
        $bank->account_num = $request->account;
        $bank->swift_code = $request->swift_code;
        $bank->iban = $request->iban;
        $bank->status = $request->status;
        $bank->created_by = $user->id;
        $bank->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $bank], 200);
    }
    
    public function show(Bank $bank)
    {
        //
    }
    
    public function edit(Request $request)
    {
        $bank = Bank::with(['user'])->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $bank], 200);
    }

    public function update(Request $request)
    {
        $t = [
            
        ];
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'shortcut' => 'required',
                'branch' => 'required',
                'accountle' => 'required',
                // 'account' => 'required',
                'swift_code' => 'required',
                'iban' => 'required',
                'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $user = User::where('api_token', $request->token)->first();
        $bank = Bank::where('id', $request->id)->first();
        $bank->name = $request->name;
        $bank->shortcut = $request->shortcut;
        $bank->branch = $request->branch;
        $bank->account_num_le = $request->accountle;
        $bank->account_num = $request->account;
        $bank->swift_code = $request->swift_code;
        $bank->iban = $request->iban;
        $bank->status = $request->status;
        $bank->updated_by = $user->id;
        $bank->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $bank], 200);
    }

    public function destroy(Request $request)
    {
        //
    }

    public function changestatus(Request $request)
    {
        //dd($request);
        $bank = Bank::where('id', $request->id)->first();
        $user = User::where('api_token', $request->token)->first();
        $bank->update([
            'status' => $request->status,
            'updated_by' => $user->id,
        ]);
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
    }
}
