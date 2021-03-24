<?php

namespace App\Http\Controllers;

use App\Repository\TeacherRepositoryInterface;
use App\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    protected $Teacher;

    public function __construct(TeacherRepositoryInterface $Teacher)
    {
        $this->Teacher = $Teacher;
    }

    public function index()
    {
        return $this->Teacher->getAllTeachers();
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(Teacher $teacher)
    {
        //
    }


    public function edit(Teacher $teacher)
    {
        //
    }


    public function update(Request $request, Teacher $teacher)
    {
        //
    }


    public function destroy(Teacher $teacher)
    {
        //
    }
}
