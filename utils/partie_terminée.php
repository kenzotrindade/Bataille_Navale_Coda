<?php
session_start();
require_once("../data/DB.php");

if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {
    header("Location: player.php");
    exit;
}

$game_id = $_SESSION['game_id'];
$mon_id = $_SESSION['user_id'];

$stmt_game_data = $pdo->prepare("SELECT winner_id, player1_id, j1_hits, j1_misses, j2_hits, j2_misses FROM games WHERE id = ?");
$stmt_game_data->execute([$game_id]);
$game_data = $stmt_game_data->fetch(PDO::FETCH_ASSOC);

$winner_id = $game_data['winner_id'];
$est_vainqueur = ($mon_id == $winner_id);

$is_j1 = ($mon_id == $game_data['player1_id']);
$mes_hits = $is_j1 ? $game_data['j1_hits'] : $game_data['j2_hits'];
$mes_misses = $is_j1 ? $game_data['j1_misses'] : $game_data['j2_misses'];

$total_tirs = $mes_hits + $mes_misses;
$ratio_precision = 0;

if ($total_tirs > 0) {
    $ratio_precision = ($mes_hits / $total_tirs) * 100;
}
$ratio_formatte = number_format($ratio_precision, 1);

if ($est_vainqueur) {
    $titre = "Bravo vous avez gagné !!!";
    $message = "Félicitation moussaillon vous avez envoyer la flotte ennemie par le fond";
    $couleur = "#4CAF50";
    $gif = "https://usagif.com/wp-content/uploads/funny-celebrate-8.gif";
} else {
    $titre = "Bravo ! Vous avez gagnez le droit de recommencez :)";
    $message = "Votre flotte a été anéantie.";
    $couleur = "#F44336";
    $gif = "https://media1.tenor.com/m/wja2I2Ggg9wAAAAC/vegeta-in-the-rain-vegeta.gif";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Fin de partie</title>
    <link rel="stylesheet" href="../GUI/CSS/partie_terminée.css">
    <style>
        h1 {
            color: <?= $couleur ?>;
        }

        button {
            background-color: <?= $couleur ?>;
        }

        .ratio-precision {
            color: <?= $couleur ?>;
        }
    </style>
</head>

<body>

    <h1><?= $titre ?></h1>
    <p><?= $message ?></p>

    <img src="<?= $gif ?>" alt="Resultat">

    <div class="score-board-container">
        <h2>🎯 Votre Performance</h2>
        <p><strong>Tirs réussis (Touches) :</strong> <?= $mes_hits ?></p>
        <p><strong>Tirs ratés (Ploufs) :</strong> <?= $mes_misses ?></p>
        <p><strong>Total des tirs :</strong> <?= $total_tirs ?></p>
        <p><strong class="ratio-precision">Précision :</strong> <?= $ratio_formatte ?> %</p>
    </div>

    <form action="reset_fin_partie.php" method="post">
        <button type="submit">REJOUER UNE PARTIE</button>
    </form>

</body>

</html>