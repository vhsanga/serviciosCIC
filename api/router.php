<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header ('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
require '../vendor/slim/slim/Slim/Slim.php';
use Slim\Slim\PhpRenderer;
include_once '../src/include/Conexion.php';
include_once '../src/include/Mail.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$_token="123TBab5";


$app->post('/ingreso_usuario', function () use ($app)  {
	$response = array();
	$error=true;
	$mensaje='';	
	$vehiculos = array();
	try {
		$conn=OpenCon();
		$data = json_decode($app->request->getBody());
		$usuario =  $_POST["usuario"];
		$pass =  $_POST["pass"];
		$nombre =  $_POST["nombre"];
		$apellido =  $_POST["apellido"];
		$ci =  $_POST["ci"];
		$direccion =  $_POST["direccion"];
		$telefono =  $_POST["telefono"];
		$correo =  $_POST["correo"];
		$tipo =  "1";

		
		CloseCon($conn);
	} catch (Exception $e) {
		$error=true;
		$mensaje=$e->getMessage();
	}
	$response["error"] = $error;
	$response["message"] = $mensaje; 
	$response["vehiculos"] = $vehiculos;
	return jsonResponse(200, $response);
});




$app->get('/ingreso_seguimiento_vehiculo/:placa/:camara/:fecha/:hora/:token', function ($placa, $camara, $fecha, $hora,  $token) use ($app, $_token)  {
	$response = array();
	$error=true;
	$mensaje='';	
	if(strcmp($token, $_token) == 0){
		try {
			$conn=OpenCon();
			$fecha=$fecha." ".$hora;		
			$query = "INSERT INTO seguimiento_vehiculos (placa, camara, fecha) VALUES ('".$placa."', '".$camara."', '".$fecha."');";
			if (mysqli_query($conn, $query)) {
				$error=false;
				$mensaje='Se ha ingresado el suceso';			
			} else {
				$mensaje='No se ha ingresado el suceso. '.mysqli_error($conn);
			}
			CloseCon($conn);
		} catch (Exception $e) {
			$error=true;
			$mensaje=$e->getMessage();
		}
	}else{
		$error=true;
		$mensaje="Token invalido";
	}
	$response["error"] = $error;
	$response["message"] = $mensaje; 
	return jsonResponse(200, $response);
});






$app->get('/camaras/editar', function () use ($app)  {
	$mensaje='';
	$codigo_estado=500;
	try {
		$conn=OpenCon();
		$campo =$_GET['name'];
		$nuevo_valor =$_GET['value'];
		$id = $_GET['pk'];
		
		$query = "UPDATE camara   SET  ".$campo." ='".$nuevo_valor."'  WHERE codigo='".$id."'";

		if (mysqli_query($conn, $query)) {
			$codigo_estado=200;
			$mensaje='Se ha guardado la informacion';			
		} else {
			$mensaje='No se ha guardado la informacion. '.mysqli_error($conn);
		}
		CloseCon($conn);
	} catch (Exception $e) {
		$mensaje=$e->getMessage();
	}
	
	return jsonResponse($codigo_estado, $mensaje);
});


$app->get('/auto', function () {
	$response = array();
	$autos = array(
		array('make'=>'Toyota', 'model'=>'Corolla', 'year'=>'2006', 'MSRP'=>'18,000'),
		array('make'=>'Nissan', 'model'=>'Sentra', 'year'=>'2010', 'MSRP'=>'22,000')
	);
	$response["error"] = false;
	$response["message"] = "Autos cargados: " . count($autos); //podemos usar count() para conocer el total de valores de un array
	$response["autos"] = $autos;
	return jsonResponse(200, $response);
});


$app->get('/test', function () {
	
	$response="prueba";
	return jsonResponse(200, $response);
});


$app->get('/books/:id', function ($id) use ($app) {
	$app->render('home.php', array('id' => $id));
});


$app->run();




//   FUNCIONES NECESARIAS //




/**
* Mostrando la respuesta en formato json al cliente o navegador
* @param String $status_code Http response code
* @param Int $response Json response
*/
function jsonResponse($status_code, $response) {
	$app = \Slim\Slim::getInstance();
	// Http response code
	$app->status($status_code);

	$json_response = $app->response; 
	$json_response['Content-Type'] = 'application/json'; 
	$json_response->body( json_encode($response) ); 

	return $json_response;
	
} 


function obtenerFechaHoraActual(){
	date_default_timezone_set('America/Guayaquil');
	$fecha_actual=new DateTime();
	return $fecha_actual->format('Y-m-d H:i:s');
}



?>