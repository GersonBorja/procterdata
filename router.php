<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\UploadedFile;
use Slim\Factory\AppFactory;

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Producto.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Buscador.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Estadistica.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Inventario.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

# API PARA OBTENER PRODUCTOS DEL SISTEMA
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

#API PARA OBTENER EL BUSCAR

$app->get('/api/buscador/{texto}/{categoria}', function (Request $request, Response $response, array $args) {
    $texto = $args['texto'];
    $categoria = $args['categoria'];
    $claseBuscador = new Buscador();
    $resultados = $claseBuscador->buscador($texto, $categoria);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($resultados));
    return $response;
});

$app->get('/api/categoria/{categoria}', function (Request $request, Response $response, array $args) {
    $categoria = $args['categoria'];
    $claseBuscador = new Buscador();
    $resultados = $claseBuscador->buscarCategoria($categoria);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($resultados));
    return $response;
});

# API PARA EL INVENTARIO ACTUALIZAR, SUBIR, ETC

$app->post('/api/inventario/upload', function (Request $request, Response $response) {
  #instancia del objeto UploadedFile
    $archivoInstancia = $request->getUploadedFiles();
    
  #se define el nombre del input en html con el que trabajaremos
    $archivoInput = $archivoInstancia['archivo'];
    
  #obtener la ruta temporal
    $ruta = $archivoInput->getStream();
    $tmp = $ruta->getMetadata()['uri'];
    
    #creando un array para el archivo cargado
    $archivoDatos = [
      "name" => $archivoInput->getClientFilename(),
      "error" => $archivoInput->getError(),
      "tmp_name" => $tmp
      ];
      
    $claseInventario = new Inventario();
    $procesar = $claseInventario->subirInventarioExcel($archivoDatos);
    $actualizarSistema = $claseInventario->actualizarInventarioSistema($procesar['ruta']);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($actualizarSistema));
    return $response;
});

$app->run();