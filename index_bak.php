<?php
session_start();
include('dbconnect.php');

// Initialize round, totals, and players if not set
if (!isset($_SESSION['round'])) {
    $_SESSION['round'] = 1;
}
if (!isset($_SESSION['totals'])) {
    $_SESSION['totals'] = ['1' => 0, '2' => 0];
}
if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = ['1' => 'Player 1', '2' => 'Player 2'];
}

// Handle name submission
if (isset($_POST['set_names'])) {
    $_SESSION['players']['1'] = !empty($_POST['player1']) ? htmlspecialchars($_POST['player1']) : 'Player 1';
    $_SESSION['players']['2'] = !empty($_POST['player2']) ? htmlspecialchars($_POST['player2']) : 'Player 2';
    header("Location: start.php");
    exit();
}

// Next round
if (isset($_GET['next_round'])) {
    $_SESSION['round']++;
}

// Reset game
if (isset($_GET['reset_game'])) {
    $_SESSION['round'] = 1;
    $_SESSION['totals'] = ['1' => 0, '2' => 0];
    $_SESSION['players'] = ['1' => 'Player 1', '2' => 'Player 2'];
    header("Location: index.php");
    exit();
}

// Current data
$round = $_SESSION['round'];
$totals = $_SESSION['totals'];
$players = $_SESSION['players'];

// Game over detection
$maxRounds = 4;
$gameOver = ($round > $maxRounds);

// Fetch question only if game not over
if (!$gameOver) {
    $sql = "SELECT * FROM questions WHERE round = $round ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $random_question = $result->fetch_assoc();
        $question_id = $random_question['id'];

        $answers_sql = "SELECT * FROM answers WHERE question_id = $question_id ORDER BY points DESC";
        $answers_result = $conn->query($answers_sql);

        $answers = [];
        while ($answer = $answers_result->fetch_assoc()) {
            $answers[] = $answer;
        }
    } else {
        $random_question = ['question_text' => '⚠️ No question found for this round.'];
        $answers = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ramosco Family Feud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
    background-image: url('bg.jpg'); /* Replace with your image path */
    background-size: cover;      /* Cover entire page */
    background-repeat: no-repeat; /* Don't tile the image */
    background-position: center;  /* Center the image */
    height: 100vh;               /* Full viewport height */
}
h1, h4 {
    color: #ffcc00;
    text-shadow: 0 0 10px #000;
}
.answer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 15px;
    justify-items: center;
    margin-bottom: 2rem;
}
.answer-card {
    width: 100%;
    max-width: 320px;
    height: 90px;
    perspective: 1000px;
}
.card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.6s;
    transform-style: preserve-3d;
    cursor: pointer;
}
.answer-card.revealed .card-inner {
    transform: rotateY(180deg);
}
.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 12px;
    backface-visibility: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    font-weight: bold;
}
.card-front {
    background: #1a1a1a;
    border: 3px solid #ffcc00;
    color: #ffcc00;
}
.card-back {
    background: #007bff;
    color: #fff;
    transform: rotateY(180deg);
    border: 3px solid #ffcc00;
}
.card-back small {
    display: block;
    margin-top: 5px;
}
.scoreboard {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 1rem;
}
.score-box {
    background: #000;
    color: #ffcc00;
    border: 3px solid #ffcc00;
    border-radius: 10px;
    padding: 15px 30px;
    text-align: center;
    box-shadow: 0 0 15px #000;
}
#wrongX {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    font-size: 15rem;
    color: red;
    opacity: 0;
    text-shadow: 0 0 30px rgba(255,0,0,0.9);
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    z-index: 9999;
}
#wrongX.show {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}
.errors {
    color: red;
    font-size: 1.6em;
}
.strike {
    margin-right: 5px;
}
</style>
</head>
<body>

