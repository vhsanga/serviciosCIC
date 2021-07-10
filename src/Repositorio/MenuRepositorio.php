<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a catalogo y detallecatalogo
*/
class MenuRepositorio 
{   

    protected $atributos = ['id','tipo', 'nombre', 'fecha', 'estado', 'cantidadinicial', 'cantidadactual', 'comida'];
    protected $tabla="menu";

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

    function findMenuDeHoy(){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();
            $sql ="select * from ".$this->tabla. " where fecha = CURDATE() and estado =1";
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


    function ingresarMenu($input){  
        $tipo = $input->tipo;      
        $nombre = $input->nombre;
        $fecha = $input->fecha;
        $estado = $input->estado;
        $cantidadinicial = $input->cantidadinicial;
        $cantidadactual = $input->cantidadactual;
        $comida = $input->comida;

       
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (tipo, nombre, fecha, estado, cantidadinicial, cantidadactual, comida) values (?,?,?,?,?,?,?)  ');
            $stmt->bind_param('issiiii', $tipo, $nombre, $fecha, $estado, $cantidadinicial, $cantidadactual, $comida); // 's' specifies the variable type => 'string' a las dos variables            
            $status = $stmt->execute();  
            $idMovimiento = $conn->insert_id;
            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{                  

                if ($status === false) {    
                    $response["message"]["type"] = "DataBase" ;
                    $response["message"]["description"] = $stmt->error;
                }else{
                    $statusCode=200; 
                    $response["message"]["type"] = "OK"; 
                    $response["message"]["description"] = "El menú ha sido ingresado correctamente"; 
                }                                     
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


    function eliminarMenu($input){  
        $id = $input->id;             
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();   
            $stmt = $conn->prepare('UPDATE '.$this->tabla.' SET estado = 0 where id= ?  ');
            $stmt->bind_param('i',  $id); // 's' specifies the variable type => 'string' a las dos variables            
            $status = $stmt->execute();                
            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{                  

                if ($status === false) {    
                    $response["message"]["type"] = "DataBase" ;
                    $response["message"]["description"] = $stmt->error;
                }else{
                    $statusCode=200; 
                    $response["message"]["type"] = "OK"; 
                    $response["message"]["description"] = "El menú ha sido eliminado"; 
                }                                     
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