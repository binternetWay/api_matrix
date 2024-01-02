<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\VariavelSistema;
use App\Container;

$app->post('/matrix/lista/{cpf}', function($request, $response, $args){

	$cpf = $args['cpf'];

	$dados = $request->getParsedBody(); //recupeda dados do banco

	$codigo = $dados['codigo'] ?? null;
	$variavel = $dados['variavel'] ?? null;

	$variavel_sistema = VariavelSistema::where('codigo', $codigo)->first();

	if( !is_null($variavel_sistema) && (md5($variavel) === $variavel_sistema->variavel ) ){

		$matrix = Container::getModel('Matrix');
		return $response->withJson($matrix->getInfoMatrix($cpf ));

		if(!$matrix){
			return $response->withJson(['Error' => 'CPF não encontrado'], 404);
		}
	}

});
