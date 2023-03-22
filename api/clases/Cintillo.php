<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Coordenada.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class Cintillo extends Database {
  protected $response;
  protected $plantilla;
  
  public function __construct($interno = '', $barra = '', $descripcion = '', $cantidad = '', $precio = '', $autor = ''){
        $this->response = [
          'status' => 'error',
          'msg' => ''
          ];
        $this->plantilla = $_SERVER['DOCUMENT_ROOT'] . '/PLANTILLA2.xlsx';
        $this->interno = $interno;
        $this->barra = $barra;
        $this->descripcion = $descripcion;
        $this->cantidad = $cantidad;
        $this->precio = $precio;
        $this->autor = $autor;
        $this->cord = new Coordenada();
    }
    
  public function obtenerCintillos($autor){
    $sql = 'SELECT * FROM codigos WHERE autor = ? ORDER BY id ASC';
    $response = $this->ejecutarConsulta($sql, [$autor]);
    $datos = $response->fetchAll(PDO::FETCH_ASSOC);
    
    return $datos;
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
    date_default_timezone_set("America/El_Salvador");
    $filename = 'CINTILLOS-' . date("Y-m-d") . '-' . date("his") . '.xlsx';
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->plantilla);
    
    $worksheet = $spreadsheet->getActiveSheet();
    $detalles_venta = $this->obtenerCintillos($autor);
    $i = 1;
    
    foreach ($detalles_venta as $item) {
      $valores = $this->cord->darCoordenadas($i);
      $descripcion = $item['descripcion'];
      $barra = floatval($item['barra']);
      $precio = "$" . $item['precio'];
      
      $worksheet->getCell($valores[0])->setValue($descripcion);
      $worksheet->getCell($valores[1])->setValue($precio);
      $worksheet->setCellValueExplicit($valores[2], $barra, DataType::TYPE_STRING);
      $i++;
    }
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=' . $filename);
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    $this->eliminarCintillos($autor);
  } 
}