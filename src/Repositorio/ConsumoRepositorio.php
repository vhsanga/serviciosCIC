<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a catalogo y detallecatalogo
*/
class ConsumoRepositorio 
{   

    protected $atributos = ['id','idcliente', 'fecha', 'valorsuma', 'valoriva', 'valordescuento', 'valortotal',];
    protected $tabla="consumo";
    protected $tabladc="detalleconsumo";

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


    function ingresarConsumo($input){  
        $idcliente = $input->idcliente;      
        $fecha = $input->fecha;
        $valorsuma = $input->valorsuma;
        $valoriva = $input->valoriva;
        $valordescuento = $input->valordescuento;
        $valortotal = $input->valortotal;
        $detalleConsumo = $input->detalleConsumo;

       
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (idcliente, fecha, valorsuma, valoriva, valordescuento, valortotal) values (?,?,?,?,?,?)  ');
            $stmt->bind_param('isdddd', $idcliente, $fecha, $valorsuma, $valoriva, $valordescuento, $valortotal); // 's' specifies the variable type => 'string' a las dos variables            
            //idsb
            $status = $stmt->execute();  
            $idconsumo = $conn->insert_id;
            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{             

                $insertado=false;
                $detalleConsumo =  (array) $detalleConsumo;
                $idconsumo = gettype($detalleConsumo);
                for($i=0;$i<count($detalleConsumo);$i++) 
                { 

                    $idconsumo = $detalleConsumo[$i]->idmenu;
                     //read image_list[].imagedata  element                   
                    
                    $idmenu = $detalleConsumo[$i]->idmenu; //read image_list[].imagedata  element                   
                    $cantidad = $detalleConsumo[$i]->cantidad; 
                    $valor = $detalleConsumo[$i]->valor; 
                    $stmt = $conn->prepare('INSERT INTO '.$this->tabladc.' (idmenu, idconsumo, cantidad, valor) values (?,?,?,?)  ');
                    $stmt->bind_param('iiid', $idmenu, $idconsumo, $cantidad, $valor); // 's' specifies the variable type => 'string' a las dos variables            
                    $status = $stmt->execute();  
                    if ($status === true) {  
                        $insertado = true;  
                    }
                }     
                
                if($insertado){
                    $statusCode=200; 
                    $response["message"]["type"] = "OK"; 
                    $response["message"]["description"] = "Se ha registrado el consumo correctamente"; 
                }   else{
                    $response["message"]["type"] = "DataBase" ;
                    $response["message"]["description"] = $stmt->error;
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