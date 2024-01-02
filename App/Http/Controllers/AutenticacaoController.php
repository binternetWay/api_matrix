<?php
//composer require firebase/php-jwt

namespace App\Http\Controllers;

use Slim\Http\{
  Request,
  Response
};
use App\Models\VariavelSistema;
use \Firebase\JWT\JWT;

final class AutenticacaoController{

  public function postAutenticacao(Request $request, Response $response, array $args): Response{

    $dados = $request->getParsedBody();

    $codigo = $dados['codigo'] ?? null;
    $variavel = $dados['variavel'] ?? null;


    $variavel_sistema = VariavelSistema::where('codigo', $codigo)->first();

    if( !is_null($variavel_sistema) && (md5($variavel) === $variavel_sistema->variavel ) ){

      $settings = require __DIR__ . '/../../../src/settings.php';
      $secretKey = $settings['settings']['secretKey'];

      //Dados que serão usados para gerar o token: https://jwt.io/
      $variavel_PlayLoad= [
        'sub' => $variavel_sistema->id,
        'codigo' => $variavel_sistema->codigo,
      ];

      $chaveAcesso = JWT::encode($variavel_PlayLoad, $secretKey);

      $mensagem = "Token gerado com sucesso. 👌😁";

      return $response->withJson([
        'systemMessage' => "OK",
        'status' => 200,
        'userMessage' => $mensagem,
        'token' => $chaveAcesso,
      ], 200);
    }else{
      $mensagem = "Dados de login invalidos, para gerar o token é necessário todos dos dados de login.";

      return $response->withJson([
          'systemMessage' => 'Dados invalidos.',
          'status' => 401,
          'userMessage' => $mensagem
      ], 401);
    }
  }
}
?>