<?php
session_start();
include "dbconnect.php";

if(!isset($_GET['player'])){
    die("Invalid access.");
}

$player = (int)$_GET['player'];
if($player < 1 || $player > 2) die("Invalid player.");


// ---------------------------------------------------------
// LOAD PLAYER ANSWERS
// ---------------------------------------------------------
$answers = [];

$q = $conn->query("
    SELECT a.question_id, a.answer_text, b.points
    FROM fast_player_answers a
    LEFT JOIN fast_answers b
        ON b.question_id = a.question_id
        AND LOWER(b.answer_text)=LOWER(a.answer_text)
    WHERE a.player = $player
    ORDER BY a.id ASC
");

while($r=$q->fetch_assoc()){
    $answers[] = [
        "text"   => $r['answer_text'],
        "points" => $r['points'] ?? 0
    ];
}

$total = 0;
foreach($answers as $a){
    if($a['text'] != "X") $total += $a['points'];
}

// SAVE SCORE
$_SESSION['fast_scores'][$player] = $total;

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<title>FINAL ROUND SCORE – PLAYER <?= $player ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:white;
    text-align:center;
    padding-top:40px;
}

.table-box{
    width:500px;
    margin:0 auto;
}

table{
    border:3px solid #ffcc00 !important;
    background:transparent;
}

th,td{
    border:2px solid #ffcc00 !important;
}

.hidden{display:none;}
</style>

</head>
<body>

<h2>PLAYER <?= $player ?> SCORE</h2>

<div class="table-box">

<table class="table">

<thead>
<tr>
    <th>ANSWER</th>
    <th>POINTS</th>
</tr>
</thead>

<tbody id="rows">

<?php foreach($answers as $a): ?>

<tr class="hidden">
    <td><?= htmlspecialchars($a['text']) ?></td>
    <td>
        <?php
        if($a['text']=="X") echo "❌";
        else echo $a['points'];
        ?>
    </td>
</tr>

<?php endforeach; ?>

<tr class="hidden" id="totalRow">
    <td><b>TOTAL</b></td>
    <td><b><?= $total ?></b></td>
</tr>

</tbody>
</table>

</div>


<button class="btn btn-warning btn-lg" onclick="reveal()">VIEW NEXT</button>


<br><br>

<?php if($player == 1): ?>

<a href="finalround.php?player=2" class="btn btn-success btn-lg">CONTINUE → PLAYER 2</a>

<?php else: ?>

<a href="finalround_result.php" class="btn btn-success btn-lg">VIEW FINAL RESULT</a>

<?php endif; ?>
 

<script>
let index = 0;
const rows = document.querySelectorAll("#rows tr");

function reveal(){
    if(index < rows.length){
        rows[index].classList.remove("hidden");
        index++;
    }
}
</script>

</body>
</html>
