<?php
session_start();

require_once("../data/DB.php");
$fichier = "../data/config.json";
$current_session_id = session_id();

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

if (isset($_POST["reset_total"])) {

  save_state($fichier, [
    "j1_session_id" => null,
    "j2_session_id" => null,
    "j1" => null,
    "j2" => null,
    "taille_finale" => null
  ]);

  unset($_SESSION['role']);
  unset($_SESSION['game_id']);
  unset($_SESSION['user_id']);
  unset($_SESSION['taille_grille']);

  $_SESSION['notification_abandon'] = "La partie a été abandonnée. Le jeu est réinitialisé.";

  header("Location: player.php");
  exit;
}

if ($etat["j1_session_id"] === $current_session_id) {
  $_SESSION["role"] = "Joueur 1";
} elseif ($etat["j2_session_id"] === $current_session_id) {
  $_SESSION["role"] = "Joueur 2";
} else {
  unset($_SESSION["role"]);
}

if (isset($_POST["joueur1"]) && $etat["j1_session_id"] === null) {
  $etat["j1_session_id"] = $current_session_id;
  $_SESSION["role"] = "Joueur 1";
  save_state($fichier, $etat);
  header("Location: player.php");
  exit;
}

if (isset($_POST["joueur2"]) && $etat["j2_session_id"] === null) {
  $etat["j2_session_id"] = $current_session_id;
  $_SESSION["role"] = "Joueur 2";
  save_state($fichier, $etat);
  header("Location: player.php");
  exit;
}

$is_player = ($etat["j1_session_id"] === $current_session_id || $etat["j2_session_id"] === $current_session_id);

if ($is_player) {
  if ($etat["j1_session_id"] !== null && $etat["j2_session_id"] !== null) {
    header("Location: choix_taille.php");
    exit;
  }
}

$notification = null;
if (isset($_SESSION['notification_abandon'])) {
  $notification = $_SESSION['notification_abandon'];
  unset($_SESSION['notification_abandon']);
}

header("refresh:5");
$role = $_SESSION["role"] ?? "Aucun rôle";
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="../GUI/CSS/style.css">
  <title>Connexion au jeu</title>
</head>

<body class="classic-theme">

  <div class="main-menu-container">
    <h1>BATAILLE NAVALE</h1>

    <?php if ($notification): ?>
      <p class="error-message"><?= htmlspecialchars($notification) ?></p>
    <?php endif; ?>

    <div class="theme-selector">
      <h2>Choisir votre Thème :</h2>
      <select id="theme-select" onchange="appliquerTheme(this.value)">
        <option value="classic">Classique</option>
        <option value="starwars">Star Wars</option>
        <option value="apoc">Post-Apocalyptique</option>
      </select>
    </div>

    <h2>Votre rôle : <strong><?= $role ?></strong></h2>

    <p class="status-message">
      Joueur 1 : <span class="<?= $etat["j1_session_id"] ? "proposed" : "waiting" ?>"><?= $etat["j1_session_id"] ? "🟢 Occupé" : "🔴 Libre" ?></span><br>
      Joueur 2 : <span class="<?= $etat["j2_session_id"] ? "proposed" : "waiting" ?>"><?= $etat["j2_session_id"] ? "🟢 Occupé" : "🔴 Libre" ?></span>
    </p>

    <form method="post">

      <?php
      $disableJ1 = $etat["j1_session_id"] && $etat["j1_session_id"] !== $current_session_id ? "disabled" : "";
      $disableJ2 = $etat["j2_session_id"] && $etat["j2_session_id"] !== $current_session_id ? "disabled" : "";

      if ($is_player && $_SESSION['role'] === 'Joueur 1') {
        $disableJ2 = "disabled";
      } elseif ($is_player && $_SESSION['role'] === 'Joueur 2') {
        $disableJ1 = "disabled";
      } elseif ($is_player) {
        $disableJ1 = "disabled";
        $disableJ2 = "disabled";
      }
      ?>

      <button type="submit" name="joueur1" <?= $disableJ1 ?>>🎮 Devenir Joueur 1</button>
      <button type="submit" name="joueur2" <?= $disableJ2 ?>>🎮 Devenir Joueur 2</button>

    </form>
  </div>

  <a href="reset.php" class="btn-debug-reset">
    ANNULER LA PARTIE (Debug)
  </a>

  <script>
    function appliquerTheme(theme) {
      document.body.className = '';
      document.body.classList.add(theme + '-theme');
      localStorage.setItem('gameTheme', theme);
    }
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      const themeSelect = document.getElementById('theme-select');
      if (themeSelect) {
        themeSelect.value = savedTheme;
        appliquerTheme(savedTheme);
      }
    });
  </script>
</body>

</html>