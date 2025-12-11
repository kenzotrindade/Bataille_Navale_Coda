<?php

require "../data/DB.php";

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

function recuperer_id_adversaire($pdo, $game_id, $mon_id)
{

    $sql = "SELECT player1_id, player2_id FROM games WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$game_id]);
    $partie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$partie) {
        die("Erreur : Partie introuvable.");
    }

    if ($mon_id == $partie['player1_id']) {
        return $partie['player2_id'];
    } elseif ($mon_id == $partie['player2_id']) {
        return $partie['player1_id'];
    } else {
        die("Erreur : Vous ne participez pas à cette partie !");
    }
}

function placer($pdo, $grille, $game_id, $player_id)
{

    $bateaux = recup($pdo, $game_id, $player_id);

    foreach ($bateaux as $bateau) {

        $x = $bateau['start_x'];
        $y = $bateau['start_y'];
        $taille = $bateau['size'];
        $direction = $bateau['orientation'];
        $width = $bateau['width'];

        for ($i = 0; $i < $taille; $i++) {
            for ($j = 0; $j < $width; $j++){
                if ($direction == "H") {
                    $grille[$y + $j][$x + $i] = $taille;
                } elseif ($direction == "V") {
                    $grille[$y + $i][$x + $j] = $taille;
                } else {
                    break;
                }
            }
        }
    }
    return $grille;
}

function placer_epave($pdo, $grille, $game_id, $adversaire_id)
{

    $bateaux = recup($pdo, $game_id, $adversaire_id);

    foreach ($bateaux as $bateau) {
        $width = $bateau['width'];

        if ($bateau["hits"] == ($bateau["size"] * $width)) {
            $x = $bateau['start_x'];
            $y = $bateau['start_y'];
            $taille = $bateau['size'];
            $direction = $bateau['orientation'];

            for ($i = 0; $i < $taille; $i++) {
                for ($j = 0; $j < $width; $j++){
                    if ($direction == "H") {
                        $grille[$y + $j][$x + $i] = "EPAVE";
                    } elseif ($direction == "V") {
                        $grille[$y + $i][$x + $j] = "EPAVE";
                    }
                }
            }
        }
    }
    return $grille;
}


function tirer($pdo, $game_id, $player_id, $adversaire_id, $x, $y)
{
    $bateaux = recup($pdo, $game_id, $adversaire_id);

    $resultat_env = "miss";
    $message = "Plouf !";

    $stmt_game = $pdo->prepare("SELECT player1_id FROM games WHERE id = ?");
    $stmt_game->execute([$game_id]);
    $game_data = $stmt_game->fetch(PDO::FETCH_ASSOC);
    $j1_id = $game_data['player1_id'];

    if ($player_id == $j1_id) {
        $colonne_hits = 'j1_hits';
        $colonne_misses = 'j1_misses';
    } else {
        $colonne_hits = 'j2_hits';
        $colonne_misses = 'j2_misses';
    }


    foreach ($bateaux as $bateau) {
        $bateau_x = $bateau['start_x'];
        $bateau_y = $bateau['start_y'];
        $size = $bateau['size'];
        $orientation = $bateau['orientation'];
        $width = $bateau['width'];

        $est_touche = false;

        if ($orientation == "H") {
            if ($y >= $bateau_y && $y < ($bateau_y + $width) && $x >= $bateau_x && $x < ($bateau_x + $size)) {
                $est_touche = true;
            }
        } elseif ($orientation == "V") {
            if ($x >= $bateau_x && $x < ($bateau_x + $width) && $y >= $bateau_y && $y < ($bateau_y + $size)) {
                $est_touche = true;
            }
        }

        if ($est_touche) {
            $resultat_env = "hit";
            $message = "Touché !";

            $nouveaux_hits = $bateau['hits'] + 1;

            $upd = $pdo->prepare("UPDATE ships SET hits = ? WHERE id = ?");
            $upd->execute([$nouveaux_hits, $bateau['id']]);

            if ($nouveaux_hits == ($size * $width)) {
                $resultat_env = "sunk";
                $message = "COULÉ !!!";
            }
            break;
        }
    }

    if ($resultat_env === 'hit' || $resultat_env === 'sunk') {
        $pdo->prepare("UPDATE games SET {$colonne_hits} = {$colonne_hits} + 1 WHERE id = ?")
            ->execute([$game_id]);
    } else {
        $pdo->prepare("UPDATE games SET {$colonne_misses} = {$colonne_misses} + 1 WHERE id = ?")
            ->execute([$game_id]);
    }

    $sql = "INSERT INTO shots (game_id, player_id, x, y, result) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$game_id, $player_id, $x, $y, $resultat_env]);

    $tous_coules = true;
    

    $verif_bateaux = recup($pdo, $game_id, $adversaire_id);
    foreach($verif_bateaux as $verif) {
        if ($verif['hits'] < ($verif['size'] * $verif['width'])) {
            $tous_coules = false;
            break;
        }
    }

    if ($tous_coules) {
        $sql_win = "UPDATE games SET status = 'finished', winner_id = ? WHERE id = ?";
        $stmt_win = $pdo->prepare($sql_win);
        $stmt_win->execute([$player_id, $game_id]);

        $message = "VICTOIRE !!!!!!";
    }

    return $message;
}


function recuperer_historique_tirs($pdo, $game_id, $player_id, $grille)
{

    $sql = "SELECT * FROM shots WHERE game_id = ? AND player_id = ?";
    $query = $pdo->prepare($sql);
    $query->execute([$game_id, $player_id]);
    $shots = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($shots as $shot) {
        $shot_y = $shot["y"];
        $shot_x = $shot["x"];
        $result = $shot["result"];

        if ($result === "hit" || $result === "sunk") {
            $grille[$shot_y][$shot_x] = "X";
        } elseif ($result === "miss") {
            $grille[$shot_y][$shot_x] = "O";
        }
    }
    return $grille;
}
