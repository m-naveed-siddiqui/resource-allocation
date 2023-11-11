<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'resource_id',
        'allocation',
        'allocation_start_date',
        'allocation_end_date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
