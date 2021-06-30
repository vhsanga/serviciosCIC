<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a catalogo y detallecatalogo
*/
class CuentaRepositorio 
{
    protected $atributos = ['id','nombre', 'codigo', 'saldo', 'observacion', 'estadocuenta'];
    protected $tabla="cuenta";

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

    function findByCompania($idCompania){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare("select * from ".$this->tabla." where compania =? and estadocuenta='CTA_ACT' ");
            $stmt->bind_param('i', $idCompania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributos); 
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



    function findByCompaniaAll($idCompania){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare("select * from ".$this->tabla." where compania =? ");
            $stmt->bind_param('i', $idCompania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributos); 
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

    function findById($id){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare("select * from ".$this->tabla." where id =? ");
            $stmt->bind_param('i', $id); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributos); 
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


    function registrarCuenta($input){        
        $nombre = $input->nombre;
        $codigo = $input->codigo;        
        $observacion = $input->observacion;        
        $grupocontable = $input->grupocontable;
        $compania = $input->compania;
        $usuario = $input->usuario;
        $estadocuenta='CTA_ACT';

        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (nombre, codigo,  saldo, observacion, estadocuenta,  fregistro, grupocontable, compania) values (?,?,0,?,?,now(),?,?)  ');
            $stmt->bind_param('sssssi',$nombre, $codigo, $observacion, $estadocuenta, $grupocontable,  $compania); // 's' specifies the variable type => 'string' a las dos variables            
            $status = $stmt->execute();  
            $id = $conn->insert_id;
            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{
                $statusCode=200; 
                $response["message"]["type"] = "OK"; 
                $response["message"]["description"] = "Cuenta ingresada correctamente"; 
                $data = array('id'=>$id ) ;
            }                                                                                                                    
            $stmt->close();
            $conn->close();                                           
        } catch (Exception $e) {
            $response["message"]["type"] = "DataBase"; 
            $response["message"]["description"] = $e->getMessage(); 
        }
        $response["statusCode"] = $statusCode;	   
	    $response["data"] = $data;
        return json_encode($response);
    }
}