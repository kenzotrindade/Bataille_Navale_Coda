<?php

$host = '127.0.0.1';
$dbname = 'bataille_navale';
$user = 'root';
$pass = '1234';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
  echo "Connexion OK";
} catch (Exception $e) {
  die('Erreur : ' . $e->getMessage());
}
