<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use App\CashReceipt;
use App\User;
use Validator;
use Illuminate\Http\Request;

class CashReceiptController extends Controller
{

    public function index(Request $request)
    {
        $cash_receipts = CashReceipt::with(['user','account','bank'])->where(function ($q) use ($request) {
                return $q->when($request->search, function ($query) use ($request) {
                    return $query->where('account_code', 'like', '%' . $request->search . '%');
                });
            })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $cash_receipts], 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // dd($request);
        $t = [

        ];
        $validator = Validator::make($request->all(),
            [
                // 'account_code' => 'required',
                // 'amount' => 'required',
                // 'currency' => 'required',
                // 'debit_credit' => 'required',
                // 'description' => 'required',
                // 'analytics_code' => 'required',
                // 'dimension' => 'required',

                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $user = User::where('api_token', $request->token)->first();
        $cash_receipt = new CashReceipt();
        $cash_receipt->account_code = $request->account_code;
        $cash_receipt->bank_code = $request->bank_code;
        $cash_receipt->name = $request->name;
        $cash_receipt->reference = $request->reference;
        $cash_receipt->description = $request->description;
        // $cash_receipt->request_date = $request->request_date;
        $cash_receipt->cash_receipt_by = $request->by;
        $cash_receipt->currency = $request->currency;
        $cash_receipt->amount = $request->amount;
        $cash_receipt->profit = $request->profit;
        $cash_receipt->cheque_no = $request->cheque_no;
        $cash_receipt->cheque_date = $request->cheque_date;
        $cash_receipt->created_by = $user->id;
        $cash_receipt->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $cash_receipt], 200);
    }

    public function show(CashReceipt $cashReceipt)
    {
        //
    }

    public function edit(Request $request)
    {
        $cash_receipt = CashReceipt::with(['user','account','bank'])->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $cash_receipt], 200);
    }

    public function update(Request $request)
    {
         //dd($request);
        $t = [

        ];
        $validator = Validator::make($request->all(),
            [
                // 'account_code' => 'required',
                // 'amount' => 'required',
                // 'currency' => 'required',
                // 'debit_credit' => 'required',
                // 'description' => 'required',
                // 'analytics_code' => 'required',
                // 'dimension' => 'required',

                //'status' => 'required',
            ], [], $t);

        if ($validator->fails()) {
            return response()->json(['error' => true, 'message' => __('site.fill_all_fields'), 'data' => $validator->messages()], 200);
        }
        $user = User::where('api_token', $request->token)->first();
        $cash_receipt = CashReceipt::where('id', $request->id)->first();
        $cash_receipt->account_code = $request->account_code;
        $cash_receipt->name = $request->name;
        $cash_receipt->reference = $request->reference;
        $cash_receipt->description = $request->description;
        // $cash_receipt->request_date = $request->request_date;
        if ($request->by == 'Cheque'){

            $cash_receipt->bank_code = $request->bank_code;
            $cash_receipt->cheque_no = $request->cheque_no;
            $cash_receipt->cheque_date = $request->cheque_date;
        }
        $cash_receipt->cash_receipt_by = $request->by;
        $cash_receipt->currency = $request->currency;
        $cash_receipt->amount = $request->amount;
        $cash_receipt->profit = $request->profit;
        $cash_receipt->updated_by = $user->id;
        $cash_receipt->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $cash_receipt], 200);
    }

    public function destroy(Request $request)
    {
        //
    }
}
