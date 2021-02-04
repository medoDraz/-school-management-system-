<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;


class Grade extends Model
{
    use HasTranslations;
    protected $fillable =[''];
    protected $table = 'grades';
    public $timestamps = true;

    use SoftDeletes;
    public $translatable = ['name'];
    protected $dates = ['deleted_at'];

}
