<?php
require $_SERVER['DOCUMENT_ROOT'] . '/api/clases/Database.php';

class Buscador extends Database {
  
  private $categorias = ['NIVEA', 'LAB SUIZOS'];
  
  public function buscador($txt, $categoria){
        if($categoria === 'TODAS'){
              $sql = 'SELECT * FROM codigos_global WHERE (interno LIKE ? OR barra LIKE ? OR descripcion LIKE ?) AND categoria <> ? AND categoria <> ?';
              $search_term = "%" . $txt . "%";
              $param = [$search_term, $search_term, $search_term, $this->categorias[0], $this->categorias[1]];
        }else{
          $sql = 'SELECT * FROM codigos_global WHERE (interno LIKE ? OR barra LIKE ? OR descripcion LIKE ?) AND categoria = ?';
          $search_term = "%" . $txt . "%";
          $param = [$search_term, $search_term, $search_term, $categoria];
        }
        $buscar = $this->ejecutarConsulta($sql, $param);
        $response = $buscar->fetchAll(PDO::FETCH_ASSOC);
        return $response;
    }
    
    public function buscarCategoria($categoria){
      $sql = "SELECT * FROM codigos_global WHERE categoria = ?";
      $busqueda = $this->ejecutarConsulta($sql, [$categoria]);
      $response = $busqueda->fetchAll(PDO::FETCH_ASSOC);
      return $response;
    }
}