<?php
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Cintillo extends Database {
  protected $response;

  public function __construct($interno = '', $barra = '', $descripcion = '', $cantidad = '', $precio = '', $autor = ''){
        $this->response = [
          'status' => 'error',
          'msg' => ''
          ];
        $this->interno = $interno;
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        $this->autor = $autor;
    }
    
  public function agregarCintillo() {
    if(empty($this->cantidad) || empty($this->precio)){
          $this->response['msg'] = 'Debes completar todos los campos';
          return $this->response;
    }else if(!is_numeric($this->cantidad)){
          $this->response['msg'] = 'La cantidad de cintillos debe ser en numeros';
          return $this->response;
    }else if(!is_numeric($this->precio)){
          $this->response['msg'] = 'El campo precio solo admite numeros';
          return $this->response;
    }else{
      if($this->cantidad > 1){
            for($i = 1;$i <= $this->cantidad;$i++){
                  $sql = 'INSERT INTO codigos (interno, barra, descripcion, cantidad, precio, autor) VALUES (?, ?, ?, ?, ?, ?)';
                  $this->ejecutarConsulta($sql, [$this->interno, $this->barra, $this->descripcion, $this->cantidad, $this->precio, $this->autor]);
            }
            $this->response['status'] = 'OK';
            $this->response['msg'] = 'Se han añadido ' . $this->cantidad . ' cintillos a la lista';
            return $this->response;
      }else{
            $sql = 'INSERT INTO codigos (interno, barra, descripcion, cantidad, precio, autor) VALUES (?, ?, ?, ?, ?, ?)';
            $this->ejecutarConsulta($sql, [$this->interno, $this->barra, $this->descripcion, $this->cantidad, $this->precio, $this->autor]);
            $this->response['status'] = 'OK';
            $this->response["msg"] = 'Se ha añadido ' . $this->cantidad . ' cintillos a la lista';
            return $this->response;
      }
    }
  }
  
  public function editarCintillo($interno, $items = ['descripcion' => '', 'precio' => '']){
    $sql = 'UPDATE codigos SET descripcion = ?, precio = ? WHERE interno = ?';
    $editar = $this->ejecutarConsulta($sql, [$items['descripcion'], $items['precio'], $interno]);
    $cantidad = $editar->rowCount();
    $this->response['status'] = 'OK';
    $this->response["msg"] = $cantidad . ' cintillos actualizados';
    return $this->response;
  }
  
  public function eliminarCintillos($autor){
    $sql = 'DELETE FROM codigos WHERE autor = ?';
    $this->ejecutarConsulta($sql, [$autor]);
    $this->response['status'] = 'OK';
    $this->response["msg"] = 'Listado de cintillos eliminados';
    return $this->response;
  }
  
  public function generarDocumentoCintillos($autor){
    
  }
}