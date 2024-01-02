<?php 

namespace App\Http\Controllers;

use Slim\Http\{
  Request,
  Response
};

use App\Container;

final class AgendamentoController{

  public function getAgendamento(Request $request, Response $response, array $args): Response{

    $agendamento = Container::getModel('Agendamento');

    $result = $agendamento->getInfoAgendamento($args['numero_contrato']); //48020

    if ($result) {

      $mensagem = "Consulta realizada com sucesso. 👌😁";
      // return print_r($result);
      return $response = $response->withJson([
          'systemMessage' => "OK",
          'status' => 200,
          'userMessage' =>  $mensagem,
          'result' => $result 
      ], 200)->withHeader('Content-Type', 'application/json');
    } else {
      $mensagem = "Cliente não possui status atual em ANDAMENTO ou o contrato é inválido.";
      
      return $response->withJson([
          'systemMessage' => '$result == null',
          'status' => 406,
          'userMessage' => $mensagem,
          'result' => $result
      ], 406);
    }
  }
}



