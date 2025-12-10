<?php
session_start();
require_once("../data/DB.php");
require_once("../utils/logique_partie.php");


if (!isset($_SESSION['user_id']) || !isset($_SESSION['game_id'])) {
  header("Location: ../utils/player.php");
  exit;
}

$game_id = $_SESSION['game_id'];
$mon_id = $_SESSION['user_id'];
$adversaire_id = recuperer_id_adversaire($pdo, $game_id, $mon_id);
$tailleMatrice = $_SESSION['taille_grille'];

$stmt = $pdo->prepare("SELECT current_player FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$id_joueur_actif = $stmt->fetchColumn();

if (isset($_GET['x']) && isset($_GET['y'])) {

  if ($mon_id != $id_joueur_actif) {
    header("Location: GUI_matrice.php");
    exit;
  }

  $tir_x = (int)$_GET['x'];
  $tir_y = (int)$_GET['y'];
  tirer($pdo, $game_id, $mon_id, $adversaire_id, $tir_x, $tir_y);

  $stmt = $pdo->prepare("UPDATE games SET current_player = ? WHERE id = ?");
  $stmt->execute([$adversaire_id, $game_id]);
  header("Location: GUI_matrice.php");
  exit;
}

$mon_tour = false;
if ($mon_id == $id_joueur_actif) {
  $mon_tour = true;
} else {
  $mon_tour = false;
}

$classe = $mon_tour ? "" : "disabled";
$message = $mon_tour ? "A vous de jouez, visez bien !" : "Tour de l'adversaire...";

$grille_defense = creerMatrice($tailleMatrice);
$grille_defense = placer($pdo, $grille_defense, $game_id, $mon_id);
$grille_defense = recuperer_historique_tirs($pdo, $game_id, $adversaire_id, $grille_defense);

$grille_attaque = creerMatrice($tailleMatrice);
$grille_attaque = recuperer_historique_tirs($pdo, $game_id, $mon_id, $grille_attaque);
$grille_attaque = placer_epave($pdo, $grille_attaque, $game_id, $adversaire_id);

// Récupérer le thème
$theme = isset($_COOKIE['gameTheme']) ? $_COOKIE['gameTheme'] : 'classic';

header('refresh:5');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      document.documentElement.className = savedTheme + '-theme';
    })();
  </script>
  <link rel="stylesheet" href="CSS/style.css">
  <link rel="stylesheet" href="./CSS/GUI.css">
  <title>Bataille Navale - Combat</title>
</head>

<body class="<?= htmlspecialchars($theme) ?>-theme">

  <div class="game-container">
    <h1><?= $message ?></h1>

    <div class="board-defense">
      <h2 class="board-title">Ma Flotte</h2>
      <table>
        <?php
        for ($i = 0; $i < $tailleMatrice; $i++) {
          echo "<tr>";
          for ($j = 0; $j < $tailleMatrice; $j++) {
            $valeur = $grille_defense[$i][$j];

            if ($valeur === "X") $c = "touche";
            elseif ($valeur === "O") $c = "plouf";
            elseif ($valeur != 0 && $valeur != "EPAVE") $c = "bateau";
            else $c = "vide";

            echo "<td class='$c'></td>";
          }
          echo "</tr>";
        }
        ?>
      </table>
    </div>

    <div class="board-attack <?= $classe ?>">
      <h2 class="board-title">Radar de Tir</h2>
      <table>
        <?php
        for ($i = 0; $i < $tailleMatrice; $i++) {
          echo "<tr>";
          for ($j = 0; $j < $tailleMatrice; $j++) {
            $valeur = $grille_attaque[$i][$j];


            if ($valeur === "X") $c = "touche";
            elseif ($valeur === "O") $c = "plouf";
            elseif ($valeur === "EPAVE") $c = "epave";
            else $c = "vide";

            $js = "";
            if ($valeur !== "X" && $valeur !== "O") {
              $c .= " clickable";
              $js = "data-x='$j' data-y='$i'";
            }

            echo "<td class='$c' $js></td>";
          }
          echo "</tr>";
        }
        ?>
      </table>
    </div>

  </div>

  <form method="post" id="abandonForm" action="../utils/player.php" style="text-align:center; margin-top:20px;">
    <button type="button" onclick="confirmAbandon()">❌ Abandonner la partie</button>
  </form>

  <script>
    function confirmAbandon() {
      if (confirm("Voulez-vous vraiment abandonner ?")) {
        let form = document.getElementById("abandonForm");
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "reset_total";
        input.value = "1";
        form.appendChild(input);
        form.submit();
      }
    }

    // Maintenir le thème
    document.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('gameTheme') || 'classic';
      document.body.className = savedTheme + '-theme';
    });
  </script>

  <script src="JS/index.js"></script>

</body>

</html>