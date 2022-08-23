<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Gate;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'date_of_birth',
        'motto',
        'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'email_verified_at' => 'date:d/m/Y H:i',
        'banned_at' => 'date:d/m/Y H:i',
        'created_at' => 'date:d/m/Y H:i',
        'last_login' => 'date:d/m/Y H:i',
    ];

    /**
     * Date
     */
    protected $dates = [
        'deleted_at',
        'banned_at',
        'email_verified_at',
        'last_login',
    ];

    /**
     * appends custom attributes
     */
    protected $appends = ['level_label', 'status_label', 'permissions'];

    /**
     * Livelli Utente
     */
    const LEVEL_USER = 0;
    const LEVEL_ADMIN = 1;


    /**
     * Verifica se l'utente è un amministratore
     */
    public function isAdmin()
    {
        return $this->level == User::LEVEL_ADMIN;
    }

    /**
     * Verifica se l'utente non è bannato
     */
    public function isNotBanned()
    {
        return $this->banned == 0;
    }

    /**
     * Restituisce la label del livello utente
     *
     * @return void
     */
    public function getLevelLabelAttribute()
    {
        switch ($this->level) {
            case self::LEVEL_ADMIN:
                return 'Amministratore';
                break;
            case self::LEVEL_USER:
            default:
                return 'Utente';
                break;
        }
    }

    /**
     * Restituisce la label del livello utente
     *
     * @return void
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->isNotBanned()) {
            return 'Bannato';
        }

        if (!$this->email_verified_at) {
            return 'Non attivo';
        }
        if ($this->deleted_at) {
            return 'Cancellato';
        }

        return 'Attivo';
    }

    /**
     * Richiesta dei permessi generale dell'attuale utente
     *
     * todo: implementazione costante con i vari permessi generali dell'utente
     */
    public function getPermissionsAttribute()
    {
        $permissions = [
            'admin' => ['show' => false]
        ];

        if ($this->isAdmin()) {
            $permissions['admin'] = [
                'show' => $this->isAdmin(),
                'user' => [
                    'list' => Gate::allows('viewAny', $this),
                ],
                'log' => [
                    'list' => Gate::allows('viewAny', new Log()),
                ]
            ];
        }

        return $permissions;
    }
}
