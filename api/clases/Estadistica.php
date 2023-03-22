<?php
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Estadistica extends Database {
  protected $response;
  
  public function __construct(){
    $this->response = [
      'status' => '',
      'msg' => ''
    ];
  }
  
  public function guardarBusqueda($busqueda, $autor, $timestamp){
    $sql = 'INSERT INTO estadisticas (busqueda, autor, fecha) VALUES (?, ?, ?)';
    $guardar = $this->ejecutarConsulta($sql, [$busqueda, $autor, $timestamp]);
    
    $this->response['status'] = 'OK';
    $this->response['msg'] = 'Registro de búsqueda guardado';
    return $this->response;
  }
}
