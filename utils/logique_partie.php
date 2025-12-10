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

        for ($i = 0; $i < $taille; $i++) {
            if ($direction == "H") {
                $grille[$y][$x + $i] = $taille;
            } elseif ($direction == "V") {
                $grille[$y + $i][$x] = $taille;
            } else {
                break;
            }
        }
    }
    return $grille;
}

function placer_epave($pdo, $grille, $game_id, $adversaire_id)
{

    $bateaux = recup($pdo, $game_id, $adversaire_id);

    foreach ($bateaux as $bateau) {
        if ($bateau["hits"] == $bateau["size"]) {
            $x = $bateau['start_x'];
            $y = $bateau['start_y'];
            $taille = $bateau['size'];
            $direction = $bateau['orientation'];

            for ($i = 0; $i < $taille; $i++) {
                if ($direction == "H") {
                    $grille[$y][$x + $i] = "EPAVE"; 
                } elseif ($direction == "V") {
                    $grille[$y + $i][$x] = "EPAVE";
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

    foreach ($bateaux as $bateau) {
        $bateau_x = $bateau['start_x'];
        $bateau_y = $bateau['start_y'];
        $size = $bateau['size'];
        $orientation = $bateau['orientation'];
        
        $est_touche = false;

        if ($orientation == "H") { 
            if ($y == $bateau_y && $x >= $bateau_x && $x < ($bateau_x + $size)) {
                $est_touche = true;
            }
        } elseif ($orientation == "V") { 
            if ($x == $bateau_x && $y >= $bateau_y && $y < ($bateau_y + $size)) {
                $est_touche = true;
            }
        }

        if ($est_touche) {
            $resultat_env = "hit";
            $message = "Touché !";

            $nouveaux_hits = $bateau['hits'] + 1;
            
            $upd = $pdo->prepare("UPDATE ships SET hits = ? WHERE id = ?");
            $upd->execute([$nouveaux_hits, $bateau['id']]);

            if ($nouveaux_hits == $size) {
                $message = "COULÉ !!!";
            }
            
            break; 
        }
    }

    $sql = "INSERT INTO shots (game_id, player_id, x, y, result) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$game_id, $player_id, $x, $y, $resultat_env]);

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

// function obtenir_matrices_combat($pdo, $game_id, $mon_id, $adversaire_id, $tailleMatrice)
// {
//     $grille_defense = creerMatrice($tailleMatrice);
//     $grille_attaque = creerMatrice($tailleMatrice);

//     $grille_defense = placer($pdo, $grille_defense, $game_id, $mon_id);

//     $grille_defense = recuperer_historique_tirs($pdo, $game_id, $adversaire_id, $grille_defense);

//     $grille_attaque = recuperer_historique_tirs($pdo, $game_id, $mon_id, $grille_attaque);

//     $grille_attaque = placer_epave($pdo, $grille_attaque, $game_id, $adversaire_id);


//     return [
//         'defense' => $grille_defense,
//         'attaque' => $grille_attaque
//     ];
// }

// function obtenir_tour_actuel($pdo, $game_id)
// {
//     $sql = "SELECT current_turn FROM games WHERE id = ?";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$game_id]);

//     return $stmt->fetchColumn();
// }

// function changer_tour($pdo, $game_id, $nouveau_joueur_id)
// {
//     $sql = "UPDATE games SET current_turn = ? WHERE id = ?";
//     $stmt = $pdo->prepare($sql);
//     $stmt->execute([$nouveau_joueur_id, $game_id]);
// }
