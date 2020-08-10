<?php
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
      //$lcConStr = "host=$url dbname=$db port=5432 user=$user password=$pass";
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
         //ELIMINAR PARA NO CONSUMIR RECURSOS
         $file = fopen("SELECT.txt", "a");
         fwrite($file, "**".date ("h:i:s")."/".date ("j/n/Y")."*****".PHP_EOL);
         fwrite($file, $p_cSql.PHP_EOL);
         fwrite($file, "*************************************************************".PHP_EOL);
         fclose($file);
         //*********************************
         $this->pnNumRow = 0; 
         $RS = mssql_query($p_cSql); 
         if (!($RS)) {
            $this->pcError = "Error al ejecutar comando SQL";
            return false;
         }
         $this->pnNumRow = mssql_num_rows($RS); 
         return $RS;
      } else {
         //ELIMINAR PARA NO CONSUMIR RECURSOS
         $file = fopen("INSERT_UPDATE.txt", "a");
         fwrite($file, "**".date ("h:i:s")."/".date ("j/n/Y")."*****".PHP_EOL);
         fwrite($file, $p_cSql.PHP_EOL);
         fwrite($file, "*************************************************************".PHP_EOL);
         fclose($file);
         //*********************************
         @$RS = mssql_query($p_cSql);
         if (mssql_rows_affected($this->h) == 0)
            if (!($RS)) {
               $this->pcError = "La operacion no afecto a ninguna fila";
               return false;
            }
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