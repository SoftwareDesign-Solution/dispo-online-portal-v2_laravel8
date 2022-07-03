<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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


    public function OrderCart()
    {
        return $this->hasOne('App\Models\OrderCart', 'user_id', 'id');
    }

    public function UserProjects()
    {
        return $this->hasMany(UserProject::class, 'Ma_Knr', 'knr')->with(['Project', 'OrderFilters', 'OrderFilters.Order']);
    }

    /*
     * class Project extends Model
        {
            public function deployments()
            {
                return $this->hasManyThrough(
                    Deployment::class,
                    Environment::class,
                    'project_id', // Foreign key on the environments table...
                    'environment_id', // Foreign key on the deployments table...
                    'id', // Local key on the projects table...
                    'id' // Local key on the environments table...
                );
            }
        }
     */

    public function Orders() {
        return $this->hasManyThrough(
            Order::class,
            UserProject::class,
            'Ma_Knr',
            'Projekt_Knr',
            'knr',
            'Projekt_Nr'
        );
    }

    /*
    public function OrderFilters() {
        return $this->hasManyThrough(
            OrderFilter::class,
            UserProject::class,
            'Ma_Knr',
            'Projekt_Knr',
            'knr',
            'Projekt_Nr'
        );
    }
    */

}
