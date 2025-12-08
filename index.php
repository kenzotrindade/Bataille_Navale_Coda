<?php

require "../data/test_db.php";

$matrice = array_fill(0, 10, array_fill(0, 10, 0));

$tailleMatrice = isset($_POST["tailleMatrice"]) ? (int)$_POST["tailleMatrice"] : 10;

function creerMatrice($taille)
{
    return array_fill(0, $taille, array_fill(0, $taille, 0));
}

$matrice = creerMatrice($tailleMatrice);

function recup($pdo, $game_id, $player_id)
{
    $sql = "SELECT * FROM ships WHERE game_id = ? AND player_id = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$game_id, $player_id]);
    $bateaux = $query->fetchAll(PDO::FETCH_ASSOC);

    return $bateaux;
}

function placer($pdo, $grille, $game_id, $player_id)
{

    $bateaux = recup($pdo, $game_id, $player_id);

    foreach ($bateaux as $bateau) {

        $x = $bateau['start_x'];
        $y = $bateau['start_y'];
        $taille = $bateau['size'];
        $direction = $bateau['orientation'];

        for ($i = 0; $i < $taille; $i++) {
            if ($direction == "horizontale") {
                $grille[$y][$x + $i] = $taille;
            } elseif ($direction == "verticale") {
                $grille[$y + $i][$x] = $taille;
            } else {
                echo "Veuillez entrer une direction valide !";
                break;
            }
        }
    }
    return $grille;
}

function tirer($pdo, $game_id, $player_id, $grille, $x, $y)
{

    $bateaux = recup($pdo, $game_id, $player_id);

    $valeur_case = $grille[$y][$x];
    $resultat_env = "";
    $message = "";


    if ($grille[$y][$x] != 0) {
        $grille[$y][$x] = "X";
        $resultat_env = "hit";
        $message = "Touché !";

        foreach ($bateaux as $bateau) {
            $bateau_x = $bateau['start_x'];
            $bateau_y = $bateau['start_y'];
            $size = $bateau['size'];
            $orientation = $bateau['orientation'];
            $touche_trouve = false;


            if ($orientation == "horizontale") {

                if ($y == $bateau_y && $x >= $bateau_x && $x < ($bateau_x + $size)) {
                    $touche_trouve = true;
                }
            } elseif ($orientation == "verticale") {

                if ($x == $bateau_x && $y >= $bateau_y && $y < ($bateau_y + $size)) {
                    $touche_trouve = true;
                }
            }


            if ($touche_trouve) {
                $nouveaux_hits = $bateau['hits'] + 1;


                $upd = $pdo->prepare("UPDATE ships SET hits = ? WHERE id = ?");
                $upd->execute([$nouveaux_hits, $bateau['id']]);


                if ($nouveaux_hits >= $size) {
                    $message = "COULÉ !!!";
                }
                break;
            }
        }
    } else {
        $grille[$y][$x] = "O";
        $resultat_env = "miss";
        $message = "Plouf !";
    }

    $sql = "INSERT INTO shots (game_id, player_id, x, y, result) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$game_id, $player_id, $x, $y, $resultat_env]);

    return $grille;
}
