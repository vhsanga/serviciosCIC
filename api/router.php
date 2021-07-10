<?php

require '../vendor/slim/slim/Slim/Slim.php';
use Slim\Slim\PhpRenderer;
include_once '../src/include/Conexion.php';
include_once '../src/include/Mail.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$_token="123TBab5";


define('ROOT_PATH', dirname(__DIR__) );

require_once  ROOT_PATH.'/src/Repositorio/MenuRepositorio.php';
require_once  ROOT_PATH.'/src/Repositorio/UsuarioRepositorio.php';
require_once  ROOT_PATH.'/src/Repositorio/ConsumoRepositorio.php';




/**
 * Menu Repositorio *************************************************************
 */

$app->get('/menu', function () use ($app)  {           
	$menuRepo = new MenuRepositorio;
	$menuRepo = $menuRepo->findMenuDeHoy();  
	return jsonResponse(200, $menuRepo); 
});

$app->post('/ingresarmenu', function () use ($app)  {  	
	$repo = new MenuRepositorio;
	$input = json_decode($app->request->getBody());		      
	$usuarios = $repo->ingresarMenu($input);   
	return jsonResponse(200, $usuarios); 
});


$app->post('/eliminarmenu', function () use ($app)  {  	
	$repo = new MenuRepositorio;
	$input = json_decode($app->request->getBody());		      
	$usuarios = $repo->eliminarMenu($input);   
	return jsonResponse(200, $usuarios); 
});

/**
 * Consumo Repositorio *************************************************************
 */

$app->post('/ingresarconsumo', function () use ($app)  {  	
	$repo = new ConsumoRepositorio;
	$input = json_decode($app->request->getBody());		      
	$usuarios = $repo->ingresarConsumo($input);   
	return jsonResponse(200, $usuarios); 
});


$app->post('/login', function () use ($app)  {  	
	$repo = new UsuarioRepositorio;
	$input = json_decode($app->request->getBody());		      
	$usuarios = $repo->login($input);   
	return jsonResponse(200, $usuarios); 
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
	if( gettype($response) == 'string' ){
		$response=json_decode($response);
	}
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