<?php
session_start();

$fichier = __DIR__ . "/../data/config.json";
if (!file_exists($fichier)) {
  header("Location: ../utils/player.php");
  exit;
}

$etat = json_decode(file_get_contents($fichier), true);

if (empty($etat['taille_finale']) || $etat['j1'] === null || $etat['j2'] === null) {
  header("Location: ../utils/player.php");
  exit;
}

if (empty($etat['taille_finale'])) {
  header("Location: ../utils/choix_taille.php");
  exit;
}

$tailleMatrice = (int)$etat['taille_finale'];

require_once(__DIR__ . "/../index.php");
header('refresh:5');
?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="CSS/style.css">
  <title>Bataille Navale</title>
</head>

<body>
  <table>
    <?php
    require_once("../index.php");
    for ($i = 0; $i < $tailleMatrice; $i++) {
      echo "<tr>";
      for ($j = 0; $j < $tailleMatrice; $j++) {
        $valeur = $matrice[$i][$j];
        if ($valeur === "X") {
          $classe = "touché";
        } elseif ($valeur === "O") {
          $classe = "plouf";
        } elseif ($valeur === 0) {
          $classe = "vide";
        } else {
          $classe = "bateau";
        }

        $attribut_js = '';
        if ($valeur != "X" && $valeur != "O") {
          $classe .= " clickable";
          $attribut_js = "data-x='$j' data-y='$i' ";
        }

        echo "<td class='$classe' $attribut_js></td>";
      }
      echo "</tr>";
    }
    ?>
  </table>

  <form method="post" id="abandonForm" action="../utils/player.php">
    <button type="button" onclick="confirmAbandon()">❌ Abandonner la partie</button>
  </form>

  <script>
    function confirmAbandon() {
      if (confirm("Voulez-vous vraiment abandonner la partie ? Cela mettra fin au jeu pour les deux joueurs.")) {
        let form = document.getElementById("abandonForm");
        let input = document.createElement("input");
        input.type = "hidden";
        input.name = "reset_total";
        input.value = "1";
        form.appendChild(input);
        form.submit();
      }
    }
  </script>

</body>

<script src="JS/index.js"></script>

</html>