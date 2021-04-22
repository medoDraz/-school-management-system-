<?php

namespace App\Http\Controllers;

use App\Classroom;
use App\Grade;
use App\Http\Requests\SectionRequest;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{

    public function index()
    {
        $Grades = Grade::with(['Sections'])->get();

        $list_Grades = Grade::all();

        return view('pages.section.index',compact('Grades','list_Grades'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>'required|unique:section_translations,name'];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {

//            $validated = $request->validated();
            $request_data=$request->all();
            $request_data['Status'] = 1;
            Section::create($request_data);

//            $Sections->Name_Section = ['ar' => $request->Name_Section_Ar, 'en' => $request->Name_Section_En];
//            $Sections->Grade_id = $request->Grade_id;
//            $Sections->Class_id = $request->Class_id;
//            $Sections->Status = 1;
//            $Sections->save();
            toastr()->success(trans('site.messages.success'));

            return redirect()->route('sections.index');
        }

        catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function show(Section $section)
    {
        //
    }


    public function edit(Section $section)
    {
        //
    }


    public function update(Request $request )
    {
        $Sections = Section::findOrFail($request->id);
        $rules=[
        ];
        foreach (config('translatable.locales') as $locale){
            $rules +=[$locale.'.name'=>['required', Rule::unique('section_translations','name')->ignore($Sections->id,'section_id')]];
//            $rules +=[$locale.'.body'=>'required'];
        }
        $request->validate($rules);
        try {
//            $validated = $request->validated();
            $request_data=$request->all();
            if(isset($request->Status)) {
                $request_data['Status'] = 1;
            } else {
                $request_data['Status'] = 2;
            }
            $Sections->update($request_data);

            $Sections->save();
            toastr()->success(trans('site.messages.Update'));

            return redirect()->route('sections.index');
        }
        catch
        (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function destroy(request $request)
    {

        Section::findOrFail($request->id)->delete();
        toastr()->error(trans('site.messages.Delete'));
        return redirect()->route('sections.index');

    }

    public function getclasses($id){
        $list_classes = Classroom::where("grade_id", $id)->get();


        return $list_classes;
    }
}
