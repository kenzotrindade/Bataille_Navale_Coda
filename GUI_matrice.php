<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="style.css">
  <title>Bataille Navale</title>
</head>

<body>
  <form method="post">
    <label>Taille : </label>
    <input type="number" name="tailleMatrice" placeholder="10" min="10" max="20">
    <button type="submit">Créer !</button>
  </form>

  <table>
    <?php
    require_once("index.php");
    for ($i = 0; $i < $tailleMatrice; $i++) {
      echo "<tr>";
      for ($j = 0; $j < $tailleMatrice; $j++) {
        $valeur = $matrice[$i][$j];
        $classe = $valeur == 0 ? "vide" : "bateau";
        echo "<td class='$classe'></td>";
      }
      echo "</tr>";
    }
    ?>
  </table>
</body>

</html>