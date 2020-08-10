<?php
// Conexion a Base de Datos
class CSql {
   private $url = "10.1.4.154";
   private $dbname = "DBCromotexPruebaProduccionV1";
   private $user = "sa";
   private $password = "Abc123456";

   
   public function geturl(){
       return $this->url;
   }
   
   public function getuser(){
       return $this->user;
   }
   
   public function getpassword(){
       return $this->password;
   }
   
   public function getdbname(){
       return $this->dbname;
   }
}