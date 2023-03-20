<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Notificacion.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

class Inventario extends Database {
      private $notificacion;
      
      public function __construct() {
        $this->notificacion = new Notificacion();
    }
    
      public function actualizarInventarioSistema($rutaDelExcel){
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaDelExcel);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $mensaje = [
                        "status" => "ok",
                        "mensaje" => "Inventario actualizado en el sistema"
                        ];
            
            for($i = 2;$i <= count($sheetData);$i++){
                  $sql = 'UPDATE  codigos_global SET existencia = ? WHERE interno = ?';
                  $this->ejecutarConsulta($sql, [$sheetData[$i]["D"], $sheetData[$i]["A"]]);
            }
            return json_encode($mensaje);
      }
      
      public function subirInventarioExcel($archivo){
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $directorio_destino = '../public/existencias/';
            $ruta_archivo = $directorio_destino . $archivo['name'];
            if ($archivo['error'] === UPLOAD_ERR_NO_FILE) {
                  $mensaje = array('status' => 'error', 'mensaje' => 'No se ha seleccionado ningún archivo.');
                  return $mensaje;
            }else if ($archivo['error'] !== UPLOAD_ERR_OK) {
                  $mensaje = array('status' => 'error', 'mensaje' => 'Error al cargar el archivo.');
                  return $mensaje;
                  
            }else if ($extension !== 'xlsx') {
                  $mensaje = array('status' => 'error', 'mensaje' => 'El archivo debe ser un archivo xlsx.');
                  echo $mensaje;
            }else if (file_exists($ruta_archivo)) {
                  $mensaje = array('status' => 'error', 'mensaje' => 'El sistema ya se actualizó anteriormente con este excel');
                  return $mensaje;
            }else{
                  if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
                        $mensaje = array('status' => 'ok', 'mensaje' => 'El archivo se ha subido correctamente.', 'ruta' => $ruta_archivo);
                        return $mensaje;
                  }else{
                        $mensaje = array('status' => 'error', 'mensaje' => 'El archivo no se ha subido correctamente.');
                        return $mensaje;
                  }
            }
      }
}