<?php

namespace App\Models;

use Eloquent as Model;


/**
 * Class Foto
 * @package App\Models
 * @version November 20, 2018, 11:26 pm -02
 *
 * @property string description
 * @property string url
 * @property integer vistoria_id
 */
class Foto extends Model
{


    public $table = 'fotos';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [

        'url',
        'vistoria_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
