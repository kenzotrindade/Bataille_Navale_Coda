<?php
session_start();

$fichier = "../data/config.json";

if (!file_exists($fichier)) {
  file_put_contents($fichier, json_encode([
    "j1" => null,
    "j2" => null,
    "taille_finale" => null
  ]));
}

$etat = json_decode(file_get_contents($fichier), true);

function save_state($file, $data)
{
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

if ($etat["taille_finale"] !== null) {
  header("Location: ../GUI/GUI_matrice.php");
  exit;
}

if ($etat["j1"] === null || $etat["j2"] === null) {
  header("Location: player.php");
  exit;
}

if (!isset($_SESSION["role"])) {
  if ($etat["j1"] === session_id()) {
    $_SESSION["role"] = "Joueur 1";
  } elseif ($etat["j2"] === session_id()) {
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

  $etat["taille_finale"] = $tailles[$index_choisi];

  save_state($fichier, $etat);

  header("Location: ../GUI/GUI_matrice.php");
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