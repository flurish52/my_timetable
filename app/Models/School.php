<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_supported'];

    // Starting offset so the first person to join doesn't see "#1"
    public function waitlistedUsers()
    {
        return $this->hasMany(User::class, 'waitlist_school_id');
    }

    /**
     * Deterministic base offset per school, seeded from its id,
     * so counts look organic instead of every school starting at
     * the same flat number.
     */
    public function waitlistBaseOffset(): int
    {
        // Range: 15–45, deterministic per school id (same result every call)
        return 15 + ($this->id * 7) % 31;
    }

    public function waitlistCount(): int
    {
        return $this->waitlistBaseOffset() + $this->waitlistedUsers()->count();
    }
    function departments()
    {
        return $this->hasMany(Department::class);
    }


}
