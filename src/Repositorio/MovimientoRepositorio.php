<?php
include_once  ROOT_PATH.'/include/Conexion.php';
/**
* this class represents a usuario
*/
class MovimientoRepositorio 
{
    protected $atributos = ['id','usuario', 'tipooperacion', 'signo', 'valor', 'fecha', 'detalle' ];
    protected $atributosResumen = ['anio','mes', 'registros', 'valor' ];
    protected $atributosConceptos = ['id','concepto', 'registros', 'valor' ];
    protected $atributosMovimiento = ['id','valor', 'tipooperacion', 'detalle', 'fecha'];
    protected $tabla="movimiento";
    protected $tablaConcepto="concepto";
    protected $tablaCta="cuenta";
    protected $tablaAsientoContable="asientocontable";

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

    function findByUsuarioAndFecha($idCompania, $fInicio, $fFin){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {            
            $conn=OpenCon();            
            $stmt = $conn->prepare('select m.id, m.usuario, m.tipooperacion, case when m.tipooperacion =\'ING\' then \'+\' end as signo,  m.valor, m.fecha, m.detalle from '.$this->tabla.' m   where m.compania=? and  m.fecha between ? and ? ');
            $stmt->bind_param('iss', $idCompania,$fInicio, $fFin); // 's' specifies the variable type => 'string' a las dos variables            
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



    /**
     * Listar un resumen de ingresos-egresos  agrupados por meses
     */
    function findByUsuarioResumen($idCompania){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {            
            $conn=OpenCon();            
            $stmt = $conn->prepare('select year(fecha) as anio,'. 
            ' case when  month(fecha) =1 then \'Enero\'  '.
            '  when  month(fecha) =2 then \'Febrero\'  '.
            '  when  month(fecha) =3 then \'Marzo\'  '.
            '  when  month(fecha) =4 then \'Abril\'  '.
            '  when  month(fecha) =5 then \'Mayo\'  '.
            '  when  month(fecha) =6 then \'Junio\'  '.
            '  when  month(fecha) =7 then \'Julio\'  '.
            '  when  month(fecha) =8 then \'Agosto\'  '.
            '  when  month(fecha) =9 then \'Septiembre\'  '.
            '  when  month(fecha) =10 then \'Octubre\'  '.
            '  when  month(fecha) =11 then \'Noviembre\'  '.
            '  when  month(fecha) =12 then \'Diciembre\'  '.
            '  end as   mes, month(fecha) as _mes,'.
            ' count(id) as registros, sum(valor) as valor from ' .$this->tabla.' where compania=? GROUP BY 1,2,3  ORDER by _mes asc');
            $stmt->bind_param('i', $idCompania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributosResumen); 
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




    /**
     * Listar un resumen de ingresos-egresos  agrupados por meses
     */
    function findResumenConceptoByCompania($idCompania){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {            
            $conn=OpenCon();            
            $stmt = $conn->prepare('select c.id as id, c.codigo  as concepto, '.
                'count(m.id) as registros, sum(m.valor) as valor '.
                'from movimiento m inner join concepto c on m.conceptoprincipal = c.id where m.compania=? '.
                'GROUP BY 1 '.
                'ORDER by valor asc');
            $stmt->bind_param('i', $idCompania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributosConceptos); 
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

    function registrarIngreso($input){  
        $usuario = $input->usuario;      
        $conceptocredito = $input->conceptocredito;
        $conceptodebito = $input->conceptodebito;
        $valor = $input->valor;
        $tipooperacion = $input->tipooperacion;
        $compania = $input->compania;
        $cuentacredito = $input->cuentacredito;
        $detalle = $input->detalle;

       
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (usuario, tipooperacion, valor, detalle, fecha, conceptoprincipal, compania) values (?,?,?,?,now(),?,?)  ');
            $stmt->bind_param('isdsii', $usuario, $tipooperacion, $valor, $detalle, $conceptocredito, $compania); // 's' specifies the variable type => 'string' a las dos variables            
            $status = $stmt->execute();  
            $idMovimiento = $conn->insert_id;
            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{

                //insertar DEBE
                $stmt = $conn->prepare('INSERT INTO '.$this->tablaAsientoContable.' (movimiento, concepto, debe) values (?,?,?)  ');
                $stmt->bind_param('iid', $idMovimiento,  $conceptocredito, $valor); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();       
                //insertar HABER
                $stmt = $conn->prepare('INSERT INTO '.$this->tablaAsientoContable.' (movimiento, concepto, haber) values (?,?,?)  ');
                $stmt->bind_param('iid', $idMovimiento,  $conceptodebito, $valor); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();  

                $stmt = $conn->prepare('UPDATE '.$this->tablaConcepto.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $valor,  $conceptocredito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();              
                
                $stmt = $conn->prepare('UPDATE '.$this->tablaCta.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $valor,  $cuentacredito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();     

                if ($status === false) {    
                    $response["message"]["type"] = "DataBase" ;
                    $response["message"]["description"] = $stmt->error;
                }else{
                    $statusCode=200; 
                    $response["message"]["type"] = "OK"; 
                    $response["message"]["description"] = "Valor ingresado correctamente"; 
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


    function registrarEgreso($input){  
        $usuario = $input->usuario;      
        $conceptocredito = $input->conceptocredito;
        $conceptodebito = $input->conceptodebito;
        $valor = $input->valor;
        $tipooperacion = $input->tipooperacion;
        $compania = $input->compania;
        $cuentacredito = $input->cuentacredito;
        $cuentadebito = $input->cuentadebito;
        $detalle = $input->detalle;

        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (usuario, tipooperacion, valor, detalle, fecha, conceptoprincipal, compania) values (?,?,?,?,now(),?,?)  ');
            $stmt->bind_param('isdsii', $usuario, $tipooperacion, $valor, $detalle, $conceptodebito, $compania); // 's' specifies the variable type => 'string' a las dos variables            
            $status = $stmt->execute();  
            $idMovimiento = $conn->insert_id;

            if ($status === false) {    
                $response["message"]["type"] = "DataBase" ;
                $response["message"]["description"] = $stmt->error;
            }else{                
                $val_=$valor*(-1);
                //insertar DEBE
                $stmt = $conn->prepare('INSERT INTO '.$this->tablaAsientoContable.' (movimiento, concepto, debe) values (?,?,?)  ');
                $stmt->bind_param('iid', $idMovimiento,  $conceptocredito, $val_); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();       
                //insertar HABER
                $stmt = $conn->prepare('INSERT INTO '.$this->tablaAsientoContable.' (movimiento, concepto, haber) values (?,?,?)  ');
                $stmt->bind_param('iid', $idMovimiento,  $conceptodebito, $val_); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute(); 

                //ACtualizar los saldos en los conceptos
                $stmt = $conn->prepare('UPDATE '.$this->tablaConcepto.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $valor,  $conceptodebito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();        
                
                $stmt = $conn->prepare('UPDATE '.$this->tablaConcepto.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $val_,  $conceptocredito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();        
                
                //ACtualizar los saldos en las cuentas
                $stmt = $conn->prepare('UPDATE '.$this->tablaCta.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $valor,  $cuentadebito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();        
                
                $stmt = $conn->prepare('UPDATE '.$this->tablaCta.' SET saldo = saldo + ? where id= ?  ');
                $stmt->bind_param('di', $val_,  $cuentacredito); // 's' specifies the variable type => 'string' a las dos variables            
                $status = $stmt->execute();        
                if ($status === false) {    
                    $response["message"]["type"] = "DataBase" ;
                    $response["message"]["description"] = $stmt->error;
                }else{
                    $statusCode=200; 
                    $response["message"]["type"] = "OK"; 
                    $response["message"]["description"] = "Valor ingresado correctamente"; 

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



    function findMovimientosByCuenta($id){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare("select m.id, m.valor, m.tipooperacion, m.detalle, m.fecha from ".$this->tabla."  m inner join ".$this->tablaConcepto."  c on  m.conceptoprincipal = c.id where c.cuenta = ? ");
            $stmt->bind_param('i', $id); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result, $this->atributosMovimiento); 
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