<?php
session_start();
header('Content-Type: application/json');

require_once("../data/DB.php");
require_once("logique_partie.php");

if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Session expirée ou non valide.']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
  exit;
}

$game_id = $_SESSION['game_id'];
$joueur_actuel_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);

$x = isset($data['x']) ? (int)$data['x'] : null;
$y = isset($data['y']) ? (int)$data['y'] : null;
$adversaire_id = isset($data['cible_id']) ? (int)$data['cible_id'] : null;

if ($x === null || $y === null || $adversaire_id === null) {
  echo json_encode(['success' => false, 'message' => 'Coordonnées de tir manquantes.']);
  exit;
}

$tour_actuel_id = obtenir_tour_actuel($pdo, $game_id);

if ($tour_actuel_id != $joueur_actuel_id) {
  echo json_encode(['success' => false, 'message' => 'Ce n\'est pas votre tour de jouer.']);
  exit;
}

$tailleMatrice = 10;
$grille_temp = creerMatrice($tailleMatrice);

$message_tir = tirer($pdo, $game_id, $joueur_actuel_id, $adversaire_id, $grille_temp, $x, $y);

changer_tour($pdo, $game_id, $adversaire_id);

echo json_encode(['success' => true, 'message' => trim($message_tir)]);
