<?php
// Conexion a Base de Datos
class CSql {
   private $url = "207.244.253.101";
   private $dbname = "DBCromotexPreProduccion";
   private $user = "sa";
   private $password = "u6gMxE5N8pa4sHoF";

   
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