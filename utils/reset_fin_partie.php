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

unset($_SESSION['role']);
unset($_SESSION['game_id']);
unset($_SESSION['user_id']);
unset($_SESSION['taille_grille']);
unset($_SESSION['notification_abandon']);

header("Location: player.php");
exit;
