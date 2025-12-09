<?php
session_start();

require_once("../data/DB.php");
$fichier = "../data/config.json";

if (!file_exists($fichier)) {
  file_put_contents($fichier, json_encode([
    "j1" => null,
    "j2" => null,
    "j1_session_id" => null,
    "j2_session_id" => null,
    "taille_finale" => null
  ]));
}

$etat = json_decode(file_get_contents($fichier), true);

function save_state($file, $data)
{
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

if ($etat["taille_finale"] !== null) {
  if (isset($_SESSION['game_id']) && isset($_SESSION['user_id'])) {
    header("Location: ../GUI/GUI_matrice.php");
  } else {
    header("Location: player.php");
  }
  exit;
}

if ($etat["j1_session_id"] === null || $etat["j2_session_id"] === null) {
  header("Location: player.php");
  exit;
}

if (!isset($_SESSION["role"])) {
  $current_session_id = session_id();

  if ($etat["j1_session_id"] === $current_session_id) {
    $_SESSION["role"] = "Joueur 1";
  } elseif ($etat["j2_session_id"] === $current_session_id) {
    $_SESSION["role"] = "Joueur 2";
  } else {
    header("Location: player.php");
    exit;
  }
}

if (isset($_POST["taille"])) {
  $taille = (int) $_POST["taille"];

  if ($taille < 10 || $taille > 20 || $taille % 2 !== 0) {
    $error = "La taille doit être pair & entre 10 et 20";
  } else {
    $role = $_SESSION["role"] ?? "Non défini";
    if ($role === "Joueur 1") {
      $etat["j1"] = $taille;
    } elseif ($role === "Joueur 2") {
      $etat["j2"] = $taille;
    }
    save_state($fichier, $etat);
  }
}

if (is_numeric($etat["j1"]) && is_numeric($etat["j2"]) && $etat["taille_finale"] === null) {

  $tailles = [(int)$etat["j1"], (int)$etat["j2"]];

  $index_choisi = array_rand($tailles);
  $taille_choisi = $tailles[$index_choisi];

  $etat["taille_finale"] = $taille_choisi;
  save_state($fichier, $etat);

  $stmt = $pdo->prepare("INSERT INTO games (board_size, status) VALUES (?, 'placement')");
  $stmt->execute([$taille_choisi]);
  $game_id = $pdo->lastInsertId();
  $_SESSION['game_id'] = $game_id;

  $stmt = $pdo->prepare("INSERT INTO players (session_id, game_id, player_number) VALUES (?, ?, 1)");
  $stmt->execute([$etat["j1_session_id"], $game_id]);
  $p1_id = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO players (session_id, game_id, player_number) VALUES (?, ?, 2)");
  $stmt->execute([$etat["j2_session_id"], $game_id]);
  $p2_id = $pdo->lastInsertId();

  $stmt = $pdo->prepare("UPDATE games SET player1_id = ?, player2_id = ? WHERE id = ?");
  $stmt->execute([$p1_id, $p2_id, $game_id]);


  $current_player_id = ($_SESSION['role'] === 'Joueur 1') ? $p1_id : $p2_id;
  $_SESSION['user_id'] = $current_player_id;

  header("Location: placement.php");
  exit;
}

header('refresh:5');

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Choix taille</title>
</head>

<body>
  <h1>Bonjour <?= $_SESSION["role"] ?></h1>

  <form method="post">
    <label>Proposez la taille de la grille (pair entre 10 et 20) :</label>
    <input type="number" name="taille" min="10" max="20" step="2" required>
    <button type="submit">Proposer</button>
  </form>

  <p>
    Joueur 1 : <?= (is_numeric($etat["j1"]) ? $etat["j1"] : "En attente") ?><br>
    Joueur 2 : <?= (is_numeric($etat["j2"]) ? $etat["j2"] : "En attente") ?>
  </p>
</body>

</html>