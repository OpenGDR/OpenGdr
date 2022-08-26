<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Race extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'visible',
        'open',
        'enable_for_registration',
        'icon',
        'main_image',
        'short_description',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'date:d/m/Y H:i',
        'options' => 'json',
    ];

    /**
     * appends custom attributes
     */
    protected $appends = ['counter', 'link_edit'];

    /**
     * Impostazione dello slug
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Restituisce il conteggio degli appartenenti alla razza
     *
     * @return void
     * //todo: implementazione del conteggio dei profili appartenenti alla razza
     */
    public function getCounterAttribute()
    {
        return 0;
    }

    /**
     * Restituisce il link per la modifica della razza
     *
     * @return void
     * //todo: implementazione del conteggio dei profili appartenenti alla razza
     */
    public function getLinkEditAttribute()
    {
        return route('admin.race.update', [
            'slug' => $this->slug
        ]);
    }
}
