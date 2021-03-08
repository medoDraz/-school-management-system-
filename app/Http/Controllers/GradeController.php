<?php

namespace App\Http\Controllers;

use App\Classroom;
use App\Grade;
use App\Http\Requests\GradeRequest;
use Illuminate\Http\Request;

class GradeController extends Controller
{

    public function index()
    {
        $Grades = Grade::all();
        return view('pages.grade.index', compact('Grades'));
    }


    public function create()
    {

    }


    public function store(GradeRequest $request)
    {

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


    public function show($id)
    {

    }


    public function edit($id)
    {

    }


    public function update(GradeRequest $request)
    {
//      dd($request);

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


    public function destroy(Request $request)
    {
        try {
            $my_classes = Classroom::where('grade_id',$request->id)->pluck('grade_id');
            if ($my_classes->count() == 0){
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


