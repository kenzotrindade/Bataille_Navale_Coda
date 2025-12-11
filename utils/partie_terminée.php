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
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #263238;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

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

        button {
            padding: 15px 40px;
            font-size: 1.2em;
            background-color: <?= $couleur ?>;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.2s;
            font-weight: bold;
        }

        button:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
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