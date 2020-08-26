<?php
// Conexion a Base de Datos
class CSql {
   private $url = "207.244.254.19";
   private $dbname = "DBCruzDelSurPreProduccion";
   private $user = "sa";
   private $password = "Golft0123";

   
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