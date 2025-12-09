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
    CASE WHEN fa.answer_text = ffa.asnwer THEN ffa.points ELSE 0 END AS points
FROM fast_questions fq
LEFT JOIN fast_player_answers fa
    ON fq.id = fa.question_id
    AND fa.player = $player
LEFT JOIN fast_answers ffa
    ON fq.id = ffa.question_id
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
