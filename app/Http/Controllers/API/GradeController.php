<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

//use App\Classroom;
//use App\Grade;
//use App\Http\Requests\GradeRequest;
use App\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{

    public function index(Request $request)
    {
        $request->limit == 'NaN' ? $limit = count(Grade::all()) : $limit = $request->limit;

        $Grades = Grade::with(['user'])->where(function ($q) use ($request) {
            return $q->when($request->search, function ($query) use ($request) {
                return $query->where('name', $request->search . '%');
            });
        })->latest()->paginate($limit);
        return response()->json(['error' => false, 'message' => __('site.successfully'), 'data' => $Grades], 200);
    }

    public function store(Request $request)
    {
        dd('create',$request);
        try {
            $validated = $request->validated();
            $grade = new  Grade();
            $grade->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $grade->notes = $request->notes;
            $grade->save();
            toastr()->success(trans('site.messages.success'));
            return redirect()->route('grade.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }

    public function update(Request $request)
    {
        dd('update',$request);

        try {
            $validated = $request->validated();
            $grade = Grade::findOrFail($request->id);
            //          dd($grade);
            $grade->name = ['en' => $request->name_en, 'ar' => $request->name_ar];
            $grade->notes = $request->notes;
            $grade->save();
            toastr()->success(trans('site.messages.Update'));
            return redirect()->route('grade.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function change_status(Request $request)
    {

    }

    public function destroy(Request $request)
    {
        try {
            $my_classes = Classroom::where('grade_id', $request->id)->pluck('grade_id');
            if ($my_classes->count() == 0) {
                Grade::findOrFail($request->id)->delete();
                toastr()->error(trans('site.messages.Delete'));
                return redirect()->route('grade.index');
            } else {
                toastr()->error(trans('site.Classes_trans.delete_Class_Error'));
                return redirect()->route('grade.index');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}


