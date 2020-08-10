<?php
require_once "DataAcces/CBase.php";

class CTablas extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function getAgencias() {
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
      $llOk = $this->mxgetAgencias($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetAgencias($p_oSql) {
      $lcSql = "select a.lem_id,b.nod_descripcion, a.lem_descripcion from tbllugarembarque_lem a, tblnodos_nod b where a.nod_codigo = b.nod_codigo";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("agenciaID"=> $laFila[0], "agencia"=>$laFila[1], "direccion"=>utf8_decode($laFila[2]));
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }

   public function getCiudades() {
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
      $llOk = $this->mxgetCiudades($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetCiudades($p_oSql) {
      $lcSql = " Select * from tblciudades_ubi";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("ciudadID"=>$laFila[0], "Denominacion"=>$laFila[1]);
         $i++;
      }

      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function getTipoDocumento() {
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
      $llOk = $this->mxgetTipoDocumento($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetTipoDocumento($p_oSql) {
      $lcSql = "select xtipo as tipoDocumentoID , tdi_nombre as denominacion from tbltdocumentosiden_tdi";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("tipoDocumentoID"=>$laFila[0], "denominacion"=>$laFila[1]);
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function getSexo() {
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
      $llOk = $this->mxgetSexo($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetSexo($p_oSql) {
      $lcSql = "select * from tblsexo";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("sexoID"=>$laFila[0], "sexo"=>$laFila[1]);
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function getNacionalidad() {
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
      $llOk = $this->mxgetNacionalidad($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetNacionalidad($p_oSql) {
      $lcSql = "select nac_codigo as nacionalidadID, nac_descripcion as nacionalidad from dbo.tblnacionalidad_nac";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("nacionalidadID"=>$laFila[0], "nacionalidad"=>$laFila[1]);
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function politicasCancelacion() {
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
      $llOk = $this->mxpoliticasCancelacion($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxpoliticasCancelacion($p_oSql) {
      $lcSql = "select * from tblpolitica_cancelacion ";
      $RS = $p_oSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("horaMax"=>$laFila[0], "horaMin"=>$laFila[1], "tipoHoras"=>$laFila[2], "penalidad"=>$laFila[3]);
         $i++;
      }
      $laData = array("politicas" => $laData);
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
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
