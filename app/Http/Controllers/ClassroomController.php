<?php

namespace App\Http\Controllers;

use App\Classroom;
use App\Grade;
use App\Http\Requests\ClassroomRequest;
use Illuminate\Http\Request;

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

    public function store(ClassroomRequest $request)
    {
//      dd($request);

        $List_Classes = $request->List_Classes;
        try {
            $validated = $request->validated();
            foreach ($List_Classes as $List_Class) {

                $My_Classes = new Classroom();

                $My_Classes->name_class = ['en' => $List_Class['name_en'], 'ar' => $List_Class['name_ar']];

                $My_Classes->grade_id = $List_Class['grade_id'];

                $My_Classes->save();

            }

            toastr()->success(trans('messages.success'));
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
//        return $request;
//        $List_Classes = $request->List_Classes;
        try {
//            $validated = $request->validated();
//          foreach ($List_Classes as $List_Class) {

            $My_Classes = Classroom::findOrFail($request->id);

            $My_Classes->name_class = ['en' => $request->name_en, 'ar' => $request->name_ar];

            $My_Classes->grade_id = $request->grade_id;

            $My_Classes->save();

//          }

            toastr()->success(trans('messages.Update'));
            return redirect()->route('classrooms.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            Classroom::findOrFail($request->id)->delete();
            toastr()->success(trans('site.messages.Delete'));
            return redirect()->route('classrooms.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }

}


