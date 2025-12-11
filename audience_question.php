<?php
include "dbconnect.php";
// ------------------------------------------
// GET THE LATEST QUESTION
// ------------------------------------------
$sql = "SELECT id, question_text FROM audience_question ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);

$question = "No question found.";
$question_id = 0;

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $question = $row['question_text'];
    $question_id = $row['id'];
}

// ------------------------------------------
// GET ANSWERS FOR THIS QUESTION
// ------------------------------------------
$answers = [];
$ansSql = "SELECT answer, points FROM audience_answers WHERE question_id = $question_id ORDER BY points DESC";
$ansRes = $conn->query($ansSql);

if ($ansRes && $ansRes->num_rows > 0) {
    while ($row = $ansRes->fetch_assoc()) {
        $answers[] = $row;
    }
}

// If less than 8 answers, fill blanks
while (count($answers) < 8) {
    $answers[] = ["answer_text" => "", "points" => ""];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audience Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: url('bg.jpg') center/cover no-repeat;
            font-family: 'Segoe UI', sans-serif;
            color: #ffcc00;
            text-align: center;
            min-height: 100vh;
            padding-top: 40px;
        }

        #question {
            font-size: 3rem;
            color: white;
            opacity: 0;
            transition: 0.6s;
            text-shadow: 0 0 10px #000;
        }

        #timerDisplay {
            font-size: 6rem;
            font-weight: bold;
            color: #ffcc00;
            text-shadow: 0 0 20px #000;
            display: none;
        }

        .answer-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 1100px;
            margin: 30px auto 0 auto;
        }

        .answer-card {
            height: 130px;
            perspective: 1000px;
        }

        .card-inner {
            height: 100%;
            width: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: 0.6s;
        }

        .answer-card.revealed .card-inner {
            transform: rotateY(180deg);
        }

        .card-front, .card-back {
            position: absolute;
            height: 100%;
            width: 100%;
            border-radius: 12px;
            display:flex;
            align-items:center;
            justify-content:center;
            backface-visibility:hidden;
            font-weight: bold;
            font-size: 2.5rem;
        }

        .card-front {
            background:#012060;
            color:white;
            border:4px solid white;
        }

        .card-back {
            background:#4278BE;
            color:white;
            border:4px solid #ffcc00;
            transform: rotateY(180deg);
            font-size: 2rem;
            padding: 10px;
        }

        #wrongX {
            position:fixed;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%) scale(0);
            font-size:14rem;
            color:red;
            text-shadow:0 0 25px red;
            opacity:0;
            transition:0.25s;
            z-index:9999;
        }

        #wrongX.show {
            transform:translate(-50%,-50%) scale(1);
            opacity:1;
        }
    </style>
</head>

<body>

    <h1 style="font-size:4rem; text-shadow:0 0 10px #000;"><b>AUDIENCE ROUND</b></h1>

    <!-- QUESTION (HIDDEN UNTIL ENTER) -->
     <div class="card text-light shadow mb-4" style="background-color: #012060;">
                <div class="card-body">
                    <h2 id="faceoffQuestion" class="fw-bold text-light d-none" style="font-size:45px;">
                        <h2 id="question"><?= htmlspecialchars($question) ?></h2>
                    </h2>
                    <h2 id="pressEnterHint" class="fw-bold text-light" style="font-size:35px; color:#ffcc00;">
                    </h2>
                </div>
            </div>

    <div id="timerDisplay">5</div>

    <!-- ANSWERS -->
    <div class="answer-grid">
        <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="answer-card">
                <div class="card-inner">
                    <div class="card-front text-warning"><?= $i + 1 ?></div>
                    <div class="card-back">
                        <?= strtoupper($answers[$i]['answer']) ?>
                        <?php if ($answers[$i]['points'] !== ""): ?>
                            <span class="points text-white px-2 py-1 rounded ms-2"
                                        style="font-size:45px; background-color: #dc3545;"><?= $answers[$i]['points'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Sounds -->
    <audio id="ding" src="ding.mp3" preload="auto"></audio>
    <audio id="buzzer" src="buzzer.mp3" preload="auto"></audio>
    <audio id="tick" src="tick.mp3" preload="auto"></audio>

    <div id="wrongX">❌</div>

<script>
const cards = document.querySelectorAll(".answer-card");
const ding = document.getElementById("ding");
const buzzer = document.getElementById("buzzer");
const tick = document.getElementById("tick");
const wrongX = document.getElementById("wrongX");
const timerDisplay = document.getElementById("timerDisplay");
const question = document.getElementById("question");

let timer = null;
let timeLeft = 5;
let timerRunning = false;
let questionRevealed = false;

/* ENTER → Reveal question */
function revealQuestion() {
    question.style.opacity = 1;
    questionRevealed = true;
}

/* TIMER FUNCTIONS */
function startTimer() {
    if (timerRunning) return;

    timeLeft = 5;
    timerDisplay.textContent = timeLeft;
    timerDisplay.style.display = "block";
    timerRunning = true;
    tick.currentTime = 0;
    tick.play();

    timer = setInterval(() => {
        timeLeft--;
        timerDisplay.textContent = timeLeft;

        if (timeLeft <= 0) {
            stopTimer();
            showWrong();
        }
    }, 1000);
}

function stopTimer() {
    clearInterval(timer);
    timerRunning = false;
    timerDisplay.style.display = "none";
}

/* REVEAL CARD */
function revealCard(card) {
    if (!card.classList.contains("revealed")) {
        card.classList.add("revealed");
        ding.currentTime = 0;
        ding.play();
        stopTimer();
    }
}

/* BIG WRONG X */
function showWrong() {
    wrongX.classList.add("show");
    buzzer.currentTime = 0;
    buzzer.play();

    setTimeout(() => {
        wrongX.classList.remove("show");
    }, 700);
}

/* CLICK REVEALS */
cards.forEach(card => {
    card.addEventListener("click", () => revealCard(card));
});

/* KEYBOARD CONTROLS */
document.addEventListener("keydown", e => {

    // ENTER = Reveal Question
    if (e.key === "Enter") {
        revealQuestion();
        return;
    }

    // SPACE = Start / Stop Timer
    if (e.code === "Space") {
        e.preventDefault();
        tick.pause();
        if (timerRunning) stopTimer();
        else startTimer();
        return;
    }

    if (e.key.toLowerCase() === "x" || e.key === "0") {
        stopTimer();
        tick.pause();
        showWrong();
        return;
    }


    // Number keys reveal answers (1–8)
    const num = parseInt(e.key);
    if (num >= 1 && num <= cards.length) {
        revealCard(cards[num - 1]);
    }
});
</script>

</body>
</html>
