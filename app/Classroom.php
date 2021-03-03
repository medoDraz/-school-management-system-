<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Classroom extends Model
{
    use HasTranslations;
    public $translatable = ['name_class'];

    protected $table = 'classrooms';
    public $timestamps = true;
    protected $fillable=['name_class','grade_id'];

    public function grade()
    {
        return $this->belongsTo('App\Grade', 'grade_id');
    }

}
