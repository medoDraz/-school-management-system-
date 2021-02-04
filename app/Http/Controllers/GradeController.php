<?php

namespace App\Http\Controllers;

use App\Grade;
use App\Http\Requests\GradeRequest;
use Illuminate\Http\Request;

class GradeController extends Controller
{

  public function index()
  {
      $Grades = Grade::all();
    return view('pages.grade.index',compact('Grades'));
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
      } catch (\Exception $e){
          return redirect()->back()->withErrors(['error'=>$e->getMessage()]);
      }

  }


  public function show($id)
  {

  }


  public function edit($id)
  {

  }


  public function update($id)
  {

  }


  public function destroy($id)
  {

  }

}

?>
