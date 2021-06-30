<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a catalogo y detallecatalogo
*/
class CatalogoRepositorio 
{
    protected $atributos = ['codigo','descripcion', 'catalogo'];
    protected $tabla="detallecatalogo";

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

    function findAll(){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();
            $sql ="select * from ".$this->tabla;
            $result = $conn->query($sql);
            if ( $result) {
                $usuarios = $this->leerResultado($result,$this->atributos ); 
                $statusCode=200; 
                $response["message"]["type"] = "OK"; 
                $response["message"]["description"] = "Consulta Correcta"; 
            }else {
                $statusCode=200; 
                $response["message"]["type"] = "DataBase"; 
                $response["message"]["description"] = $conn->error; 
            }                             
            CloseCon($conn);    
        } catch (Exception $e) {
            $response["message"]["type"] = "DataBase"; 
            $response["message"]["description"] = $e->getMessage(); 
        }
        $response["statusCode"] = $statusCode;	   
	    $response["data"] = $usuarios;
        return json_encode($response);
    }

}