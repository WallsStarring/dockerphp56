<?php
require_once "DataAcces/CBase.php";

class CPasajero extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function getPasajero() {
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
      $llOk = $this->mxgetPasajero($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetPasajero($p_oSql) {
      $lntipoDocumentoID  = $this->paData['tipoDocumentoID'];
      $lnnumeroDocumento = $this->paData['numeroDocumento'];
      $lcSql = "select b.ent_id as pasajeroID, b.ent_apellidomaterno as apellidoMaterno, b.ent_apellidopaterno as  apellidoPaterno, 
                  b.ent_nombre as nombres, b.ent_sexo as sexoID, c.sex_descripcion as sexo ,0 as indeseable,null as motivo
                  from tbltdocumentosiden_tdi a, dbo.tblentidades_ent b, tblsexo c where a.tdi_codigo = b.ent_tipodocumento and b.ent_sexo = c.sex_codigo and a.xtipo = '$lntipoDocumentoID' and b.ent_nrodocumento =  '$lnnumeroDocumento'";
      $RS = $p_oSql->omExec($lcSql);     
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("PasajeroID"=>$laFila[0], "apellidoMaterno"=>utf8_decode($laFila[1]), "apellidoPaterno"=>utf8_decode($laFila[2]), "nombres"=>utf8_decode($laFila[3]), "sexoID"=>$laFila[4], "sexo"=>$laFila[5], "indeseable"=>$laFila[6], "motivo"=>$laFila[7]);
         $i++;
      }
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function setPasajero() {
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
      $llOk = $this->mxsetPasajero($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxsetPasajero($p_oSql) {
      $lcPasajeroID = $this->paData['pasajeroID'];
      $lnnumeroDocumento = $this->paData['numeroDocumento'];
      $lnTipoDocumento = $this->paData['tipoDocumentoID'];
      $lcApellidoPaterno = $this->paData['apellidoPaterno'];
      $lcApellidoMaterno = $this->paData['apellidoMaterno'];
      $lcSexoID = $this->paData['sexoID'];
      $lcNombres = $this->paData['nombres'];
      

      if($lcPasajeroID == NULL){
         $lcSql = "select b.ent_id from tbltdocumentosiden_tdi a, dbo.tblentidades_ent b where a.tdi_codigo = b.ent_tipodocumento 
                     and a.xtipo = '$lnTipoDocumento' and b.ent_nrodocumento =  '$lnnumeroDocumento'";
         $RS = $p_oSql->omExec($lcSql);
         $laFila = $p_oSql->fetch($RS);
         if (!empty($laFila[0])) {
            $this->pcError = "-2";
            return false;
         }
      }
      $lcSql = "select b.ent_id from tbltdocumentosiden_tdi a, dbo.tblentidades_ent b where a.tdi_codigo = b.ent_tipodocumento 
                  and a.xtipo = '$lnTipoDocumento' and b.ent_nrodocumento =  '$lnnumeroDocumento'";
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
      if (!empty($laFila[0])) {
         $lcSql = "update  dbo.tblentidades_ent set ent_tipodocumento = (select tdi_codigo from tbltdocumentosiden_tdi  where xtipo = '$lnTipoDocumento'), ent_nrodocumento = '$lnnumeroDocumento', ent_sexo = '$lcSexoID', ent_usumodificacion = '$Usuario', ent_fecmodificacion = SYSDATETIME()  where ent_id = $laFila[0]
              "; 
         $llOk = $p_oSql->omExec($lcSql);
         if (!$llOk) {
            $this->pcError = '-1';
            return false;
         }
      }else{
         if ($lcPasajeroID == null){
            $lcSql = "insert into dbo.tblentidades_ent (ent_tipodocumento,ent_nrodocumento, ent_apellidopaterno, ent_apellidomaterno, ent_nombre, ent_sexo
                      ,ent_usucreacion, ent_feccreacion) 
                      values ( (select tdi_codigo from tbltdocumentosiden_tdi  where xtipo = '$lnTipoDocumento'), '$lnnumeroDocumento','$lcApellidoPaterno',
                      '$lcApellidoMaterno','$lcNombres', '$lcSexoID','$Usuario', SYSDATETIME())";
            $llOk = $p_oSql->omExec($lcSql);
            if (!$llOk) {
               $this->pcError = '-1';
               return false;
            }
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