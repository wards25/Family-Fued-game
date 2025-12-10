<?php
session_start();
include "dbconnect.php";
// Which player? (default = player 1)
$player = isset($_GET['player']) ? (int)$_GET['player'] : 1;  // Get the player from the URL parameter

// Fetch 5 Fast Money questions and match answers for the selected player
$sql = "
    SELECT 
    fq.id AS qid,
    fq.question_text,
    fa.answer_text AS player_answer,
    COALESCE((
        SELECT points 
        FROM fast_answers 
        WHERE question_id = fq.id 
        AND asnwer = fa.answer_text 
        LIMIT 1
    ), 0) AS points

FROM fast_questions fq
LEFT JOIN fast_player_answers fa
    ON fq.id = fa.question_id
    AND fa.player = $player
ORDER BY fq.id ASC
LIMIT 5;

";

$res = $conn->query($sql);

$output = [];
while ($row = $res->fetch_assoc()) {
    $output[] = [
        "question_id"  => $row["qid"],
        "question"     => $row["question_text"],
        "answer_text"  => $row["player_answer"],
        "points"       => $row["points"]
    ];
}

header("Content-Type: application/json");
echo json_encode($output);
?>
