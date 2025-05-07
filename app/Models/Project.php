<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['project_suggestion_id', 'student_id', 'supervisor_id', 'final_rating'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function suggestion()
    {
        return $this->belongsTo(ProjectSuggestion::class, 'project_suggestion_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
