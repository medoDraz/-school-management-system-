<?php
namespace App\Repository;

use App\Teacher;

class TeacherRepository implements TeacherRepositoryInterface{

    public function getAllTeachers(){
        return Teacher::all();
    }

}
