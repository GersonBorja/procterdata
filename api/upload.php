<?php

require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Inventario.php';
$respuestaDelMetodo = $_SERVER["REQUEST_METHOD"];

switch ($respuestaDelMetodo) {
    
    case 'POST':
          $archivo = $_FILES["archivo"];
          $stock = new Inventario();
          $res = $stock->subirInventarioExcel($archivo);
          var_dump($res);
        break;
    default:
        // Código para manejar un método no admitido o desconocido
        http_response_code(405);
        echo json_encode("Metodo no admitido");
        break;
}
