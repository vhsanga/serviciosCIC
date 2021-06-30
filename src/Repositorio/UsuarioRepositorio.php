<?php
include_once  ROOT_PATH.'/include/Conexion.php';
include_once  ROOT_PATH.'/include/Const.php';
/**
* this class represents a usuario
*/
class UsuarioRepositorio 
{
    
    protected $atributos = ['id','usuario', 'contrasenia','estadousuario','compania' ];
    protected $atributosUP = ['id','usuario', 'estadousuario', 'fregistro', 'idpersona', 'identificacion', 'nombres', 'apellidos', 'fnacimiento', 'direccion', 'telefono', 'correo'  ];
    protected $atributosLogin = ['id','usuario', 'contrasenia', 'identificacion','nombres', 'apellidos', 'compania' ];
    protected $tabla="usuario";
    protected $tablaUP="usuariopersona";
    protected $tablaP="persona";
    protected $tablaUsarioPersona="usuariopersona";
    protected $tablaPersona="persona";
    protected $ESTADO_ACTVO="ACT";

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

  
    private function leerResultadoAttr($result, $arrayAttr){  
        $usuarios = array();       
        while($row = $result->fetch_assoc()) {
            $fila = array(); 
            foreach ($arrayAttr as $columna)  {
                $fila[$this->atributos[($columna-1)]]=$row[$this->atributos[($columna-1)]];
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


    function findAllAttr($arrayAttr){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();
            $sql ="select * from ".$this->tabla;
            $result = $conn->query($sql);
            if ( $result) {
                $usuarios = $this->leerResultadoAttr($result, $arrayAttr); 
                $statusCode=200; 
                $response["message"]["type"] = "OK"; 
                $response["message"]["description"] = "Consulta Correcta"; 
            }else {
                $statusCode=201; 
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



    function findAllByCompania($compania){
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();

            $stmt = $conn->prepare('select u.id,  u.usuario, u.estadousuario, u.fregistro, p.id idpersona, p.identificacion, p.nombres, p.apellidos, p.fnacimiento, p.direccion, p.telefono, p.correo '.
                                   '  from '.$this->tabla.' u left join '.$this->tablaUP.' up on u.id=up.usuario left join '.$this->tablaP.' p on up.persona=p.id  where p.compania=? ');
            $stmt->bind_param('i', $compania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                $usuarios = $this->leerResultado($result,$this->atributosUP ); 
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

    function login($input){
        $usuario = $input->usuario;
        $pass = $input->pass;
        $usuarios = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();        
            $stmt = $conn->prepare('select u.id, u.usuario, up.persona, p.identificacion, p.nombres, p.apellidos, u.compania, u.contrasenia  from '.$this->tabla.' u  left join '.$this->tablaUsarioPersona.' up on up.usuario=u.id  left join '.$this->tablaPersona.' p on up.persona=p.id where u.usuario= ? and u.estadousuario="ACT"  ');
            $stmt->bind_param('s', $usuario); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();            
            if ( $result) {
                if ($result->num_rows > 0) {
                    $usuarios = $this->leerResultado($result, $this->atributosLogin); 
                    $_contrasenia=$usuarios[0]["contrasenia"];
                    if (password_verify("123", $_contrasenia)) {                        
                        $statusCode=200; 
                        $response["message"]["type"] = "OK"; 
                        $response["message"]["description"] = "Ingreso Correcto";
                    }else{                    
                        $statusCode=403; 
                        $response["message"]["type"] = "ERR"; 
                        $response["message"]["description"] = "Credenciales Incorrectas";
                    } 
                }else{                    
                    $statusCode=403; 
                    $response["message"]["type"] = "ERR"; 
                    $response["message"]["description"] = "El usuario ".$usuario." no existe";
                }                              
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


    function registrarUsuario($input){
        $usuario = $input->usuario;
        $pass = $input->contrasenia;
        $compania = $input->compania;
        
        $identificacion = $input->identificacion;
        $nombres = $input->nombres;
        $apellidos = $input->apellidos;
        $fnacimiento = $input->fnacimiento;
        $direccion = $input->direccion;
        $telefono = $input->telefono;
        $correo = $input->correo;
        $tipoidentificacion = $input->tipoidentificacion;
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon();            
            $stmt = $conn->prepare('INSERT INTO '.$this->tabla.' (usuario, contrasenia, estadousuario, fregistro, compania) values (?,?,?,now(),?)  ');
            $stmt->bind_param('sssi', $usuario, $pass, $this->ESTADO_ACTVO,$compania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $idUsuario = $conn->insert_id;

            $stmt = $conn->prepare('INSERT INTO '.$this->tablaPersona.' (identificacion, nombres, apellidos, fnacimiento, direccion, telefono, correo, tipoidentificacion, fregistro, compania) values (?,?,?,?,?,?,?,?,now(),?)  ');
            $stmt->bind_param('ssssssssi', $identificacion, $nombres, $apellidos, $fnacimiento, $direccion, $telefono, $correo, $tipoidentificacion,$compania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $idPersona = $conn->insert_id;

            $stmt = $conn->prepare('INSERT INTO '.$this->tablaUsarioPersona.' (usuario, persona) values (?,?)  ');
            $stmt->bind_param('ii', $idUsuario, $idPersona); // 'i' specifies the variable type => 'entero' a las dos variables            
            $stmt->execute();
            $idPersona = $conn->insert_id;
                           
            $statusCode=200; 
            $response["message"]["type"] = "OK"; 
            $response["message"]["description"] = "Usuario Registrado correctamente";                                               
                                         
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



    function cambiarContrasenia($input){
        $usuario = $input->usuario;
        $pass = $input->pass;
        $passAnterior = $input->passa;
        $compania = $input->compania;
               
        $data = array();               
        $response = array();
        $statusCode=500;
        $mensaje='';	
        try {
            $conn=OpenCon(); 
            $pass = password_hash($pass, HASH, ['cost' => COST]);

            $stmt = $conn->prepare('select u.id, u.usuario, u.contrasenia  from '.$this->tabla.' u  where u.id= ? and u.compania = ?  ');
            $stmt->bind_param('is', $usuario, $compania); // 's' specifies the variable type => 'string' a las dos variables            
            $stmt->execute();
            $result = $stmt->get_result();
            if ( $result) {
                if ($result->num_rows > 0) {
                    $_contrasenia="";
                    while($row = $result->fetch_assoc()) {                        
                        $_contrasenia=$row['contrasenia'];               
                    }
                    if (password_verify($passAnterior, $_contrasenia)) {

                        $stmt = $conn->prepare('UPDATE '.$this->tabla.' SET contrasenia = ? where id= ?  ');
                        $stmt->bind_param('si', $pass,  $usuario); // 's' specifies the variable type => 'string' a las dos variables            
                        $status = $stmt->execute();        
                        if ($status === false) {    
                            $response["message"]["type"] = "DataBase" ;
                            $response["message"]["description"] = $stmt->error;
                        }else{
                            $statusCode=200; 
                            $response["message"]["type"] = "OK"; 
                            $response["message"]["description"] = "Contraseña cambiada correctamente"; 
                        } 
                        
                    }else{
                        $statusCode=403; 
                        $response["message"]["type"] = "ERR"; 
                        $response["message"]["description"] = "La contraseña Anterior es incorrecta";
                    }     
                }else{
                    $statusCode=403; 
                    $response["message"]["type"] = "ERR"; 
                    $response["message"]["description"] = "El usuario ".$usuario." no existe ";
                }                             
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
	    $response["data"] = $data;
        return json_encode($response);
    }


    


}