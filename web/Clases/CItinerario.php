<?php
require_once "DataAcces/CBase.php";

class CItinerario extends CBase {
   public $DataJSON, $codigo, $cod_busportal, $paData;

   public function __construct() {
      parent::__construct();
      $this->DataJSON = $this->paData = null;
   }
   
   public function getItinerario() {
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
      $llOk = $this->mxgetItinerario($loSql);
      //$loSql->omDisconnect();      
      return $llOk;
   }

   protected function mxgetItinerario($p_oSql) {      
      $ldFechaPartida  = $this->paData['fechaPartida'];
      $lcOrigen = $this->paData['origen'];
      $lcDestino = $this->paData['destino'];
      $lcSql = "select a.via_id as itinerarioID,c.nod_descripcion as agenciaPartida,  f.nod_descripcion as agenciaLlegada, a.rut_id as rutaID, cast(a.via_fechaviaje AS date) AS fechaPartida, 
convert(varchar(8),cast(a.via_horaviaje AS time)) AS horaPartida,CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada,
convert(varchar(8),cast (DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada, 
1 as piso, j.cat_id as servicioID, j.cat_nombre as servicioNombre,a.via_precio1 as tarifa
from dbo.tblviajes_via a 
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join dbo.tblnodos_nod c on b.nod_codigoorigen = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo 
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo 
inner join dbo.tblubigeos_ubi g on f.ubi_codigo = g.ubi_codigo 
inner join dbo.tblciudades_ubi h on g.ubi_codigociudad = h.id 
inner join dbo.tblvehiculos_veh i on a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join tbllugarembarque_lem l on l.lem_id = k.lem_id
where 
a.cat_id != 7 
and l.nod_codigo = b.nod_codigoorigen
and a.evi_codigo = 'PEN'
--and a.via_ventasinternet = 1
and a.via_fechaviaje = '$ldFechaPartida'
and l.lem_id = $lcOrigen
and a.lem_iddesembarque = $lcDestino
union all
select a.via_id as itinerarioID,c.nod_descripcion as agenciaPartida, f.nod_descripcion as agenciaLlegada,a.rut_id as rutaID, cast(a.via_fechaviaje AS date) AS fechaPartida, 
convert(varchar(8),cast(a.via_horaviaje AS time)) AS horaPartida,CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada,
convert(varchar(8),cast (DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada, 
i.veh_pisos as piso, j.cat_id as servicioID, j.cat_nombre as servicioNombre,a.via_precio1 as tarifa
from 
dbo.tblviajes_via a  
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join dbo.tblnodos_nod c on b.nod_codigoorigen = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo 
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo 
inner join dbo.tblubigeos_ubi g on f.ubi_codigo = g.ubi_codigo 
inner join dbo.tblciudades_ubi h on g.ubi_codigociudad = h.id 
inner join dbo.tblvehiculos_veh i on a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join tbllugarembarque_lem l on l.lem_id = k.lem_id
where 
a.cat_id != 7
and i.veh_pisos = 2
and l.nod_codigo = b.nod_codigoorigen
and a.evi_codigo = 'PEN'
--and a.via_ventasinternet = 1
and a.via_fechaviaje = '$ldFechaPartida'
and l.lem_id = $lcOrigen
and a.lem_iddesembarque = $lcDestino
union all
--Ndos intermedios 1Piso
select a.via_id as itinerarioID,c.nod_descripcion as agenciaPartida,  f.nod_descripcion as agenciaLlegada, a.rut_id as rutaID, cast(a.via_fechaviaje AS date) AS fechaPartida, 
convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) AS horaPartida,
CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada,
convert(varchar(8),cast (DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada, 
1 as piso, j.cat_id as servicioID, j.cat_nombre as servicioNombre,k.vle_precio1 as tarifa
from dbo.tblviajes_via a 
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join tbllugarembarque_lem l on l.lem_id = k.lem_id
inner join dbo.tblnodos_nod c on l.nod_codigo = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo 
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo 
inner join dbo.tblubigeos_ubi g on f.ubi_codigo = g.ubi_codigo 
inner join dbo.tblciudades_ubi h on g.ubi_codigociudad = h.id 
inner join dbo.tblvehiculos_veh i on a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
where 
a.cat_id != 7 
and a.evi_codigo = 'PEN'
--and a.via_ventasinternet = 1
and a.via_fechaviaje = '$ldFechaPartida'
and l.lem_id= $lcOrigen
and a.lem_iddesembarque = $lcDestino
and convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) between '09:00' and '23:30'
and k.via_id in (select a.via_id from dbo.tblviajes_via a inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id inner join tbllugarembarque_lem l on l.lem_id = k.lem_id where 
a.cat_id != 7 and a.evi_codigo = 'PEN' 
--and a.via_ventasinternet = 1
and  a.via_fechaviaje = '$ldFechaPartida' 
and l.lem_id= $lcOrigen
and a.lem_iddesembarque = $lcDestino
and e.id in (12/*NASCA*/)
and l.lem_id in (1009/*NASCA*/)
)
union all
--Nodos intermendio 2piso
select a.via_id as itinerarioID,c.nod_descripcion as agenciaPartida,  f.nod_descripcion as agenciaLlegada, a.rut_id as rutaID, cast(a.via_fechaviaje AS date) AS fechaPartida, 
convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) AS horaPartida,
CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada,
convert(varchar(8),cast (DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada, 
i.veh_pisos as piso, j.cat_id as servicioID, j.cat_nombre as servicioNombre,k.vle_precio1 as tarifa
from dbo.tblviajes_via a 
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join tbllugarembarque_lem l on l.lem_id = k.lem_id
inner join dbo.tblnodos_nod c on l.nod_codigo = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo 
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo 
inner join dbo.tblubigeos_ubi g on f.ubi_codigo = g.ubi_codigo 
inner join dbo.tblciudades_ubi h on g.ubi_codigociudad = h.id 
inner join dbo.tblvehiculos_veh i on a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
where 
a.cat_id != 7 
and i.veh_pisos = 2
and a.evi_codigo = 'PEN'
--and a.via_ventasinternet = 1
and a.via_fechaviaje = '$ldFechaPartida'
and l.lem_id=$lcOrigen
and a.lem_iddesembarque = $lcDestino
and convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) between '09:00' and '23:30'
and k.via_id in (select a.via_id from dbo.tblviajes_via a inner join tblviajeslugarembarque_vle k on a.via_id = k.via_id inner join tbllugarembarque_lem l on l.lem_id = k.lem_id where 
a.cat_id != 7 and a.evi_codigo = 'PEN' 
--and a.via_ventasinternet = 1
and  a.via_fechaviaje = '$ldFechaPartida' 
and l.lem_id= $lcOrigen 
and a.lem_iddesembarque = $lcDestino
and e.id in (12/*NASCA*/)
and l.lem_id in (1009/*NASCA*/)
)"; 
      $RS = $p_oSql->omExec($lcSql); 
      $i = 0;
      $laServicios = null;
      $laAnt = null;
      $laData = null;
      $llFirst = true;
      while ($laFila = $p_oSql->fetch($RS)) {
         if ($llFirst){
            $laServicios[] = array("piso"=>$laFila[8] , "servicioID"=>$laFila[9], "servicioNombre"=>$laFila[10], "tarifa"=>$laFila[11]); 
            $llFirst = false;
            $laAnt[0] = $laFila[0];
            $laAnt[1] = $laFila[1];
            $laAnt[2] = $laFila[2];
            $laAnt[3] = $laFila[3];
            $laAnt[4] = $laFila[4];
            $laAnt[5] = $laFila[5];
            $laAnt[6] = $laFila[6];
            $laAnt[7] = $laFila[7];
         }else{
            if ($laFila[0] == $laAnt[0]){
               $laServicios[] = array("piso"=>$laFila[8] , "servicioID"=>$laFila[9], "servicioNombre"=>$laFila[10], "tarifa"=>$laFila[11]);                
            }else {
               $laData[] = array("itinerarioID"=>$laAnt[0], "agenciaPartida"=>$laAnt[1], "agenciaLlegada"=>$laAnt[2], "rutaID"=>$laAnt[3], "fechaPartida"=>$laAnt[4], "horaPartida"=>$laAnt[5], "fechaLlegada"=>$laAnt[6], "horaLlegada"=>$laAnt[7], "servicios"=>$laServicios);
               $laServicios = null;
               $laServicios[] = array("piso"=>$laFila[8] , "servicioID"=>$laFila[9], "servicioNombre"=>$laFila[10], "tarifa"=>$laFila[11]);                
               $laAnt[0] = $laFila[0];
               $laAnt[1] = $laFila[1];
               $laAnt[2] = $laFila[2];
               $laAnt[3] = $laFila[3];
               $laAnt[4] = $laFila[4];
               $laAnt[5] = $laFila[5];
               $laAnt[6] = $laFila[6];
               $laAnt[7] = $laFila[7];          
            }            
         }
         $i++;
      } 
      $laData[] = array("itinerarioID"=>$laAnt[0], "agenciaPartida"=>$laAnt[1], "agenciaLlegada"=>$laAnt[2], "rutaID"=>$laAnt[3], "fechaPartida"=>$laAnt[4], "horaPartida"=>$laAnt[5], "fechaLlegada"=>$laAnt[6], "horaLlegada"=>$laAnt[7], "servicios"=>$laServicios);            
      if ($i==0) {
         $this->pcError = "NO DATA";
         return false;
      }
      $this->DataJSON = json_encode($laData);
      return true;
   }
   
   public function getTodoItinerario() {
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
      $llOk = $this->mxgetTodoItinerario($loSql); 
      //$loSql->omDisconnect(); 
      return $llOk;
   }

   public function mxgetTodoItinerario($p_oSql) {
      $ldFechaPartida  = $this->paData['fechaPartida'];
      $lcSql = "select 
      a.via_id as itinerarioID
      , a.rut_id as rutaID
      , e.id as origenID
      , e.descripcion as origen
      , h.id as destinoID
      , h.descripcion as destino
      , 1 as piso,j.cat_id as servicioID
      , j.cat_nombre as servicioNombre
      , a.via_precio1 as tarifa
      , convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada 
      , cast(a.via_fechaviaje AS date) AS fechaPartida
      , CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada
      , a.lem_iddesembarque as agenciaLlegadaID
      , f.nod_descripcion as agenciaLlegada
      , m.lem_descripcion as direccionLlegada
      , l.lem_id as agenciaPartidaID
      , c.nod_descripcion as agenciaPartida
      , convert(varchar(8), cast(a.via_horaviaje AS time)) AS horaPartida
      , l.lem_descripcion as direccion 
      from dbo.tblviajes_via a 
     inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
     inner join dbo.tblnodos_nod c on b.nod_codigoorigen = c.nod_codigo 
     inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo
     inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
    inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo
     inner join dbo.tblubigeos_ubi g on  f.ubi_codigo = g.ubi_codigo
     inner join dbo.tblciudades_ubi h on  g.ubi_codigociudad = h.id
     inner join dbo.tblvehiculos_veh i on  a.veh_id = i.veh_id
     inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
     inner join dbo.tblviajeslugarembarque_vle k on a.via_id = k.via_id 
     inner join  dbo.tbllugarembarque_lem l on l.lem_id = k.lem_id and l.nod_codigo = b.nod_codigoorigen
     inner join dbo.tbllugarembarque_lem m on a.lem_iddesembarque = m.lem_id
            where 
            a.evi_codigo = 'PEN'
            and a.via_ventasinternet = 1
            and a.cat_id != 7
            and a.via_fechaviaje = '$ldFechaPartida'
    
       union all
            select  
            a.via_id as itinerarioID
            , a.rut_id as rutaID
            , e.id as origenID
            , e.descripcion as origen
            , h.id as destinoID
            , h.descripcion as destino
            , i.veh_pisos as piso
            , j.cat_id as servicioID 
            , j.cat_nombre as servicioNombre
            , a.via_precio2 as tarifa
            , convert(varchar(8) , cast (DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada 
            , cast(a.via_fechaviaje AS date) AS fechaPartida
            , CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada
            , a.lem_iddesembarque as agenciaLlegadaID, f.nod_descripcion as agenciaLlegada
            , m.lem_descripcion as direccionLlegada
            , l.lem_id as agenciaPartidaID
            , c.nod_descripcion as agenciaPartida 
            , convert(varchar(8),cast(a.via_horaviaje AS time)) AS horaPartida
            , l.lem_descripcion as direccion 
            from dbo.tblviajes_via a  
         inner join dbo.tblrutas_rut b on  a.rut_id = b.rut_id
         inner join dbo.tblnodos_nod c on b.nod_codigoorigen = c.nod_codigo
         inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo
         inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
      inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo
         inner join dbo.tblubigeos_ubi g on f.ubi_codigo = g.ubi_codigo
         inner join dbo.tblciudades_ubi h on g.ubi_codigociudad = h.id
         inner join dbo.tblvehiculos_veh i on a.veh_id = i.veh_id and i.veh_pisos = 2
         inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
         inner join dbo.tblviajeslugarembarque_vle k on a.via_id = k.via_id
         inner join dbo.tbllugarembarque_lem l on l.lem_id = k.lem_id and l.nod_codigo = b.nod_codigoorigen
         inner join dbo.tbllugarembarque_lem m on a.lem_iddesembarque = m.lem_id
            where 
            a.evi_codigo = 'PEN'
            and a.via_ventasinternet = 1
            and a.cat_id != 7
            and a.via_fechaviaje =  '$ldFechaPartida'

union all
select distinct
a.via_id as itinerarioID
, a.rut_id as rutaID
, e.id as origenID
, c.nod_descripcion as origen
, h.id as destinoID
, h.descripcion as destino
, 1 as piso,j.cat_id as servicioID
, j.cat_nombre as servicioNombre
, a.via_precio1 as tarifa
, convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada 
, cast(a.via_fechaviaje AS date) AS fechaPartida
, CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada
, a.lem_iddesembarque as agenciaLlegadaID
, f.nod_descripcion as agenciaLlegada
, m.lem_descripcion as direccionLlegada
, lem.lem_id as agenciaPartidaID
, nod.nod_descripcion as agenciaPartida
--, convert(varchar(8), cast(a.via_horaviaje AS time)) AS horaPartida
--,rxn.rxn_tiempoviaje
, convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', rxn.rxn_tiempoviaje),'00:00:00' ) as time(1))) AS horaPartida2
, lem.lem_descripcion as direccion 
from dbo.tblviajes_via a 
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join tblrutasnodos_rxn rxn on a.rut_id=rxn.rut_id
inner join dbo.tblnodos_nod nod on rxn.nod_codigo = nod.nod_codigo 
inner join  dbo.tbllugarembarque_lem lem on nod.nod_codigo = lem.nod_codigo
inner join dbo.tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join  dbo.tbllugarembarque_lem l on l.lem_id = k.lem_id --and l.nod_codigo = b.nod_codigoorigen
inner join dbo.tblnodos_nod c on l.nod_codigo = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo
inner join dbo.tblubigeos_ubi g on  f.ubi_codigo = g.ubi_codigo
inner join dbo.tblciudades_ubi h on  g.ubi_codigociudad = h.id
inner join dbo.tblvehiculos_veh i on  a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
inner join dbo.tbllugarembarque_lem m on a.lem_iddesembarque = m.lem_id
where 
a.evi_codigo = 'PEN'
and a.via_ventasinternet = 1
--and l.nod_codigo = b.nod_codigoorigen
and a.cat_id != 7
and a.via_fechaviaje = '$ldFechaPartida'
and convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) between '09:00' and '23:30'
and e.id in (12/*NASCA*/)
and lem.lem_id in (1009/*NASCA*/)

union all
select distinct
a.via_id as itinerarioID
, a.rut_id as rutaID
, e.id as origenID
, c.nod_descripcion as origen
, h.id as destinoID
, h.descripcion as destino
, i.veh_pisos as piso,j.cat_id as servicioID
, j.cat_nombre as servicioNombre
, a.via_precio2 as tarifa
, convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', b.rut_tiempoviaje),'00:00:00' ) as time(1))) as horaLlegada 
, cast(a.via_fechaviaje AS date) AS fechaPartida
, CAST(dateadd(day, datediff(day,'19000101',a.via_fechaviaje), CAST(a.via_horaviaje as datetime)) + CAST(b.rut_tiempoviaje as datetime) as date) as fechaLlegada
, a.lem_iddesembarque as agenciaLlegadaID
, f.nod_descripcion as agenciaLlegada
, m.lem_descripcion as direccionLlegada
, lem.lem_id as agenciaPartidaID
, nod.nod_descripcion as agenciaPartida
--, convert(varchar(8), cast(a.via_horaviaje AS time)) AS horaPartida
--,rxn.rxn_tiempoviaje
, convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', a.via_horaviaje)+DATEDIFF(S,'00:00:00', rxn.rxn_tiempoviaje),'00:00:00' ) as time(1))) AS horaPartida2
, lem.lem_descripcion as direccion 
from dbo.tblviajes_via a 
inner join dbo.tblrutas_rut b on a.rut_id = b.rut_id 
inner join tblrutasnodos_rxn rxn on a.rut_id=rxn.rut_id
inner join dbo.tblnodos_nod nod on rxn.nod_codigo = nod.nod_codigo 
inner join  dbo.tbllugarembarque_lem lem on nod.nod_codigo = lem.nod_codigo
inner join dbo.tblviajeslugarembarque_vle k on a.via_id = k.via_id 
inner join  dbo.tbllugarembarque_lem l on l.lem_id = k.lem_id --and l.nod_codigo = b.nod_codigoorigen
inner join dbo.tblnodos_nod c on l.nod_codigo = c.nod_codigo 
inner join dbo.tblubigeos_ubi d on c.ubi_codigo = d.ubi_codigo
inner join dbo.tblciudades_ubi e on d.ubi_codigociudad = e.id
inner join dbo.tblnodos_nod f on b.nod_codigodestino = f.nod_codigo
inner join dbo.tblubigeos_ubi g on  f.ubi_codigo = g.ubi_codigo
inner join dbo.tblciudades_ubi h on  g.ubi_codigociudad = h.id
inner join dbo.tblvehiculos_veh i on  a.veh_id = i.veh_id
inner join dbo.tblcategorias_cat j on i.cat_id = j.cat_id
inner join dbo.tbllugarembarque_lem m on a.lem_iddesembarque = m.lem_id
where 
a.evi_codigo = 'PEN'
and a.via_ventasinternet = 1
--and l.nod_codigo = b.nod_codigoorigen
and i.veh_pisos = 2
and a.cat_id != 7
and a.via_fechaviaje = '$ldFechaPartida'
and convert(varchar(8),cast(DATEADD(S,DATEDIFF(S,'00:00:00', (select rxn_tiempoviaje from tblrutasnodos_rxn where nod_codigo=c.nod_codigo AND rut_id=a.rut_id)),cast(a.via_horaviaje as time)) as time)) between '09:00' and '23:30'
and e.id in (12/*NASCA*/)
and lem.lem_id in (1009/*NASCA*/)
";

      $RS = $p_oSql->omExec($lcSql); 
      $i = 0; 
      $laServicios = null;
      $laPuntoEmbarque = null;
      $laAnt = null;
      $laData = null;
      $llFirst = true;
      $llFirst1 = true;

      while ($laFila = $p_oSql->fetch($RS)){ 
         if ($llFirst){ 
            $laServicios[] = array("piso"=>$laFila[6], "servicioID"=>$laFila[7], "servicioNombre"=>$laFila[8], "tarifa"=>$laFila[9]);
            $laPuntoEmbarque[] = array("agenciaPartidaID"=>$laFila[16], "agenciaPartida"=>$laFila[17], "horaPartida"=>$laFila[18], "direccion"=>utf8_decode($laFila[19]));
            $llFirst = false;
            $laAnt[0] = $laFila[0];
            $laAnt[1] = $laFila[1];
            $laAnt[2] = $laFila[2];
            $laAnt[3] = $laFila[3];
            $laAnt[4] = $laFila[4];
            $laAnt[5] = $laFila[5];
            $laAnt[6] = $laFila[6];
            $laAnt[7] = $laFila[7];
            $laAnt[8] = $laFila[8];
            $laAnt[9] = $laFila[9];
            $laAnt[10] = $laFila[10];
            $laAnt[11] = $laFila[11];
            $laAnt[12] = $laFila[12];
            $laAnt[13] = $laFila[13];
            $laAnt[14] = $laFila[14];
            $laAnt[15] = $laFila[15]; 
            $laAnt[16] = $laFila[16];            
         }else{
            if ($laFila[0] == $laAnt[0]){
               if ($laFila[6] == $laAnt[6]){
                  if (!$laFila[16] == $laAnt[16]){
                     $laPuntoEmbarque[] = array("agenciaPartidaID"=>$laFila[16], "agenciaPartida"=>$laFila[17], "horaPartida"=>$laFila[18], "direccion"=>utf8_decode($laFila[19]));
                     $laAnt[16] = $laFila[16];
                  }
               }else if ($llFirst1){
                  $laServicios[] = array("piso"=>$laFila[6], "servicioID"=>$laFila[7], "servicioNombre"=>$laFila[8], "tarifa"=>$laFila[9]);                  
                  $llFirst1 = false;
               }               
            }else {
               $laData[] = array("itinerarioID"=>$laAnt[0], "rutaID"=>$laAnt[1], "origenID"=>$laAnt[2], "origen"=>$laAnt[3], "destinoID"=>$laAnt[4], "destino"=>$laAnt[5], "servicios"=>$laServicios, "horaLlegada"=>$laAnt[10], "fechaPartida"=>$laAnt[11], "fechaLlegada"=>$laAnt[12], "agenciaLlegadaID"=>$laAnt[13], "agenciaLlegada"=>$laAnt[14], "direccionLlegada"=>utf8_decode($laAnt[15]), "IstPuntoEmbarque"=>$laPuntoEmbarque);
               $laServicios = null;
               $laPuntoEmbarque = null;
               $laServicios[] = array("piso"=>$laFila[6], "servicioID"=>$laFila[7], "servicioNombre"=>$laFila[8], "tarifa"=>$laFila[9]);
               $laPuntoEmbarque[] = array("agenciaPartidaID"=>$laFila[16], "agenciaPartida"=>$laFila[17], "horaPartida"=>$laFila[18], "direccion"=>utf8_decode($laFila[19]));
               $laAnt[0] = $laFila[0];
               $laAnt[1] = $laFila[1];
               $laAnt[2] = $laFila[2];
               $laAnt[3] = $laFila[3];
               $laAnt[4] = $laFila[4];
               $laAnt[5] = $laFila[5];
               $laAnt[6] = $laFila[6];
               $laAnt[7] = $laFila[7];
               $laAnt[8] = $laFila[8];
               $laAnt[9] = $laFila[9];
               $laAnt[10] = $laFila[10];
               $laAnt[11] = $laFila[11];
               $laAnt[12] = $laFila[12];
               $laAnt[13] = $laFila[13];
               $laAnt[14] = $laFila[14];
               $laAnt[15] = $laFila[15];
               $laAnt[16] = $laFila[16];
               $llFirst1 = true;
            }
         }
         $i++; 
      }
      $laData[] = array("itinerarioID"=>$laAnt[0], "rutaID"=>$laAnt[1], "origenID"=>$laAnt[2], "origen"=>$laAnt[3], "destinoID"=>$laAnt[4], "destino"=>$laAnt[5], "servicios"=>$laServicios, "horaLlegada"=>$laAnt[10], "fechaPartida"=>$laAnt[11], "fechaLlegada"=>$laAnt[12], "agenciaLlegadaID"=>$laAnt[13], "agenciaLlegada"=>$laAnt[14],"direccionLlegada"=>utf8_decode($laAnt[15]), "IstPuntoEmbarque"=>$laPuntoEmbarque);
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