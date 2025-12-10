<?php
session_start();

require_once("../data/DB.php");

$fichier = "../data/config.json";

$reset_etat = [
  "j1" => null,
  "j2" => null,
  "j1_session_id" => null,
  "j2_session_id" => null,
  "taille_finale" => null
];
file_put_contents($fichier, json_encode($reset_etat, JSON_PRETTY_PRINT));

session_unset();
session_destroy();

header("Location: player.php");
exit;
