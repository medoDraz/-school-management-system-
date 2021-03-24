<?php

namespace App\Http\Controllers;

use App\Classroom;
use App\Grade;
use App\Http\Requests\GradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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


    public function store(Request $request)
    {
//        return $request;
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>'required|unique:grade_translations,name'];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {
            $request_data=$request->all();
            Grade::create($request_data);
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

    public function update(Request $request)
    {
//      dd($request);
        $grade = Grade::findOrFail($request->id);
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>['required', Rule::unique('grade_translations','name')->ignore($grade->id,'grade_id')]];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {

            $request_data=$request->all();
            $grade->update($request_data);

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


