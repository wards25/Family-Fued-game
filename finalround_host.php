<?php
session_start();
include "dbconnect.php";

$questions = $conn->query("SELECT * FROM fast_questions LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
<title>HOST PANEL - FAST MONEY</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#111;color:white">
<div class="container mt-4">

<h1 style="color:yellow;">FAST MONEY - HOST PANEL</h1>
<p>Only host sees this page. Players should NOT see this screen.</p>

<!-- Player Selection Dropdown -->
<div class="mb-3">
    <label for="playerSelect" class="form-label text-white">Select Player</label>
    <select id="playerSelect" class="form-select">
        <option value="1">Player 1</option>
        <option value="2">Player 2</option>
    </select>
</div>

<?php while($row = $questions->fetch_assoc()): ?>
<div class="card p-3 mt-3">
    <h3><?= $row['question_text'] ?></h3>

    <input type="text" class="form-control answer" 
           data-qid="<?= $row['id'] ?>" placeholder="Player answer...">

    <button class="btn btn-warning mt-2 saveBtn" 
            data-qid="<?= $row['id'] ?>">SAVE</button>
</div>
<?php endwhile; ?>

<script>
document.querySelectorAll(".saveBtn").forEach(btn => {
    btn.onclick = function() {
        let qid = this.dataset.qid;
        let answer = document.querySelector("input[data-qid='"+qid+"']").value;
        let player = document.getElementById("playerSelect").value;

        fetch("fastmoney_save.php", {
            method: "POST",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: "qid=" + qid + "&answer=" + encodeURIComponent(answer) + "&player=" + player
        })
        .then(r => r.text())
        .then(res => {
            console.log("SAVE RESPONSE:", res);

            // Store last response for board (simple global variable on server)
            fetch("fastmoney_last_result.php?msg=" + res);
        });
    }
});

</script>

</div>
</body>
</html>
