<?php

namespace App\Repositories;

use App\Models\Roteiro;
use InfyOm\Generator\Common\BaseRepository;

class RoteiroRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return Roteiro::class;
    }
}
