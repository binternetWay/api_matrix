<?php 

namespace src;
use Psr\Http\Message\{
  ServerRequestInterface as Request,
  ResponseInterface as Response
};
use Tuupola\Middleware\JwtAuthentication;

function jwtAuth(): JwtAuthentication{
  $settings = require __DIR__ . '/settings.php';
  $secretKey = $settings['settings']['secretKey'];
  return new JwtAuthentication([
    'secret' =>  $secretKey,
    'attribute' => 'jwt',
    "header" => "token", //Authorization ou X-Token
    "regexp" => "/(.*)/",
    "path" => "/api", /* or ["/api", "/admin"] */
    "ignore" => ["/api/token"],
    "error" =>  function (Response $response, $arguments) {
                  $data['status'] = 401;
                  $data['error'] = 'Token não fornecido. É necessário um token de autorização. 🔑';

                  $body = $response->getBody();
                  $body->write(json_encode($data));

                  return $response
                      ->withHeader('Content-Type', 'application/json')
                      ->withStatus(401)
                      ->withBody($body);
                }
  ]);
}

$app->add(function (Request $request, Response $response, $next) {
  $response = $next($request, $response);
  return $response
          ->withHeader('Access-Control-Allow-Origin', '*')
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET', 'POST');
});

?>