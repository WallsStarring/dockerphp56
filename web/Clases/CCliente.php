<?php
require_once "DataAcces/CBase.php";

class CCliente extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function getCliente() {
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
      $llOk = $this->mxgetCliente($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetCliente($p_oSql) {
      $lcNumeroRuc  = $this->paData['numeroRuc'];
      $lcSql = "select ent_id as clienteID, ent_nrodocumento as numeroRuc, ent_nombre as razonSocial, ent_direccion as domicilioSocial, nac_codigo as nacionalidadID from dbo.tblentidades_ent WHERE ent_tipodocumento = 'RUC' and ent_nrodocumento ='$lcNumeroRuc'";
      $RS = $p_oSql->omExec($lcSql);   
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("ClienteID"=>$laFila[0], "numeroRuc"=>$laFila[1], "razonSocial"=>utf8_decode($laFila[2]), "domicilioSocial"=>utf8_decode($laFila[3]), "nacionalidadID"=>$laFila[4]);
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function setCliente() {
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
      $llOk = $this->mxsetCliente($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxsetCliente($p_oSql) {
      $lnClienteID = $this->paData['clienteID'];
      $lcNumeroRuc  = $this->paData['numeroRuc'];
      $lcRazonSocial  = $this->paData['razonSocial'];
      $lcDomicilioSocial = $this->paData['domicilioSocial'];
      $lcNacionalidadID = $this->paData['nacionalidadID'];

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
      
      if($lnClienteID == "null" || $lnClienteID == 0 || $lnClienteID == ""){
         $lcSql = "select ent_id from dbo.tblentidades_ent WHERE ent_tipodocumento = 'RUC' and ent_nrodocumento ='$lcNumeroRuc'";
         $RS = $p_oSql->omExec($lcSql);
         $laFila = $p_oSql->fetch($RS);
         if (!empty($laFila[0])) {
            $this->pcError = "-2";
            return false;
         }else{

            $lcSql = "insert into dbo.tblentidades_ent (ent_tipodocumento, ent_nrodocumento, ent_nombre, ent_direccion, ent_usucreacion, ent_feccreacion, nac_codigo) values ('RUC','$lcNumeroRuc','$lcRazonSocial','$lcDomicilioSocial','$Usuario', SYSDATETIME(),$lcNacionalidadID)";
            $llOk = $p_oSql->omExec($lcSql); 
            if (!$llOk) {
               $this->pcError = '-1';
               return false;
            }
         }
      }else{
         $lcSql = "select ent_id from dbo.tblentidades_ent WHERE ent_tipodocumento = 'RUC' and ent_nrodocumento ='$lcNumeroRuc'";
         $RS = $p_oSql->omExec($lcSql);
         $laFila = $p_oSql->fetch($RS);
         if (!empty($laFila[0])) {
            $lcSql = "update dbo.tblentidades_ent set ent_nrodocumento = '$lcNumeroRuc' , ent_nombre = '$lcRazonSocial' , ent_direccion = '$lcDomicilioSocial', 
                     ent_usumodificacion = '$Usuario', ent_fecmodificacion = SYSDATETIME(), nac_codigo = '$lcNacionalidadID' where ent_id = '$lnClienteID' ";
            $llOk = $p_oSql->omExec($lcSql);
            if (!$llOk) {
               $this->pcError = '-1';
               return false;
            }
         }else{
            $this->pcError = 'Si es nuevo ClienteID debe ser 0';
            return false;
         }
      }
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