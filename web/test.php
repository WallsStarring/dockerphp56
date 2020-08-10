<?php
/*$username = 'sa';
$password = 'Abc123456';
$msconnect = mssql_connect('10.1.4.154', $username, $password);
if (!$msconnect) {  die('Not connected : ' . mssql_get_last_message());} 

//$msconnect=mssql_connect("sqlserver2012","sa","clave");
//$db_selected = mssql_select_db($database, $connection);

$msdb=mssql_select_db("TEST",$msconnect);
$msquery = "select * from persona ";
$msresults= mssql_query($msquery);
while ($row = mssql_fetch_array($msresults)) {
echo $row['codigo'].'<br>';
echo $row['nombre'];
}*/

require_once "DataAcces/CSql.php";
// Conexion a Base de Datos
class CBase {
   public $pcError;
   protected $h;

   public function __construct() {
      $this->pcError = null;
   }

   public function omDisconnect() {
      $this->omExec("COMMIT;");
      pg_close($this->h);
   }

   public function omConnect() {
      $lo = new CSql();
      $url = $lo->geturl();
      $pass = $lo->getpassword();
      $db   = $lo->getdbname(); 
      $user = $lo->getuser();
      $lcConStr = "host=$url dbname=$db port=5432 user=$user password=$pass";

      @$this->h = mssql_connect($url, $user, $pass) or die("Can't connect to database".mssql_get_last_message());
      if (!$this->h) {
         $this->pcError = "No se pudo conectar a la base de datos";
         return false;
      }
      $msdb=mssql_select_db($db, $this->h);
      $this->omExec("BEGIN;");
      return true;
   }

   public function omExec($p_cSql) {
      $lcSql = substr(strtoupper(trim($p_cSql)), 0, 6);
      if ($lcSql === "SELECT") {
      	echo 'aaa';
         //ELIMINAR PARA NO CONSUMIR RECURSOS
         $file = fopen("SELECT.txt", "a");
         fwrite($file, "**".date ("h:i:s")."/".date ("j/n/Y")."*****".PHP_EOL);
         fwrite($file, $p_cSql.PHP_EOL);
         fwrite($file, "*************************************************************".PHP_EOL);
         fclose($file);
         //*********************************
         echo 'bb';
         $this->pnNumRow = 0;
         echo 'ccc';
         echo '<br>1)'.$p_cSql.'<br>';
         $RS = mssql_query($p_cSql);
         echo 'ddd';
         if (!($RS)) {
            $this->pcError = "Error al ejecutar comando SQL";
            return false;
         }
         echo 'eee';
         $this->pnNumRow = mssql_num_rows($RS);
         echo 'fff';
         return $RS;
      } else {
         //ELIMINAR PARA NO CONSUMIR RECURSOS
         $file = fopen("INSERT_UPDATE.txt", "a");
         fwrite($file, "**".date ("h:i:s")."/".date ("j/n/Y")."*****".PHP_EOL);
         fwrite($file, $p_cSql.PHP_EOL);
         fwrite($file, "*************************************************************".PHP_EOL);
         fclose($file);
         //*********************************
         echo '<br>'.$p_cSql.'<br>';
         @$RS = mssql_query($p_cSql);
         if (mssql_rows_affected($this->h) == 0)
            if (!($RS)) {
               $this->pcError = "La operacion no afecto a ninguna fila";
               return false;
            }
         echo 'zz';
         return true;
      }
   }

   public function fetch($RS) {
      return mssql_fetch_array($RS);     
   }
   
   public function rollback() {
      $this->omExec("ROLLBACK;");
   }
}
$loSql = new CBase();
$llOk = $loSql->omConnect();
if (!$llOk) {
  $this->pcError = $loSql->pcError;
  return false;
}

      $lcApikey = 'sss';
      $lcSql = "select codigo, codusu from ws_keys where keyAccess = crypt('$lcApikey', keyAccess) and level = 1";
      $lcSql = "select codigo, codusu  from ws_keys where '$lcApikey' =  CONVERT(VARCHAR(MAX), DECRYPTBYPASSPHRASE('password', Llave)) and level = 1";

      $lcSql = "select codigo, codusu  from ws_keys where '#ojIOuhSOhs.98hsoHOh98soijHS8.U908jsoij9856tyug.lkj7wo9jhoa8' =  CONVERT(VARCHAR(MAX), DECRYPTBYPASSPHRASE('password', Llave)) and level = 1";
$lcSql = "select codigo, codusu  from ws_keys";
      echo '111';
      $RS = $loSql->omExec($lcSql);
      echo '222';
      $laFila = $loSql->fetch($RS);
      echo '333';
      if (empty($laFila[0])) {
         $pcError = "Apikey Invalida";
         echo $pcError;
      }
      $codigo = $laFila[0];
      $cod_busportal = $laFila[1];
      echo $codigo;
      echo $cod_busportal;
      //SELECT
      echo 'select';
      $lcSql = "select a.age_id as agenciaID, a.age_descripcion as agencia, a.age_direccion as direccion from tblagencias_age a";
      $RS = $loSql->omExec($lcSql);
      $i = 0;
      while ($laFila = $loSql->fetch($RS)) {
         $laData[] = array("agenciaID"=>$laFila[0], "agencia"=>$laFila[1], "direccion"=>$laFila[2]);
         $i++;
      }
      print_r($laData);
      if ($i==0) {
         $pcError = "NO DATA";
         return false;
      }echo 'fin';
      //UPDATE
     /* $lcSql = "UPDATE persona set codigo = 99999 where nombre = 'nueva'";
            $llOk = $loSql->omExec($lcSql);
            if (!$llOk) {
               $pcError = '-1';
               return false;
            }
            echo $pcError;

      //INSERT
$lcSql = "INSERT INTO persona values (7777,'nueva')";
            $llOk = $loSql->omExec($lcSql);
            if (!$llOk) {
               $pcError = '-1';
               return false;
            }
            echo $pcError;*/
      $lcSql = "select a.age_id as agenciaID, a.age_descripcion as agencia, a.age_direccion as direccion from tblagencias_age a";
   // $lcSql = "  select * FROM tblpuntosventa_pvt";
      $RS = $loSql->omExec($lcSql);
      $y = 0;
      while ($laFila = $loSql->fetch($RS)) {
         $laData1[] = array("agenciaID"=>$laFila[0], "agencia"=>$laFila[1], "direccion"=>$laFila[2]);
         $i++;
      }
      print_r($laData1);
      


?>