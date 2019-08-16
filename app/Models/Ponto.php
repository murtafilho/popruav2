<?php

namespace App\Models;

use Eloquent as Model;

class Ponto extends Model
{

	public $table = 'pontos';

	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';


	protected $dates = ['deleted_at'];


	public $fillable = [
		'numero',
		'caracteristica_abrigo_id',
		'complemento',
		'endereco_id'
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'id' => 'integer',
		'numero' => 'string',
		'caracteristica_abrigo' => 'string',
		'complemento' => 'string',
		'endereco_id' => 'integer',
        'caracteristica_abrigo_id' =>'integer'
	];

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public static $rules = [

	];

	public function endereco(){
		return $this->belongsTo(Endereco::class);
	}

	public function getResumoAttribute(){
		$resumo = $this->endereco->logradouro.' '.
			$this->numero.' - '.$this->endereco->bairro.' - '.$this->endereco->regional;
		$resumo .= '<br>';
		$resumo .= $this->endereco->caracteristica_abrigo.'  '.$this->caracteristica_abrigo;
		return $resumo;
	}
}
