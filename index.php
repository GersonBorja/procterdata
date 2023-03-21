<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Producto.php';
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
#$app->addErrorMiddleware(false, true, true);

$app->get('/api/productos', function (Request $request, Response $response, array $args) {
    $n = new Producto();
    $productos = $n->obtenerProductos();
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($productos));
    return $response;
});

$app->put('/api/productos/{interno}', function (Request $request, Response $response, array $args) {
    $interno = $args['interno'];
    $body = $request->getParsedBody();
    $claseProducto = new Producto();
    $productos = $claseProducto->editarProducto($interno, $body);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($productos));
    return $response;
});


$app->run();