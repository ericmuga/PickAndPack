<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


     public function linePrepacks()
     {
        return $this->hasMany(LinePrepack::class);
     }

     public function assemblyLines()
     {
        return $this->hasMany(AssemblyLine::class);
     }

     public function packing_sessions()
     {
         return $this->hasMany(PackingSession::class);
     }


     public function assembly_sessions()
     {
         return $this->hasMany(AssemblySession::class);
     }

     public function assignments()
     {
        return $this->hasMany(Assignment::class,'assignee_id','id');
     }

     public function assignedTo()
     {
        return $this->hasMany(Assignment::class,'assignor_id','id');
     }


    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }

}
