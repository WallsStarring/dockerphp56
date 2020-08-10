<?php
require_once "DataAcces/CBase.php";

class CAsiento extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function setBloquearAsiento() {
      $loSql = new CBase();
      $llOk = $loSql->omConnect();
      if (!$llOk) {
         $this->pcError = $loSql->pcError;
         return false;
      }
      $llOk = $this->mxValApikey($loSql);
      if (!$llOk) {
         $loSql->omDisconnect();
         return false;
      }
      $llOk = $this->mxsetBloquearAsiento($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxsetBloquearAsiento($p_oSql) {
       $lnItinerarioID  = $this->paData['itinerarioID'];
      $lnAsiento = $this->paData['asiento'];
      $ldFechaPartida = $this->paData['fechaPartida'];
      $lnRutaID = $this->paData['rutaID'];
      $lnPiso = $this->paData['piso'];
      $lcSql = "select a.ast_codigo from pasajes.tblviajesasientos_vbo a where a.via_id = $lnItinerarioID and a.vbo_numasiento = $lnAsiento and a.ast_codigo != 'lib'";
      $RS = $p_oSql->omExec($lcSql); 
      $laFila = $p_oSql->fetch($RS);
      if (!empty($laFila[0])) {
         $this->pcError = "-2";
         return false;
      }
      $lcSql = "select a.ast_codigo  from pasajes.tblviajesasientos_vbo a where a.via_id = $lnItinerarioID and a.vbo_numasiento = $lnAsiento and a.ast_codigo = 'lib'";
      $RS = $p_oSql->omExec($lcSql); 
      $laFila = $p_oSql->fetch($RS); 

      if ($this->codigo == "000001") {
           $Usuario = "REDBUS";
        }
       if ($this->codigo == "000002") {
           $Usuario = "REDBUS";
        }
        if ($this->codigo == "000003") {
           $Usuario = "RECORRIDO";
        }
        if ($this->codigo == "000004") {
           $Usuario = "REDBUS";
        }

      if (empty($laFila[0])) {
         $lcSql = "insert into pasajes.tblviajesasientos_vbo (via_id, ast_codigo,nod_codigoorigen, nod_codigodestino, vbo_numasiento, vbo_piso,
                  vbo_tipo, pvt_id, vbo_usucreacion, vbo_feccreacion, vbo_fechaliberacion) values ($lnItinerarioID, 'blo', (select nod_codigoorigen from dbo.tblrutas_rut where rut_id = $lnRutaID), (select nod_codigodestino from dbo.tblrutas_rut where rut_id = $lnRutaID), $lnAsiento, $lnPiso, 'AE', 2018,'$Usuario',SYSDATETIME(), DATEADD(minute,120,getdate())) ";
         $llOk = $p_oSql->omExec($lcSql);
         if (!$llOk) {
            $this->pcError = '-1';
            return false;
         }
      }else{
            $lcSql = "update pasajes.tblviajesasientos_vbo  set ast_codigo = 'blo', vbo_usumodificacion = '$Usuario', vbo_fecmodificacion = SYSDATETIME(), vbo_fechaliberacion =  DATEADD(minute,120,getdate()) where via_id = $lnItinerarioID and vbo_numasiento = $lnAsiento and ast_codigo = 'lib'";
            $llOk = $p_oSql->omExec($lcSql);
            if (!$llOk) {
               $this->pcError = '-1';
               return false;
            }
         }
      $laData = array("OK" => "1");
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function setLiberarAsiento() {
      $loSql = new CBase();
      $llOk = $loSql->omConnect();
      if (!$llOk) {
         $this->pcError = $loSql->pcError;
         return false;
      }
      $llOk = $this->mxValApikey($loSql);
      if (!$llOk) {
         $loSql->omDisconnect();
         return false;
      }
      $llOk = $this->mxsetLiberarAsiento($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxsetLiberarAsiento($p_oSql) {
      $lnItinerarioID  = $this->paData['itinerarioID'];
      $lnAsiento = $this->paData['asiento'];
      $lnPiso = $this->paData['piso'];
      $lnRutaID = $this->paData['rutaID'];
      if ($this->codigo == "000001") {
           $Usuario = "REDBUS";
        }
       if ($this->codigo == "000002") {
           $Usuario = "REDBUS";
        }
        if ($this->codigo == "000003") {
           $Usuario = "RECORRIDO";
        }
      $lcSql = " select a.vbo_id from pasajes.tblviajesasientos_vbo a, dbo.tblviajes_via b , dbo.tblrutas_rut c   where b.via_id = a.via_id and b.rut_id = c.rut_id and c.rut_id = $lnRutaID and  a.via_id = $lnItinerarioID and a.vbo_numasiento = $lnAsiento and a.vbo_piso = $lnPiso and a.ast_codigo = 'blo' and a.vbo_usucreacion = '$Usuario' ";
      $RS = $p_oSql->omExec($lcSql);
      $laFila = $p_oSql->fetch($RS); 
      if (!empty($laFila[0])) {
         $lcSql = "update pasajes.tblviajesasientos_vbo  set ast_codigo = 'lib', vbo_usumodificacion = '$Usuario', vbo_fecmodificacion = SYSDATETIME(), vbo_fechaliberacion =  null where vbo_id = $laFila[0] and ast_codigo = 'blo'";
      }
      else {
         $this->pcError = "-1";
         return false;
      }

      $llOk = $p_oSql->omExec($lcSql);
      if (!$llOk) {
         $this->pcError = '-1';
         return false;
      }
      $laData = array("OK" => "1");
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function getTarifaAsiento() {
      $loSql = new CBase();
      $llOk = $loSql->omConnect();
      if (!$llOk) {
         $this->pcError = $loSql->pcError;
         return false;
      }
      $llOk = $this->mxValApikey($loSql);
      if (!$llOk) {
         $loSql->omDisconnect();
         return false;
      }
      $llOk = $this->mxgetTarifaAsiento($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetTarifaAsiento($p_oSql) {
      $laAsientos = $this->paData['asientos'];
      foreach($laAsientos as $laTmp){
         $lnAsiento = $laTmp['asiento'];
         $lnItinerarioID = $laTmp['itinerarioId'];
         $lnRutaID = $laTmp['rutaId'];
         $lcOrigen = $laTmp['origen'];
         $lcSql = "
         select c.vhc_numasiento as asiento,a.via_id as itinerarioId, a.rut_id as rutaId, 
          case when c.vhc_piso = 1 then via_precio1 else via_precio2 end as tarifa,k.lem_id
          from tblviajes_via a
          inner join tblvehiculos_veh b on a.veh_id = b.veh_id
          inner join tblvehiculoscroquis_vhc c on  b.veh_id = c.veh_id
          inner join dbo.tblrutas_rut d on a.rut_id = d.rut_id 
          inner join dbo.tblviajeslugarembarque_vle k on a.via_id = k.via_id 
          inner join  dbo.tbllugarembarque_lem l on l.lem_id = k.lem_id 
          inner join dbo.tblnodos_nod e on l.nod_codigo = e.nod_codigo 
          where a.via_id = '$lnItinerarioID' and a.rut_id = '$lnRutaID' and a.via_ventasinternet = 1 and c.vhc_numasiento = '$lnAsiento' and k.lem_id = '$lcOrigen'
                                  ";
         $RS = $p_oSql->omExec($lcSql);
         $laFila = $p_oSql->fetch($RS);
         if (empty($laFila[0])) {
            $laAsiento = array("asiento" => "NO DATA", "itinerarioId" => "NO DATA", "rutaId" => "NO DATA", "tarifa" => "NO DATA");
         }else{
            $laAsiento = array("asiento" => $laFila[0], "itinerarioId" => $laFila[1], "rutaId" => $laFila[2], "tarifa" => $laFila[3]);
         }
         $laData[] = $laAsiento;
      }      
      $this->DataJSON = json_encode($laData);
      return true;
   }

   protected function mxValApikey($p_oSql) {
        if (empty($this->paData['apikey'])){
         $this->pcError = "apikey no definida";
         return false;
      }
      $lcApikey = $this->paData['apikey'];
      $lcSql = "select codigo, codusu  from tblws_keys where '$lcApikey' =  CONVERT(VARCHAR(MAX), DECRYPTBYPASSPHRASE('password', Llave)) and level = 1";
      $RS = $p_oSql->omExec($lcSql);
      $laFila = $p_oSql->fetch($RS);
      if (empty($laFila[0])) {
         $this->pcError = "Apikey Invalida";
         return false;
      }
      $this->codigo = $laFila[0];
      $this->cod_busportal = $laFila[1];
      return true;
   }

   public function getlcError(){
      $lcError = array("ERROR" => $this->pcError);
      return json_encode($lcError);
   }
   
}