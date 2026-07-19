<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function electives()
    {
        return $this->hasMany(StudentElective::class, 'student_id');
    }
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'avatar',
        'programme_id',
        'level_id',
        'last_login_at',
        'is_online',
        'waitlist_school_id',
        'waitlist_joined_at',
    ];
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }
    public function questionAttempts()
    {
        return $this->hasMany(QuestionAttempt::class);
    }

    public function waitlistSchool()
    {
        return $this->belongsTo(School::class, 'waitlist_school_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        'waitlist_joined_at' => 'datetime',
        ];
    }
}
