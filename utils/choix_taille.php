<?php
session_start();

require_once("../data/DB.php");
$fichier = "../data/config.json";

// ... Code PHP (Logique de jeu) inchangé ...

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
  if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {

    unset($_SESSION['game_id']);
    unset($_SESSION['user_id']);
    unset($_SESSION['taille_grille']);

    $current_session_id = session_id();

    $stmt_player = $pdo->prepare("SELECT id, game_id FROM players WHERE session_id = ? ORDER BY id DESC LIMIT 1");
    $stmt_player->execute([$current_session_id]);
    $player_data = $stmt_player->fetch(PDO::FETCH_ASSOC);

    if ($player_data) {
      $game_id = $player_data['game_id'];

      $stmt_game = $pdo->prepare("SELECT id, board_size FROM games WHERE id = ?");
      $stmt_game->execute([$game_id]);
      $game_data = $stmt_game->fetch(PDO::FETCH_ASSOC);

      if ($game_data) {
        $_SESSION['game_id'] = $game_id;
        $_SESSION['user_id'] = $player_data['id'];
        $_SESSION['taille_grille'] = $game_data['board_size'];

        $role = ($current_session_id === $etat["j1_session_id"]) ? 'Joueur 1' : 'Joueur 2';
        $_SESSION['role'] = $role;

        header("Location: placement.php");
        exit;
      }
    }
  }

  if (isset($_SESSION['game_id']) && isset($_SESSION['user_id'])) {
    $stmt_status = $pdo->prepare("SELECT status FROM games WHERE id = ?");
    $stmt_status->execute([$_SESSION['game_id']]);
    $game_status = $stmt_status->fetchColumn();

    if ($game_status === 'placement') {
      header("Location: placement.php");
    } elseif ($game_status === 'in_progress' || $game_status === 'finished') {
      header("Location: ../GUI/GUI_matrice.php");
    } else {
      header("Location: player.php");
    }
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

  $stmt = $pdo->prepare("INSERT INTO players (session_id, game_id, player_number) VALUES (?, ?, 1)");
  $stmt->execute([$etat["j1_session_id"], $game_id]);
  $p1_id = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO players (session_id, game_id, player_number) VALUES (?, ?, 2)");
  $stmt->execute([$etat["j2_session_id"], $game_id]);
  $p2_id = $pdo->lastInsertId();

  $stmt = $pdo->prepare("UPDATE games SET player1_id = ?, player2_id = ?, current_player = ? WHERE id = ?");
  $stmt->execute([$p1_id, $p2_id, $p1_id, $game_id]);

  $current_session_id = session_id();

  if ($current_session_id === $etat["j1_session_id"]) {
    $_SESSION['user_id'] = $p1_id;
    $_SESSION['role'] = 'Joueur 1';
  } elseif ($current_session_id === $etat["j2_session_id"]) {
    $_SESSION['user_id'] = $p2_id;
    $_SESSION['role'] = 'Joueur 2';
  }

  $_SESSION['game_id'] = $game_id;
  $_SESSION['taille_grille'] = $taille_choisi;

  header("Location: placement.php");
  exit;
}

header('refresh:5');

$role = $_SESSION["role"] ?? "Non défini";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../GUI/CSS/style.css">
  <title>Choix taille</title>
</head>

<body>

  <div class="game-lobby">
    <div class="player-info">
      <h1>Bonjour <?= $role ?></h1>
      <?php if (!is_numeric($etat["j1"]) || !is_numeric($etat["j2"])): ?>
        <p class="waiting-indicator">En attente de l'autre joueur...</p>
      <?php endif; ?>
    </div>

    <form method="post" class="size-form">
      <label for="taille">Proposez la taille de la grille (pair entre 10 et 20) :</label>
      <input type="number" name="taille" id="taille" min="10" max="20" step="2" required value="<?= $etat[$_SESSION["role"] === 'Joueur 1' ? 'j1' : 'j2'] ?>">
      <button type="submit" class="btn-proposer" <?= is_numeric($etat[$_SESSION["role"] === 'Joueur 1' ? 'j1' : 'j2']) ? 'disabled' : '' ?>>Proposer</button>
      <?php if (isset($error)): ?>
        <p class="error-message"><?= $error ?></p>
      <?php endif; ?>
    </form>

    <div class="status-propositions">
      <h2>Propositions:</h2>
      <p>
        Joueur 1 :
        <span class="<?= (is_numeric($etat["j1"]) ? 'proposed' : 'waiting') ?>">
          <?= (is_numeric($etat["j1"]) ? $etat["j1"] : "En attente") ?>
        </span>
      </p>
      <p>
        Joueur 2 :
        <span class="<?= (is_numeric($etat["j2"]) ? 'proposed' : 'waiting') ?>">
          <?= (is_numeric($etat["j2"]) ? $etat["j2"] : "En attente") ?>
        </span>
      </p>
    </div>

  </div>

  <a href="reset.php" class="btn-debug-reset">
    ANNULER LA PARTIE (Debug)
  </a>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      document.body.className = savedTheme + '-theme';
    });
  </script>
</body>

</html>