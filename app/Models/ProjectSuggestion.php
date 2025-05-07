<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSuggestion extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'student_id', 'status'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
