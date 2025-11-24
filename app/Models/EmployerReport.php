<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployerReport extends Model
{
    protected $fillable = [
        'employer_id',
        'candidate_id',
        'type',
        'reason',
        'status',
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function candidate()
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }
}