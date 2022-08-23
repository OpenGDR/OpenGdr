<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;

class Log extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'type',
        'level',
        'model',
        'related_id',
        'note',
        'data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date:d/m/Y H:i',
        'data' => 'json',
    ];

    protected $appends = [
        'related_link',
        'label_level'
    ];

    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_NOTICE = 2;
    const LEVEL_WARN = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_ALERT = 5;


    /**
     * Crea la riga di log
     *
     * @param int $level Livello del log
     * @param string $model Modello di riferimento dell'ID
     * @param int $related_id ID del modello di Riferimento
     * @param string $note Stringa informativa
     * @param array|object $data Dati debug
     */
    public static function write($level = self::LEVEL_INFO, $type = '', $model = null, $related_id = null, $note = '', $data = [])
    {
        if (!in_array($level, [
            self::LEVEL_DEBUG,
            self::LEVEL_INFO,
            self::LEVEL_NOTICE,
            self::LEVEL_WARN,
            self::LEVEL_ERROR,
            self::LEVEL_ALERT
        ])) {
            $level = self::LEVEL_INFO;
        }

        if (!in_array($model, [
            'User'
        ])) {
            FacadesLog::error('Log Model not found: ' . $model, [
                'related_id' => $related_id
            ]);
            $model = 'none';
        }

        $log = Log::create([
            'user_id' => Auth::id(),
            'type' => $type,
            'level' => $level,
            'model' => $model,
            'related_id' => $related_id,
            'note' => $note,
            'data' => json_encode($data)
        ]);

        FacadesLog::info('Log action',  [
            'user_id' => Auth::id(),
            'type' => $type,
            'level' => $level,
            'model' => $model,
            'related_id' => $related_id,
            'note' => $note,
            'data' => $data
        ]);

        return true;
    }


    /**
     * Restituisce il link in relazione al modello e id correlato
     *
     * @return void
     */
    public function getRelatedLinkAttribute()
    {
        switch ($this->model) {
            case 'User':
                return route('user.profile', [
                    'id' => $this->related_id
                ]);
                break;
            default:
                return '';
        }
    }



    /**
     * Restituisce la label del livello di log
     *
     * @return void
     */
    public function getLabelLevelAttribute()
    {

        switch ($this->level) {
            case self::LEVEL_DEBUG:
                return 'Debug';
                break;
            case self::LEVEL_INFO:
                return 'Info';
                break;
            case self::LEVEL_NOTICE:
                return 'Notice';
                break;
            case self::LEVEL_WARN:
                return 'Warning';
                break;
            case self::LEVEL_ERROR:
                return 'Error';
                break;
            case self::LEVEL_ALERT:
                return 'Alert';
                break;
            default:
                return '';
        }
    }
}
