<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use App\PaymentVoucher;
use App\User;
use Illuminate\Http\Request;
use Validator;

class PaymentVoucherController extends Controller
{
    
    public function index(Request $request)
    {
        if($request->limit){
            $user = User::where('api_token', $request->token)->first();
            $role_name=$user->roles->pluck('name')[0];
            if ($role_name == 'Audit') {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('status',2)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            } elseif ($role_name == 'Budget') {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('status',3)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            }elseif ($role_name == 'CFO') {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])
                ->where('status',5)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            } elseif ($role_name == 'GM') {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('status',6)->where('accepted',0)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            } elseif ($role_name == 'Fin M') {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('status',4)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            } else {
                $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('status',1)->where(function ($q) use ($request) {
                    return $q->when($request->search, function ($query) use ($request) {
                        return $query->where('account_code', 'like', '%' . $request->search . '%');
                    });
                })->latest()->paginate($request->limit);
                return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
            }
        } else {
            $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where(function ($q) use ($request) {
                return $q->when($request->search, function ($query) use ($request) {
                    return $query->where('account_code', 'like', '%' . $request->search . '%');
                });
            })->latest()->get();
            return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);
        }
        
    }

    public function getsuccess(Request $request){
        $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('accepted',1)->where(function ($q) use ($request) {
                return $q->when($request->search, function ($query) use ($request) {
                    return $query->where('account_code', 'like', '%' . $request->search . '%');
                });
            })->latest()->paginate($request->limit);
            return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);

    }

    
    public function create()
    {
        //
    }

    
    public function store(Request $request)
    {
        //dd($request);
        $t = [
            
        ];
        $validator = Validator::make($request->all(),
            [
                'account_code' => 'required',
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
        $payment_voucher = new PaymentVoucher();
        $payment_voucher->account_code = $request->account_code;
        $payment_voucher->bank_code = $request->bank_code;
        $payment_voucher->request_to = $request->request_to;
        $payment_voucher->subject = $request->subject;
        $payment_voucher->description = $request->description;
        $payment_voucher->request_date = $request->request_date;
        $payment_voucher->analysis_code = $request->analytics_code;
        $payment_voucher->amount = $request->amount;
        $payment_voucher->currency = $request->currency;
        $payment_voucher->dimension = $request->dimension;
        $payment_voucher->profit = $request->profit;
        $payment_voucher->status = $request->status;
        $payment_voucher->created_by = $user->id;
        $payment_voucher->save();
        return response()->json(['error' => false, 'message' => __('site.added_successfully'), 'data' => $payment_voucher], 200);
    }

    
    public function show(PaymentVoucher $paymentVoucher)
    {
        //
    }

   
    public function edit(Request $request)
    {
        $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('id', $request->id)->first();
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment_voucher], 200);

    }

    
    public function update(Request $request)
    {
        $t = [
            
        ];
        $validator = Validator::make($request->all(),
            [
                'account_code' => 'required',
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
        $payment_voucher = PaymentVoucher::with(['user','account','analysis','bank'])->where('id', $request->id)->first();
        $payment_voucher->account_code = $request->account_code;
        $payment_voucher->bank_code = $request->bank_code;
        $payment_voucher->request_to = $request->request_to;
        $payment_voucher->subject = $request->subject;
        $payment_voucher->description = $request->description;
        $payment_voucher->request_date = $request->request_date;
        $payment_voucher->analysis_code = $request->analytics_code;
        $payment_voucher->amount = $request->amount;
        $payment_voucher->currency = $request->currency;
        $payment_voucher->dimension = $request->dimension;
        $payment_voucher->profit = $request->profit;
        $payment_voucher->status = $request->status;
        $payment_voucher->updated_by = $user->id;
        $payment_voucher->save();
        return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => $payment_voucher], 200);
    }

    
    public function destroy(Request $request)
    {
        //
    }

    public function changestatus(Request $request)
    {
        //dd($request);
        $payment_voucher = PaymentVoucher::where('id', $request->id)->first();
        $user = User::where('api_token', $request->token)->first();
        $role_name=$user->roles->pluck('name')[0];
            if ($role_name == 'Audit') {
                $payment_voucher->update([
                    'status' => $request->status,
                    'audit_by' => $user->id,
                ]);
                return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
            } elseif ($role_name == 'Budget') {
                $payment_voucher->update([
                    'status' => $request->status,
                    'budget_approval_by' => $user->id,
                ]);
                return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
            }elseif ($role_name == 'CFO') {
                $payment_voucher->update([
                    'status' => $request->status,
                    'cfo_approval_by' => $user->id,
                ]);
                return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
            } elseif ($role_name == 'GM') {
                if ($request->accepted) {
                    $payment_voucher->update([
                        'accepted' => $request->accepted,
                        'gm_approval_by' => $user->id,
                    ]);
                    return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
                } else{
                    $payment_voucher->update([
                        'status' => $request->status,
                        'gm_approval_by' => $user->id,
                    ]);
                    return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
                }
                
            } elseif ($role_name == 'Fin M') {
                $payment_voucher->update([
                    'status' => $request->status,
                    'fin_m_approval_by' => $user->id,
                ]);
                return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
            } 
            // else {
            //     $payment_voucher->update([
            //         'status' => $request->status,
            //         'updated_by' => $user->id,
            //     ]);
            //     return response()->json(['error' => false, 'message' => __('site.updated_successfully'), 'data' => []], 200);
            // }
    }
}
