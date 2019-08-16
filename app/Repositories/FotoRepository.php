<?php

namespace App\Repositories;

use App\Models\Foto;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FotoRepository
 * @package App\Repositories
 * @version November 20, 2018, 11:26 pm -02
 *
 * @method Foto findWithoutFail($id, $columns = ['*'])
 * @method Foto find($id, $columns = ['*'])
 * @method Foto first($columns = ['*'])
*/
class FotoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'url',
        'vistoria_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Foto::class;
    }
}
