<?php
session_start();
require_once 'DB.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['game_id'])) {
    echo json_encode(['success' => false, 'message' => 'Erreur de session : Joueur ou Partie non défini.']);
    exit;
}

$json_content = file_get_contents("php://input");
$data = json_decode($json_content, true);

if (!isset($data['ships']) || !is_array($data['ships'])) {
    echo json_encode(['success' => false, 'message' => 'Aucune donnée de navire reçue.']);
    exit;
}

try {
    $pdo->beginTransaction(); 

    $game_id = $_SESSION['game_id'];
    $player_id = $_SESSION['user_id'];


    $sql = "INSERT INTO ships (game_id, player_id, type, start_x, start_y, orientation, size, hits) 
            VALUES (:game_id, :player_id, :type, :x, :y, :orientation, :size, 0)";
    
    $stmt = $pdo->prepare($sql);


    foreach ($data['ships'] as $ship) {
        $stmt->execute([
            ':game_id'     => $game_id,
            ':player_id'   => $player_id,
            ':type'        => $ship['nom'],
            ':x'           => $ship['x'],
            ':y'           => $ship['y'],
            ':orientation' => $ship['orientation'],
            ':size'        => $ship['taille']
        ]);
    }

    $pdo->commit(); 
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur SQL : ' . $e->getMessage()
    ]);
}
?>