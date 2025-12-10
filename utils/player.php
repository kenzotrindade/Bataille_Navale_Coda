<?php
session_start();

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
  <title>Connexion au jeu</title>
</head>

<body>

  <h1>Connexion</h1>
  <h2>Votre rôle : <strong><?= $role ?></strong></h2>

  <p>
    Joueur 1 : <?= $etat["j1_session_id"] ? "🟢 Occupé" : "🔴 Libre" ?><br>
    Joueur 2 : <?= $etat["j2_session_id"] ? "🟢 Occupé" : "🔴 Libre" ?>
  </p>

  <form method="post">

    <?php
    $disableJ1 = $etat["j1_session_id"] ? "disabled" : "";
    $disableJ2 = $etat["j2_session_id"] ? "disabled" : "";

    if ($is_player) {
      $disableJ1 = "disabled";
      $disableJ2 = "disabled";
    }
    ?>

    <button type="submit" name="joueur1" <?= $disableJ1 ?>>🎮 Devenir Joueur 1</button>
    <button type="submit" name="joueur2" <?= $disableJ2 ?>>🎮 Devenir Joueur 2</button>

    <br><br>

    <div>
      <a href="reset.php">
        <button>
          ANNULER LA PARTIE (Debug)
        </button>
      </a>
    </div>

  </form>

</body>

</html>