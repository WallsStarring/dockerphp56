<?php
require_once "DataAcces/CBase.php";

class CVenta extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function setVenta() {
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
      $llOk = $this->mxsetVenta($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxsetVenta($p_oSql) {
     $lnItinerarioID  = $this->paData['itinerarioID'];
      $lnRutaID  = $this->paData['rutaID'];
      $lnNumeroAsiento = $this->paData['numeroAsiento'];
      $lnPasajeroID = $this->paData['pasajeroID'];
      $lnClienteID = $this->paData['clienteID'];
      $lnTarifa = $this->paData['tarifa'];
      $lnAgenciaPartidaID = $this->paData['agenciaPartidaID'];
      $lnFechaPartida = $this->paData['fechaPartida'];

      $lcSql = "select vbo_id from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento and ast_codigo = 'ven'";
      $RS = $p_oSql->omExec($lcSql);
      $laFila = $p_oSql->fetch($RS);
      if (!empty($laFila[0])) {
         $this->pcError = "-2"; 
         return false;
      }
      $lcSql = "select vbo_id from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento and ast_codigo = 'int'";
      $RS = $p_oSql->omExec($lcSql);
      $laFila = $p_oSql->fetch($RS);
      if (!empty($laFila[0])) {
         $this->pcError = "-2"; 
         return false;
      }
      $lcSql = "select bol_id from pasajes.tblboletos_bol where via_id = $lnItinerarioID and bol_numasiento = $lnNumeroAsiento";
      $RS = $p_oSql->omExec($lcSql);
      $laFila = $p_oSql->fetch($RS);
      if (!empty($laFila[0])) {
         $this->pcError = "-2"; 
         return false;
      }
       $lcSql = "select vbo_id from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento and ast_codigo = 'blo'";
      $RS = $p_oSql->omExec($lcSql); 
      $laFila = $p_oSql->fetch($RS); 
      if (empty($laFila[0])) {
         $this->pcError = "-1"; 
         return false;
      }
     $lcSql = "select ent_tipodocumento from dbo.tblentidades_ent where ent_id = $lnPasajeroID";
      $RS = $p_oSql->omExec($lcSql); 
      $laFila = $p_oSql->fetch($RS);
      if (empty($laFila[0])) {
         $this->pcError = "-1"; 
         return false;
      }
     if ($lnClienteID != "" && $lnClienteID != "null") {
          $lcSql = "select ent_tipodocumento from dbo.tblentidades_ent where ent_id = $lnClienteID and ent_tipodocumento = 'RUC'";
         $RS = $p_oSql->omExec($lcSql); 
         $laFila = $p_oSql->fetch($RS);
         if (empty($laFila[0])) {
           $this->pcError = "-1"; 
         return false;
         }
     }
      if ($this->codigo == "000001") {
           $FormaPago = "RED";
           $Usuario = "REDBUS";
        }
       if ($this->codigo == "000002") {
           $FormaPago = "INT";
           $Usuario = "REDBUS";
        }
        if ($this->codigo == "000003") {
           $FormaPago = "REC";
           $Usuario = "RECORRIDO";
        }
        if ($this->codigo == "000004") {
           $FormaPago = "REA";
           $Usuario = "REDBUS";
        }

     $lcSql = "update pasajes.tblviajesasientos_vbo set ast_codigo = 'int', cen_cod = 'POL', ent_id = $lnPasajeroID , vbo_usucreacion = '$Usuario', vbo_usumodificacion = '$Usuario', vbo_fecmodificacion = SYSDATETIME(), vbo_fechaliberacion = null, vbo_monto = '$lnTarifa', fdp_codigo = '$FormaPago', bol_fecembarque = '$lnFechaPartida', lem_id = '$lnAgenciaPartidaID'
       where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento"; 
      $llOk = $p_oSql->omExec($lcSql); 
      if (!$llOk) {
         $this->pcError = '-1';
         return false;
      }
   if ($lnClienteID != "" && $lnClienteID != "null") {
         $lcSql = "update pasajes.tblviajesasientos_vbo set ent_idruc = '$lnClienteID' where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento";
         $llOk = $p_oSql->omExec($lcSql);
         if (!$llOk) {
            $this->pcError = '-1';
            return false;
         }
     }
   $lcSql = "select vbo_id from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID and vbo_numasiento = $lnNumeroAsiento";
   $RS = $p_oSql->omExec($lcSql); 
   $laFila = $p_oSql->fetch($RS);
   
   $i = 0;
         if (empty($laFila[0])) {
           $this->pcError = "-1"; 
         return false;
         }
      else {
         $laData[] = array("numeroComprobante"=>$laFila[0], "OK"=>"1");
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
        return true;

   }
   
   public function cancelarVenta() {
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
      $llOk = $this->mxcancelarVenta($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxcancelarVenta($p_oSql) {
      $lnItinerarioID  = $this->paData['itinerarioID'];
      $lnNumeroComprobante = $this->paData['numeroComprobante'];
      //$lcSql = "";
      //$llOk = $p_oSql->omExec($lcSql);
      //if (!$llOk) {
         $this->pcError = '-1';
         return false;
      // }
      $laData = array("OK" => "1");
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
