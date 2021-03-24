<?php

namespace App\Http\Controllers\API\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Payment;
use App\PaymentsInstallment;
use App\User;
use App\FeesPaymentsStructure;

class ParentsCollectionsController extends Controller
{
    public function getpaymentsuccess(Request $request){
    	$payment = Payment::with(['user','student'])->where('hold',null)->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment], 200);

    }

    public function getpaymentpendding(Request $request){
    	$payment = Payment::with(['user','student'])->where('hold',1)->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment], 200);

    }

    public function getpaymentfailed(Request $request){
    	$payment = Payment::with(['user','student'])->where('hold',-1)->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('name', 'like', '%' . $request->search . '%');
            });

        })->latest()->paginate($request->limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment], 200);

    }

    public function paymentdetails(Request $request){
    	$payment = PaymentsInstallment::with(['user','student','fees_payment'])->where('student_id',$request->student_id)->get();
    	return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $payment], 200);
    }
}
