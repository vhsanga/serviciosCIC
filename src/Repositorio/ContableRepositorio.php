<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a usuario
*/
class ContableRepositorio 
{
    protected $atributosLibro = ['id','descripcion', 'debe', 'haber' ];
    protected $tablaAC="asientocontable";
    protected $tablaConcepto="concepto";
    protected $tablaMovimiento="movimiento";

    private function leerResultado($result, $arrAtrib){  
        $usuarios = array();       
        while($row = $result->fetch_assoc()) {
            $fila = array(); 
            foreach ($arrAtrib as $columna)  {
                $fila[$columna]=$row[$columna];
            }
            array_push($usuarios , $fila );                
        }              
        return $usuarios;
    }

    function findLibroDiarioByFecha($idCompania, $fechaI, $fechaF){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {            
            $conn=OpenCon();           
            $stmt = $conn->prepare('select ac.id, c.descripcion, ac.debe, ac.haber  from ' .$this->tablaAC.' ac inner join ' .$this->tablaConcepto.' c on ac.concepto=c.id  inner join  '.$this->tablaMovimiento.' m on ac.movimiento=m.id where m.fecha BETWEEN ? AND ?   and c.compania =? ');
            $stmt->bind_param('ssi',$fechaI ,$fechaF , $idCompania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributosLibro); 
                $statusCode=200; 
                $response["message"]["type"] = "OK"; 
                $response["message"]["description"] = "Consulta Correcta";                                               
            }else {
                $response["message"]["type"] = "DataBase"; 
                $response["message"]["description"] = $conn->error; 
            }                             
            $stmt->close();
            $conn->close();            
        } catch (Exception $e) {
            $response["message"]["type"] = "DataBase"; 
            $response["message"]["description"] = $e->getMessage(); 
        }
        $response["statusCode"] = $statusCode;	   
	    $response["data"] = $usuarios;
        return json_encode($response);
    }



}