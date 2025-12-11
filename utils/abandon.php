<?php
session_start();
require_once("../data/DB.php");
require_once("logique_partie.php");

if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {
    header("Location: player.php");
    exit;
}

$game_id = $_SESSION['game_id'];
$mon_id = $_SESSION['user_id'];

$adversaire_id = recuperer_id_adversaire($pdo, $game_id, $mon_id);

$stmt = $pdo->prepare("UPDATE games SET status = 'abandonned', winner_id = ? WHERE id = ?");
$stmt->execute([$adversaire_id, $game_id]);

header("Location: partie_terminée.php");
exit;
