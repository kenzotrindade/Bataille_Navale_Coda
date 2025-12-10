<?php
session_start();

$fichier = __DIR__ . "/../data/config.json";
if (!file_exists($fichier)) {
  header("Location: ../utils/player.php");
  exit;
}

$etat = json_decode(file_get_contents($fichier), true);

if (empty($etat['taille_finale']) || $etat['j1'] === null || $etat['j2'] === null) {
  header("Location: ../utils/choix_taille.php");
  exit;
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['game_id'])) {
  header("Location: ../utils/player.php");
  exit;
}

$tailleMatrice = (int) $etat['taille_finale'];

require_once("../data/DB.php");
require_once("../utils/logique_partie.php");

$game_id = $_SESSION['game_id'];
$joueur_actuel_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT player1_id, player2_id FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$game_players = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$game_players) {
  die("Erreur critique: Partie introuvable.");
}

if ($game_players['player1_id'] == $joueur_actuel_id) {
  $joueur_adversaire_id = $game_players['player2_id'];
  $current_player_number = 1;
} else {
  $joueur_adversaire_id = $game_players['player1_id'];
  $current_player_number = 2;
}

if (isset($_GET['x']) && isset($_GET['y'])) {
  $x = (int)$_GET['x'];
  $y = (int)$_GET['y'];

  $tour_actuel_id = obtenir_tour_actuel($pdo, $game_id);
  if ($tour_actuel_id == $joueur_actuel_id) {

    $grille_temp = creerMatrice($tailleMatrice);

    $message_tir = tirer($pdo, $game_id, $joueur_actuel_id, $joueur_adversaire_id, $grille_temp, $x, $y);

    changer_tour($pdo, $game_id, $joueur_adversaire_id);

    $_SESSION['message_tir'] = $message_tir;
    header('Location: GUI_matrice.php');
    exit;
  } else {
    $_SESSION['message_tir'] = "Erreur: Ce n'est pas votre tour !";
    header('Location: GUI_matrice.php');
    exit;
  }
}

$matrices = obtenir_matrices_combat($pdo, $game_id, $joueur_actuel_id, $joueur_adversaire_id, $tailleMatrice);

$matrice_defense = $matrices['defense'];
$matrice_attaque = $matrices['attaque'];

$tour_actuel_id = obtenir_tour_actuel($pdo, $game_id);
$est_mon_tour = ($tour_actuel_id == $joueur_actuel_id);

if (!$est_mon_tour) {
  header('refresh:5');
}

$message_display = '';
if (isset($_SESSION['message_tir'])) {
  $message_display = $_SESSION['message_tir'];
  unset($_SESSION['message_tir']);
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="CSS/style.css">
  <title>Bataille Navale - Combat</title>
</head>

<body>
  <?php if ($message_display) : ?>
    <div class="message-tir">
      <h2><?= htmlspecialchars($message_display) ?></h2>
    </div>
  <?php endif; ?>

  <h1>Phase de Combat - Votre Tour : <?= $est_mon_tour ? 'OUI' : 'NON (Rafraîchissement dans 5s)' ?></h1>

  <div class="grilles-container">

    <div class="grille-defense">
      <h2>Votre Défense</h2>
      <table>
        <?php
        for ($i = 0; $i < $tailleMatrice; $i++) {
          echo "<tr>";
          for ($j = 0; $j < $tailleMatrice; $j++) {
            $valeur = $matrice_defense[$i][$j];

            if ($valeur === "X") {
              $classe = "touché";
            } elseif ($valeur === "O") {
              $classe = "plouf";
            } elseif ($valeur === 0) {
              $classe = "vide";
            } else {
              $classe = "bateau";
            }

            echo "<td class='$classe'></td>";
          }
          echo "</tr>";
        }
        ?>
      </table>
    </div>


    <div class="grille-attaque">
      <h2>Grille d'Attaque (Adversaire)</h2>
      <p><?= $est_mon_tour ? 'Cliquez pour tirer !' : 'En attente du tir adverse...' ?></p>
      <table>
        <?php
        for ($i = 0; $i < $tailleMatrice; $i++) {
          echo "<tr>";
          for ($j = 0; $j < $tailleMatrice; $j++) {
            $valeur = $matrice_attaque[$i][$j];
            $classe = "vide";
            $attribut_js = '';

            if ($valeur === "X" || $valeur === "EPAVE") {
              $classe = "touché";
            } elseif ($valeur === "O") {
              $classe = "plouf";
            }

            if ($valeur === 0 && $est_mon_tour) {
              $classe .= " clickable";
              $attribut_js = "data-x='$j' data-y='$i'"; // Simplifié pour JS basique
            }

            echo "<td class='$classe' $attribut_js></td>";
          }
          echo "</tr>";
        }
        ?>
      </table>
    </div>
  </div>

  <form method="post" id="abandonForm" action="../utils/reset.php">
    <button type="button" onclick="confirmAbandon()">❌ Abandonner la partie</button>
  </form>

  <script>
    function confirmAbandon() {
      if (confirm("Voulez-vous vraiment abandonner la partie ? Cela mettra fin au jeu et réinitialisera tout.")) {
        document.getElementById("abandonForm").submit();
      }
    }
  </script>

</body>

<script src="JS/index.js"></script>

</html>