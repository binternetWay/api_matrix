<?php
//composer update

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariavelSistema extends Model{

	public $pluralize = false;
	
	public $timestamps = false;
	
	protected $table = 'variavel_sistema';

	protected $fillable = [ 
		'codigo',
		'variavel'
	];
}