<?php 

use function src\{
  basicAuth,
  jwtAuth
};

use App\Http\Controllers\{
  AutenticacaoController,
  AuthController,
  MatrixController,
  AgendamentoController
};

use Psr\Http\Message\{
  ServerRequestInterface as Request,
  ResponseInterface as Response
};

use App\Http\Middlewara\MiddlewareError;

use src\jwtAuth;
use Tuupola\Middleware\JwtAuthentication;

$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

require_once '../src/jwtAuth.php';

require_once '../src/dependencies.php';

$container->get('db');

$app->post('/token', AutenticacaoController::class . ':postAutenticacao');

$app->group('/api/v1', function(){
 
  $this->get('/matrix/consulta/{cpf}', MatrixController::class . ':getMatrix');

  $this->get('/matrix/agendamento/{numero_contrato}', AgendamentoController::class . ':getAgendamento');

})->add(jwtAuth());


$app->add(function (Request $request, Response $response, $next) {
  try {
    return $next($request, $response);
  } catch (Exception $e) {
      $mensagem = 'Erro interno do servidor: ⚡' . $e->getMessage();
      return $response->withJson([
        'status' => 500,
        'userMessage' => $mensagem
      ], 500);
  }
});

$app->map(['POST', 'PUT','DELETE', 'PATCH'], '/{routes:.+}', function(Request $request, Response $response) {
  $mensagem = 'Método não permitido. 🔒';
  return $response->withJson([
    'status' => 405,
    'userMessage' => $mensagem
  ], 405);
});

$app->get('/{routes:.+}', function ($request, $response) {
  $mensagem = 'Página não encontrada. 🔍';
  return $response->withJson([
    'status' => 404,
    'userMessage' => $mensagem
  ], 404);

});

$app->run();

?>


