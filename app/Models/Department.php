<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'code', 'description', 'sort_order', 'public_name', 'public_summary', 'public_description', 'responsibilities', 'public_display_order'])]
class Department extends Model
{
    protected function casts(): array
    {
        return ['responsibilities' => 'array', 'public_published_at' => 'datetime'];
    }

    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;
}
