<?php
error_reporting(0);
require_once 'Clases/CTablas.php';
require_once 'Clases/CVenta.php';
require_once 'Clases/CPasajero.php';
require_once 'Clases/CBusOcupabilidad.php';
require_once 'Clases/CCliente.php';
require_once 'Clases/CAsiento.php';
require_once 'Clases/CItinerario.php';
$method = $_SERVER['REQUEST_METHOD'];//POST GET
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));//FUNCION getCiudades
$input = json_decode(file_get_contents('php://input'),true);//DATOS JSON
switch ($method) {
  case 'GET':
    echo "Not suported GET METHOD"; break;    
  case 'PUT':
    echo "Not suported PUT METHOD"; break;
  case 'POST':
    Servidor($request[0], $input); break;
  case 'DELETE':
    echo "Not suported DELETE METHOD"; break;
  default:
    echo "Not suported METHOD";  
}

function Servidor($p_method, $input){
    switch ($p_method) {
       case 'getAgencias':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->getAgencias();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getCiudades':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->getCiudades();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getTipoDocumento':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->getTipoDocumento();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getSexo':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->getSexo();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getNacionalidad':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->getNacionalidad();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'politicasCancelacion':
          $lo = new CTablas();
          $lo->paData = $input;
          $llOk = $lo->politicasCancelacion();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'setVenta':
          $lo = new CVenta();
          $lo->paData = $input;
          $llOk = $lo->setVenta();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'cancelarVenta':
          $lo = new CVenta();
          $lo->paData = $input;
          $llOk = $lo->cancelarVenta();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getPasajero':
          $lo = new CPasajero();
          $lo->paData = $input;
          $llOk = $lo->getPasajero();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'setPasajero':
          $lo = new CPasajero();
          $lo->paData = $input;
          $llOk = $lo->setPasajero();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getEstructuraBusOcupabilidad':
          $lo = new CBusOcupabilidad();
          $lo->paData = $input;
          $llOk = $lo->getEstructuraBusOcupabilidad();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getCliente':
          $lo = new CCliente();
          $lo->paData = $input;
          $llOk = $lo->getCliente();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'setCliente':
          $lo = new CCliente();
          $lo->paData = $input;
          $llOk = $lo->setCliente();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'setBloquearAsiento':
          $lo = new CAsiento();
          $lo->paData = $input;
          $llOk = $lo->setBloquearAsiento();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'setLiberarAsiento':
          $lo = new CAsiento();
          $lo->paData = $input;
          $llOk = $lo->setLiberarAsiento();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getTarifaAsiento':
          $lo = new CAsiento();
          $lo->paData = $input;
          $llOk = $lo->getTarifaAsiento();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getItinerario':
          $lo = new CItinerario();
          $lo->paData = $input;
          $llOk = $lo->getItinerario();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
       case 'getTodoItinerario':
          $lo = new CItinerario();
          $lo->paData = $input;
          $llOk = $lo->getTodoItinerario();
          if (!$llOk) {
            $resp = $lo->getlcError();
            break;
          }          
          $resp = $lo->DataJSON;
          break;
    }
    echo $resp;
}
?>