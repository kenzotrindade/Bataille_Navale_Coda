<?php
// session_start();
// if (!isset($_SESSION['game_id']) || !isset($_SESSION['user_id'])) {
//     header("Location: player.php");
//     exit;
// }

$tailleMatrice = 10; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Placement des navires</title>
    <style>

        body { display: flex; gap: 20px; font-family: sans-serif; }
        
        .dock {
            width: 200px;
            padding: 10px;
            background: #f0f0f0;
            border-right: 2px solid #ccc;
        }

        .ship-model {
            background: #555;
            color: white;
            margin-bottom: 10px;
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px; 
        }


        .w-5 { width: 200px; } 
        .w-4 { width: 160px; }
        .w-3 { width: 120px; }
        .w-2 { width: 80px; }

        .grid-container {
            display: grid;

            grid-template-columns: repeat(<?= $tailleMatrice ?>, 40px);
            grid-template-rows: repeat(<?= $tailleMatrice ?>, 40px);
            gap: 1px;
            background: #000;
        }

        .cell {
            background: white;
            width: 40px;
            height: 40px;
        }


        .occupied { background-color: #777; border: 2px solid #333; }
        .hover-valid { background-color: #a5d6a7; }
        .hover-invalid { background-color: #ef9a9a; }
        
        #actions { margin-top: 20px; }
    </style>
</head>
<body>

    <div class="dock">
        <h3>Votre Flotte</h3>
        <p>Glissez les bateaux sur la grille</p>
        
        <div class="ship-model w-5" draggable="true" data-size="5" data-type="carrier" id="ship-1">Porte-Avions (5)</div>
        <div class="ship-model w-4" draggable="true" data-size="4" data-type="battleship" id="ship-2">Croiseur (4)</div>
        <div class="ship-model w-3" draggable="true" data-size="3" data-type="cruiser" id="ship-3">Contre-Torp (3)</div>
        <div class="ship-model w-3" draggable="true" data-size="3" data-type="submarine" id="ship-4">Sous-Marin (3)</div>
        <div class="ship-model w-2" draggable="true" data-size="2" data-type="destroyer" id="ship-5">Torpilleur (2)</div>
        
        <div id="actions">
            <button id="rotateBtn">Orientation : HORIZONTALE</button>
            <br><br>
            <button id="validateBtn" disabled>VALIDER LE PLACEMENT</button>
        </div>
    </div>

    <div>
        <h3>Placez vos navires</h3>
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

    <script src="JS/drag_drop.js"></script>
</body>
</html>