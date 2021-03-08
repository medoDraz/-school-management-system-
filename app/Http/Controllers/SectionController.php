<?php

namespace App\Http\Controllers;

use App\Grade;
use App\Section;
use Illuminate\Http\Request;

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
        //
    }


    public function show(Section $section)
    {
        //
    }


    public function edit(Section $section)
    {
        //
    }


    public function update(Request $request, Section $section)
    {
        //
    }


    public function destroy(Section $section)
    {
        //
    }

    public function getclasses(){

    }
}