<div class="container py-4 text-center">
    <?php if ($round === 1 && $totals['1'] == 0 && $totals['2'] == 0 && !$gameOver): ?>
        <div class="card bg-dark text-warning shadow p-4 mb-4">
            <h4 class="mb-3">Enter Team Names</h4>
            <form method="POST">
                <div class="row justify-content-center">
                    <div class="col-md-4 mb-3">
                        <input type="text" name="player1" class="form-control form-control-lg text-center" placeholder="Player 1" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="player2" class="form-control form-control-lg text-center" placeholder="Player 2" required>
                    </div>
                </div>
                <button type="submit" name="set_names" value="1" class="btn btn-lg btn-success mt-2">Start Game</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!$gameOver): ?>
        <h4>Round <?php echo $round; ?></h4>

        <!-- Question -->
        <div class="card bg-dark text-warning shadow mb-4">
            <div class="card-body">
                <h2 class="fw-bold"><?php echo htmlspecialchars($random_question['question_text']); ?></h2>
            </div>
        </div>

        <!-- Answer Grid -->
        <div class="answer-grid">
            <?php foreach ($answers as $index => $answer): ?>
                <div class="answer-card" 
                    data-answer="<?php echo htmlspecialchars($answer['answer_text']); ?>" 
                    data-points="<?php echo (int)$answer['points']; ?>">
                    <div class="card-inner">
                        <div class="card-front"><?php echo $index + 1; ?></div>
                        <div class="card-back">
                            <div>
                                <span><?php echo strtoupper(htmlspecialchars($answer['answer_text'])); ?></span>
                                <small><?php echo $answer['points']; ?> pts</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Player Controls -->
        <div class="mb-4">
            <button class="btn btn-lg btn-outline-warning player-btn active" data-player="1">
                <?php echo strtoupper($players['1']); ?>
            </button>
            <button class="btn btn-lg btn-outline-warning player-btn" data-player="2">
                <?php echo strtoupper($players['2']); ?>
            </button>
            <form method="GET" class="d-inline">
                <button type="submit" name="reset_game" value="1" class="btn btn-lg btn-danger ms-2 btn-sm"><i class="fas fas-refresh fas-sm"></i></button>
            </form>
        </div>

        <!-- Scoreboard -->
        <div class="scoreboard">
            <div class="score-box">
                <h5><?php echo strtoupper($players['1']); ?></h5>
                <h3 id="score1">0</h3>
                <div id="errors1" class="errors"></div>
            </div>
            <div class="score-box">
                <h5><?php echo strtoupper($players['2']); ?></h5>
                <h3 id="score2">0</h3>
                <div id="errors2" class="errors"></div>
            </div>
        </div>

        <!-- Totals -->
        <div class="mt-4">
            <h5>
                <?php echo $players['1']; ?> Total: <span id="total1"><?php echo $totals['1']; ?></span> |
                <?php echo $players['2']; ?> Total: <span id="total2"><?php echo $totals['2']; ?></span>
            </h5>
            <form method="GET" class="mt-3">
                <button type="submit" name="next_round" value="1" class="btn btn-lg btn-success">Next Round</button>
            </form>
        </div>

    <?php else: ?>
        <!-- GAME OVER SCREEN -->
        <div class="card bg-dark text-warning shadow p-5 mt-5">
            <h2 class="mb-4">🏁 GAME OVER 🏁</h2>
            <h4>Final Scores</h4>
            <p class="mt-3 mb-0"><?php echo $players['1']; ?>: <strong><?php echo $totals['1']; ?></strong></p>
            <p><?php echo $players['2']; ?>: <strong><?php echo $totals['2']; ?></strong></p>

            <?php if ($totals['1'] > $totals['2']): ?>
                <h3 class="text-success mt-4">🏆 <?php echo strtoupper($players['1']); ?> Wins!</h3>
            <?php elseif ($totals['2'] > $totals['1']): ?>
                <h3 class="text-success mt-4">🏆 <?php echo strtoupper($players['2']); ?> Wins!</h3>
            <?php else: ?>
                <h3 class="text-warning mt-4">🤝 It’s a tie!</h3>
            <?php endif; ?>

            <form method="GET" class="mt-4">
                <button type="submit" name="reset_game" value="1" class="btn btn-lg btn-primary">Play Again</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<audio id="ding" src="ding.mp3" preload="auto"></audio>
