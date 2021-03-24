<?php

namespace App\Http\Controllers;

use App\Classroom;
use App\Grade;
use App\Http\Requests\ClassroomRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{

    public function index()
    {
        $My_Classes = Classroom::all();
        $Grades = Grade::all();
        return view('pages.class.index', compact('Grades', 'My_Classes'));
    }


    public function create()
    {

    }

    public function store(Request $request)
    {
//      return $request->List_Classes;

        $List_Classes = $request->List_Classes;
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=['List_Classes.*.name:'.$locale=>'required|unique:classroom_translations,name'];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {
//            $validated = $request->validated();
            foreach ($List_Classes as $List_Class) {
                Classroom::create($List_Class);
            }
            toastr()->success(trans('site.messages.success'));
            return redirect()->route('classrooms.index');
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
        $My_Classes = Classroom::findOrFail($request->id);
        $rules=[
        ];

        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>['required', Rule::unique('classroom_translations','name')->ignore($My_Classes->id,'classroom_id')]];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {

            $request_data=$request->all();
            $My_Classes->update($request_data);

            toastr()->success(trans('site.messages.Update'));
            return redirect()->route('classrooms.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            Classroom::findOrFail($request->id)->delete();
            toastr()->error(trans('site.messages.Delete'));
            return redirect()->route('classrooms.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }

    public function delete_all(Request $request)
    {
//        dd($request);
        $delete_all_id = explode(",", $request->delete_all_id);

        Classroom::whereIn('id', $delete_all_id)->Delete();
        toastr()->error(trans('site.messages.Delete'));
        return redirect()->route('classrooms.index');
    }

}


