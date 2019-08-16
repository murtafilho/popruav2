<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Roteiro
 * @package App\Models
 * @version September 10, 2018, 6:34 pm UTC
 */
class Roteiro extends Model
{

    public $table = 'Roteiro';
    
    public $timestamps = false;



    public $fillable = [
        'Des_Rote',
        'Sgl_Rote',
        'Sit_Rote',
        'Sec_Rote',
        'Cls_Rote',
        'Tip_Estb',
        'Cod_Func_Incl',
        'Dat_Incl',
        'Cod_Func_Altr',
        'Dat_Altr'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'Des_Rote' => 'string',
        'Sgl_Rote' => 'string',
        'Sit_Rote' => 'string',
        'Sec_Rote' => 'string',
        'Cls_Rote' => 'string',
        'Tip_Estb' => 'string',
        'Cod_Func_Incl' => 'string',
        'Cod_Func_Altr' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function roteiroItem()
    {
        return $this->hasOne(\App\Models\RoteiroItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function roteiroSubAtividade()
    {
        return $this->hasOne(\App\Models\RoteiroSubAtividade::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     **/
    public function roteiroTematica()
    {
        return $this->hasOne(\App\Models\RoteiroTematica::class);
    }
}
