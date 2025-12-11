<?php
session_start();
$tailleMatrice = $_SESSION['taille_grille'];

$largeurCarrier = ($tailleMatrice == 20) ? 2 : 1;
$totalBateaux = 5;

if ($tailleMatrice >= 12){
    $totalBateaux++;
}
if ($tailleMatrice >= 14){
    $totalBateaux++;
}
if ($tailleMatrice >= 16){
    $totalBateaux++;
}
if ($tailleMatrice >= 18){
    $totalBateaux++;
}
?>

<!DOCTYPE html>
<html lang="fr">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement des navires</title>
    <link rel="stylesheet" href="../GUI/CSS/placement.css">
</head>

<body>
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(<?= $tailleMatrice ?>, 40px);
            grid-template-rows: repeat(<?= $tailleMatrice ?>, 40px);
            gap: 1px;
            background-color: #333;
            border: 5px solid #333;
        }
    </style>

    <div class="dock-container" data-ships="<?= $totalBateaux ?>">
        <h3>Votre Flotte</h3>
        <p><small>Glissez les navires sur la grille</small></p>

        <div class="ship-container">
            <div>Porte-Avions (5)</div>
            <div class="ship size-5" draggable="true" data-width="<?= $largeurCarrier ?> data-size="5" data-type="carrier" id="ship-carrier"
            style="<?= ($largeurCarrier == 2) ? 'height: 80px; line-height: 80px;' : '' ?>"> ⚓ </div>
        </div>

        <?php 
            if ($tailleMatrice >= 18): ?>
                <div class="ship-container">
                    <div>Croiser (4)</div>
                    <div class="ship size-4" draggable="true" data-width="1" data-size="4" data-type="battleship" id="ship-cruiser_2">⚓</div>
                </div>
        <?php endif; ?>

        <div class="ship-container">
            <div>Croiser (4)</div>
            <div class="ship size-4" draggable="true" data-width="1" data-size="4" data-type="battleship" id="ship-cruiser_1">⚓</div>
        </div>

        <?php 
            if ($tailleMatrice >= 16): ?>
            <div class="ship-container">
                <div>Sous-Marin (3)</div>
                <div class="ship size-3" draggable="true" data-width="1" data-size="3" data-type="submarine" id="ship-submarine_3">⚓</div>
            </div>
        <?php endif; ?>

        <div class="ship-container">
            <div>Sous-Marin (3)</div>
            <div class="ship size-3" draggable="true" data-width="1" data-size="3" data-type="submarine" id="ship-submarine_2">⚓</div>
        </div>

        <div class="ship-container">
            <div>Sous-Marin (3)</div>
            <div class="ship size-3" draggable="true" data-width="1" data-size="3" data-type="submarine" id="ship-submarine_1">⚓</div>
        </div>

        <?php
            if ($tailleMatrice >= 14): ?> 
                <div class="ship-container">
                    <div>Torpilleur (2)</div>
                    <div class="ship size-2" draggable="true" data-width="1" data-size="2" data-type="destroyer" id="ship-destroyer_3">⚓</div>
                </div>
        <?php endif; ?>


        <?php
            if ($tailleMatrice >= 12): ?> 
                <div class="ship-container">
                    <div>Torpilleur (2)</div>
                    <div class="ship size-2" draggable="true" data-width="1" data-size="2" data-type="destroyer" id="ship-destroyer_2">⚓</div>
                </div>
        <?php endif; ?>


        <div class="ship-container">
            <div>Torpilleur (2)</div>
            <div class="ship size-2" draggable="true" data-width="1" data-size="2" data-type="destroyer" id="ship-destroyer_1">⚓</div>
        </div>

        <div class="controls">
            <button id="rotateBtn">Orientation : HORIZONTALE</button>
            <button id="validateBtn" disabled>VALIDER LA FLOTTE</button>
        </div>
    </div>

    <div>
        <h3>Zone de déploiement</h3>
        <div class="grid-container" id="grid">
            <?php
            for ($y = 0; $y < $tailleMatrice; $y++) {
                for ($x = 0; $x < $tailleMatrice; $x++) {
                    echo "<div class='cell' data-x='$x' data-y='$y'></div>";
                }
            }
            ?>
        </div>
    </div>

    <div>
        <a href="reset.php">
            <button>
                ANNULER LA PARTIE (Debug)
            </button>
        </a>
    </div>

    <script src="../GUI/JS/drag_drop.js"></script>

</body>

</html>