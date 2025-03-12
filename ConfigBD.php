<?php
$host="db4free.net";
$usuario="julismosilva";
$senha="FarinhaJulismo";
$banco="escolaepbjc3"

$conn=mysqli_connect($host,$usuario,$senha,$banco);

if(!$conn){
    die("Conexão falhou : " . mysqli_connect_error());
}

echo "Conexão bem sucedida";

?>