<?php
require_once "DataAcces/CBase.php";

class CBusOcupabilidad extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function getEstructuraBusOcupabilidad() {
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
      $llOk = $this->mxgetEstructuraBusOcupabilidad($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetEstructuraBusOcupabilidad($p_oSql) {
      $lnItinerarioID  = $this->paData['itinerarioID'];
      $lnRutaID = $this->paData['rutaID'];
      $lcSql = "
select c.vhc_numasiento as asiento,
case when c.vhc_numasiento = 0 then 0 else case when c.vhc_piso = 1 then case when c.vhc_tipo = 'AE' then a.via_precio1 else a.via_precio2 end 
else a.via_precio2 end  end as tarifaAsiento, 
case when c.vhc_numasiento = 0 then 0 else case when c.vhc_numasiento in (select vbo_numasiento from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID 
and ast_codigo != 'lib' or vbo_numasiento in (54,55,60)) then 0 else 1 end end as estadoAsiento,
c.vhc_x as columna, c.vhc_y as fila, c.vhc_piso as piso,d.MaxAsi as cantidadAsientos, d.MaxCol as cantidadColumnas , d.MaxFil as cantidadFila,
b.veh_pisos as numeroPisos, 
case when c.vhc_numasiento in (select vbo_numasiento from pasajes.tblviajesasientos_vbo where via_id = $lnItinerarioID and ast_codigo != 'lib') then 7 else
case c.vhc_tipo when 'AE' then 0 when 'AS' then 0 when 'TV' then 1 when 'CF' then 2 when 'SH' then 3 when 'ES' then 4 else 7  end end as tipoObjeto
from dbo.tblviajes_via a,dbo.tblvehiculos_veh b , tblvehiculoscroquis_vhc c ,
(select m.veh_id , m.vhc_piso, max(m.vhc_x ) as MaxCol, max (m.vhc_y ) as MaxFil, count (m.vhc_numasiento ) as MaxAsi from dbo.tblvehiculoscroquis_vhc m, dbo.tblviajes_via n
where m.vhc_numasiento != 0 and n.veh_id = m.veh_id  and n.via_id = $lnItinerarioID group by m.veh_id, m.vhc_piso) as d
where a.veh_id = b.veh_id and b.veh_id = c.veh_id and a.veh_id = d.veh_id and c.vhc_piso = d.vhc_piso and a.via_ventasinternet = 1 and a.via_id = $lnItinerarioID and a.rut_id = $lnRutaID 
      ";
      $RS = $p_oSql->omExec($lcSql); 
      $i = 0;
      while ($laFila = $p_oSql->fetch($RS)) {
         $laData[] = array("asiento"=>$laFila[0], "tarifaAsiento"=>$laFila[1], "estadoAsiento"=>$laFila[2], "columna"=>$laFila[3], "fila"=>$laFila[4], "piso"=>$laFila[5], "cantidadAsientos"=>$laFila[6], "cantidadColumnas"=>$laFila[7], "cantidadFilas"=>$laFila[8], "numeroPisos"=>$laFila[9], "tipoObjeto"=>$laFila[10]);
         $i++;
      }
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