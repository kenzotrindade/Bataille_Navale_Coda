<?php
session_start();
require_once("../data/DB.php");

if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {
    header("Location: player.php");
    exit;
}

$game_id = $_SESSION['game_id'];
$mon_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT winner_id FROM games WHERE id = ?");
$stmt->execute([$game_id]);
$winner_id = $stmt->fetchColumn();

$est_vainqueur = false;

if ($mon_id == $winner_id) {
    $est_vainqueur = true;
}

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
    <link rel="stylesheet" href="../GUI/CSS/partie_terminée.css" >
    <style>
        h1 {
            color: <?= $couleur ?>;
        }

<<<<<<< HEAD
        h1 {
            font-size: 4em;
            margin-bottom: 10px;
            color: <?= $couleur ?>;
        }

        p {
            font-size: 1.5em;
            margin-bottom: 30px;
        }

        img {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
            max-width: 400px;
        }

=======
>>>>>>> c23613d8c9d3ad690059bb56edfbdee96d9953b7
        button {
            background-color: <?= $couleur ?>;
        }
<<<<<<< HEAD

        button:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
=======
>>>>>>> c23613d8c9d3ad690059bb56edfbdee96d9953b7
    </style>
</head>

<body>

    <h1><?= $titre ?></h1>
    <p><?= $message ?></p>

    <img src="<?= $gif ?>" alt="Resultat">

    <form action="reset_fin_partie.php" method="post">
        <button type="submit">REJOUER UNE PARTIE</button>
    </form>

</body>

</html>