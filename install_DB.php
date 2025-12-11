<?php

$host = '127.0.0.1';
$dbname = 'bataille_navale';
$user = 'root';
$pass = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("USE $dbname");

    $sql = "
    SET FOREIGN_KEY_CHECKS = 0;

    DROP TABLE IF EXISTS shots;
    DROP TABLE IF EXISTS ships;
    DROP TABLE IF EXISTS players;
    DROP TABLE IF EXISTS games;

    CREATE TABLE games (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        board_size INT(11) NOT NULL,
        player1_id INT(11) DEFAULT NULL,
        player2_id INT(11) DEFAULT NULL,
        current_player INT(11) DEFAULT NULL,
        winner_id INT(11) DEFAULT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'placement',
        j1_hits INT(11) DEFAULT 0,
        j1_misses INT(11) DEFAULT 0,
        j2_hits INT(11) DEFAULT 0,
        j2_misses INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE TABLE players (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        game_id INT(11) NOT NULL,
        player_number INT(11) NOT NULL,
        placement_done TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
    );

    CREATE TABLE ships (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        game_id INT(11) NOT NULL,
        player_id INT(11) NOT NULL,
        type ENUM('carrier','battleship','cruiser','submarine','destroyer') NOT NULL,
        start_x INT(11) NOT NULL,
        start_y INT(11) NOT NULL,
        orientation ENUM('H','V') NOT NULL,
        size INT(11) NOT NULL,
        hits INT(11) DEFAULT 0,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
    );

    CREATE TABLE shots (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        game_id INT(11) NOT NULL,
        player_id INT(11) NOT NULL,
        x INT(11) NOT NULL,
        y INT(11) NOT NULL,
        result ENUM('miss','hit','sunk') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
    );

    SET FOREIGN_KEY_CHECKS = 1;
    ";
    $pdo->exec($sql);

    $messages = [
        "Connexion a la base de donnée OK",
        "La base de donnée nommée : $dbname est prête à être utilisé",
        "Les tables ont été crées avec succès !",
        "Installation terminée !"
    ];
    $success = true;
} catch (PDOException $e) {
    $messages = [
        "Erreur : " . $e->getMessage(),
        "Vérifie que MariaDB est bien lancé !"
    ];
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Base de Données</title>
    <link rel="stylesheet" href="./GUI/CSS/DB.css">
</head>

<body>
    <div class="container">
        <div class="header">⚓ Installation Base de Données</div>

        <ul class="message-list">
            <?php foreach ($messages as $index => $message): ?>
                <li class="message-item <?= $success ? 'success' : 'error' ?>">
                    <span class="icon"><?= $success ? '✔' : '✖' ?></span>
                    <?= $message ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($success): ?>
            <a href='utils/player.php' class='launch-link'>Lancer la partie !</a>
        <?php else: ?>
            <p class='error-message'>Veuillez résoudre les erreurs ci-dessus pour continuer.</p>
        <?php endif; ?>
    </div>
</body>

</html>