<?php 

namespace App\Http\Controllers;

use Slim\Http\{
  Request,
  Response
};
use App\Container;

final class MatrixController{

  public function getMatrix(Request $request, Response $response, array $args): Response{
    
    $matrix = Container::getModel('Matrix');

    $result = $matrix->getInfoMatrix($args['cpf']);

    if ($result) {
      $mensagem = "Consulta realizada com sucesso. 👌😁";

      return $response = $response->withJson([
          'systemMessage' => "OK",
          'status' => 200,
          'userMessage' =>  $mensagem,
          'result' => $result
      ], 200)->withHeader('Content-Type', 'application/json');
    } else {
      $mensagem = "Contrato é inválido.";

      return $response->withJson([
          'systemMessage' => 'is_null($result)',
          'status' => 406,
          'userMessage' => $mensagem,
          'result' => $result
      ], 406);
    }

  }

}

?>