<audio id="buzzer" src="buzzer.mp3" preload="auto"></audio>
<div id="wrongX">❌</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll('.answer, .answer-card');
    const playerButtons = document.querySelectorAll('.player-btn');
    const ding = document.getElementById('ding');
    const buzzer = document.getElementById('buzzer');
    const wrongX = document.getElementById('wrongX');

    let activePlayer = 1;
    let errors = {1: 0, 2: 0};
    let stealMode = false;
    let stealingPlayer = null;
    let stealAttemptUsed = false;
    let round = <?php echo $round; ?>;
    const maxRounds = 4;
    let roundMultiplier = 1;

    // ✅ Pull persistent team totals from PHP session
    let teamTotals = {
        1: <?php echo $totals['1']; ?>,
        2: <?php echo $totals['2']; ?>
    };

    // Update totals display on page load
    document.getElementById('total1').textContent = teamTotals[1];
    document.getElementById('total2').textContent = teamTotals[2];

    // 🔹 Round multiplier logic
    function updateRoundMultiplier() {
        if (round <= 2) roundMultiplier = 1;
        else if (round === 3) roundMultiplier = 2;
        else roundMultiplier = 3;
    }
    updateRoundMultiplier();

    // 🔹 Player switching
    playerButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            if (stealMode) return;
            activePlayer = parseInt(btn.dataset.player);
            playerButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // 🔹 Wrong X popup
    function showWrongX() {
        wrongX.classList.add('show');
        buzzer.currentTime = 0;
        buzzer.play();
        setTimeout(() => wrongX.classList.remove('show'), 800);
    }

    // 🔹 Add strikes and trigger steal mode if 3 strikes
    function addError(player) {
        if (stealMode) {
            if (!stealAttemptUsed) {
                stealAttemptUsed = true;
                showWrongX();
                announceStealResult(false);
            }
            return;
        }

        if (errors[player] < 3) {
            errors[player]++;
            document.getElementById(`errors${player}`).innerHTML += '❌';
            showWrongX();
        }

        if (errors[player] === 3 && !stealMode) {
            stealMode = true;
            stealingPlayer = player === 1 ? 2 : 1;
            announceSteal(stealingPlayer);
        }
    }

    // 🔹 Announce steal mode
    function announceSteal(player) {
        showMessage(`Player ${player} can steal!`, "#ffc107");
        activePlayer = player;
        playerButtons.forEach(btn => btn.classList.remove('active'));
        document.querySelector(`[data-player="${player}"]`).classList.add('active');
    }

    // 🔹 Flip cards on keypress
    document.addEventListener('keydown', e => {
        const key = e.key;
        const index = parseInt(key) - 1;
        if (isNaN(index)) return;
        if (index >= 0 && index < cards.length) {
            const card = cards[index];
            const isRevealed = card.classList.contains('revealed');

            if (!isRevealed) {
                card.classList.add('revealed');
                ding.currentTime = 0;
                ding.play();

                const pts = parseInt(card.dataset.points);
                const scoreEl = document.getElementById(`score${activePlayer}`);
                scoreEl.textContent = parseInt(scoreEl.textContent) + pts * roundMultiplier;

                // Steal mode handling
                if (stealMode && !stealAttemptUsed) {
                    stealAttemptUsed = true;
                    let totalPoints = 0;
                    cards.forEach(c => {
                        if (c.classList.contains('revealed')) {
                            totalPoints += parseInt(c.dataset.points);
                        }
                    });
                    const scaled = totalPoints * roundMultiplier;
                    teamTotals[activePlayer] += scaled;
                    document.getElementById(`total${activePlayer}`).textContent = teamTotals[activePlayer];
                    saveTotals(); // ✅ persist to session
                    announceStealResult(true);
                }

            } else {
                addError(activePlayer);
            }
        } else {
            addError(activePlayer);
        }
    });

    // 🔹 Announce steal result
    function announceStealResult(success) {
        showMessage(
            success ? `Player ${activePlayer} steals the round! 🎉` : `Steal failed!`,
            success ? "#28a745" : "#dc3545"
        );
        stealMode = false;
        stealingPlayer = null;
    }

    // 🔹 Reusable message overlay
    function showMessage(text, color) {
        const msg = document.createElement('div');
        msg.textContent = text;
        msg.style.position = "fixed";
        msg.style.top = "50%";
        msg.style.left = "50%";
        msg.style.transform = "translate(-50%, -50%)";
        msg.style.backgroundColor = color;
        msg.style.color = "#fff";
        msg.style.padding = "20px 40px";
        msg.style.borderRadius = "10px";
        msg.style.fontSize = "1.5em";
        msg.style.fontWeight = "bold";
        msg.style.zIndex = "9999";
        document.body.appendChild(msg);
        setTimeout(() => msg.remove(), 2500);
    }

    // 🔹 Save totals to session (AJAX)
    function saveTotals() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_totals.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`total1=${teamTotals[1]}&total2=${teamTotals[2]}`);
    }
});
</script>

</body>
</html>
