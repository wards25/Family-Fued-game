<?php

// ----------------- GET READY (Start screen) -----------------
// Show the "Get Ready" start screen if pre-round is done but round hasn't started yet
if (!$gameOver && empty($_SESSION['round_started']) && !empty($_SESSION['pre_round_done'])) {
    // ensure players/totals available
    $players = $_SESSION['players'];
    $totals = $_SESSION['totals'];
    $starter = isset($_SESSION['starter']) ? (int) $_SESSION['starter'] : 1;
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Round <?php echo $round; ?> | Ramosco Family Feud</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="fa-6/css/all.css" rel="stylesheet">
        <style>
            body {
                background: url('bg.jpg') center/cover no-repeat;
                font-family: 'Segoe UI', sans-serif;
                color: #ffcc00;
                text-align: center;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .card {
                background-color: #012060;
                color: #fff;
                border: 3px solid #fff;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 0 15px #000;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <img class="img-fluid mb-3" src="logo.png" alt="Logo" style="height: 250px;">
            <div class="card mx-auto text-center py-5 mb-4">
                <h1 class="mb-2" style="font-size: 75px; color: #f3dc9f;"><b>ROUND <?php echo $round; ?></b></h1>
                <h4 style="font-size: 40px;">Get Ready!</h4>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-md-6 mb-3">
                    <div class="card p-4 text-center">
                        <h3 class="text-warning" style="font-size: 60px;">
                            <b><?php echo strtoupper($players['1']); ?> </b>:
                        </h3>
                        <div class="d-flex justify-content-center align-items-center">
                            <strong class="text-danger" style="font-size: 70px;"><?php echo $totals['1']; ?></strong>
                            &nbsp;<span class="text-light" style="font-size: 40px;"> pts</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="card p-4 text-center">
                        <h3 class="text-warning" style="font-size: 60px;">
                            <b><?php echo strtoupper($players['2']); ?> </b>:
                        </h3>
                        <div class="d-flex justify-content-center align-items-center">
                            <strong class="text-danger" style="font-size: 70px;"><?php echo $totals['2']; ?></strong>
                            &nbsp;<span class="text-light" style="font-size: 40px;">pts</span>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST">
                <button type="submit" name="start_round" class="btn btn-sm btn-warning mt-5 px-5"><i
                        class="fa-solid fa-play"></i> START ROUND <?php echo $round; ?></button>
            </form>

            <audio id="preround_sound" src="preround.mp3" preload="auto"></audio>

            <script>
                document.addEventListener('keydown', e => {
                    if (e.key.toLowerCase() === 's') {
                        const preRoundSound = document.getElementById('preround_sound');
                        if (preRoundSound) {
                            try {
                                if (preRoundSound.paused) {
                                    // ▶️ Play if currently paused
                                    preRoundSound.currentTime = 0;
                                    preRoundSound.play();
                                } else {
                                    // ⏸️ Stop if currently playing
                                    preRoundSound.pause();
                                    preRoundSound.currentTime = 0; // reset to beginning
                                }
                            } catch (err) {
                                console.warn('Audio toggle error:', err);
                            }
                        }
                    }
                });
            </script>
        </div>
    </body>

    </html>
    <?php
    exit();
}

// ----------------- MAIN ROUND (show question & answers) -----------------
if (!$gameOver && !empty($_SESSION['round_started'])) {
    // fetch a random question for this round (your original logic)
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

    // prepare variables for display
    $players = $_SESSION['players'];
    $totals = $_SESSION['totals'];
    $starter = isset($_SESSION['starter']) ? (int) $_SESSION['starter'] : 1;
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Round <?php echo $round; ?> | Ramosco Family Feud</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="fa-6/css/all.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', sans-serif;
                background-image: url('bg.jpg');
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                height: 100vh;
            }

            h1,
            h4 {
                color: #ffcc00;
                text-shadow: 0 0 10px #000;
            }

            .answer-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                width: 100%;
                max-width: 1300px;
                margin: 0 auto 2rem auto;
            }

            @media(max-width:768px) {
                .answer-grid {
                    grid-template-columns: 1fr;
                }
            }

            .answer-card {
                width: 100%;
                height: 120px;
                perspective: 1000px;
                display: block;
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

            .card-front,
            .card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 12px;
                backface-visibility: hidden;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }

            .card-front {
                background: #1a1a1a;
                border: 3px solid #ffffff;
                color: #ffffff;
                font-size: 2rem;
            }

            .card-back {
                background: #4278be;
                color: #fff;
                transform: rotateY(180deg);
                border: 3px solid #ffcc00;
                padding: 10px;
                text-align: center;
            }

            .card-back .text-warning {
                text-transform: uppercase;
                font-size: 1.8rem;
                color: #ffcc00 !important;
                text-shadow: 0 0 5px #000;
                margin-bottom: 10px;
            }

            .card-back .points-box {
                background: #dc3545;
                color: #fff;
                padding: 6px 16px;
                border-radius: 8px;
                font-size: 1.5rem;
                font-weight: bold;
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
                text-shadow: 0 0 30px rgba(255, 0, 0, 0.9);
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
        </style>
    </head>

    <body>

        <div class="container py-4 text-center">
            <h3 class="text-warning">ROUND <?php echo $round; ?></h3>

            <!-- Question -->
            <div class="card text-light shadow mb-4" style="background-color: #012060;">
                <div class="card-body">
                    <h2 id="faceoffQuestion" class="fw-bold text-light d-none" style="font-size:45px;">
                        <?php echo htmlspecialchars($random_question['question_text']); ?>
                    </h2>
                    <h2 id="pressEnterHint" class="fw-bold text-light" style="font-size:35px; color:#ffcc00;">
                    </h2>
                </div>
            </div>

            <div class="answer-grid">
                <?php foreach ($answers as $index => $answer): ?>
                    <div class="answer-card" data-answer="<?php echo htmlspecialchars($answer['answer_text']); ?>"
                        data-points="<?php echo (int) $answer['points']; ?>">
                        <div class="card-inner">
                            <div class="card-front text-warning" style="background-color:#012060;">
                                <?php echo $index + 1; ?>
                            </div>
                            <div class="card-back" style="background-color:#4278be;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="text-warning text-uppercase" style="font-size:50px;">
                                        <?php echo htmlspecialchars($answer['answer_text']); ?>
                                    </span>
                                    <span class="bg-danger text-white px-2 py-1 rounded ms-2" style="font-size:45px;">
                                        <?php echo $answer['points']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <!-- Player 1 -->
                <div class="col-6 mb-3">
                    <button
                        class="btn btn-sm btn-outline-warning player-btn <?php echo ($_SESSION['starter'] == 1) ? 'active' : ''; ?> mb-2 w-100"
                        data-player="1">
                        <b><?php echo strtoupper($players['1']); ?></b>
                    </button>
                    <div class="card">
                        <div class="card-body">
                            <h3 id="score1"><b>0</b></h3>
                            <div id="errors1" class="errors"></div>
                            <h6>TOTAL: <span id="total1"><?php echo $totals['1']; ?></span></h6>
                        </div>
                    </div>
                </div>

                <!-- Player 2 -->
                <div class="col-6 mb-3">
                    <button
                        class="btn btn-sm btn-outline-warning player-btn <?php echo ($_SESSION['starter'] == 2) ? 'active' : ''; ?> mb-2 w-100"
                        data-player="2">
                        <b><?php echo strtoupper($players['2']); ?></b>
                    </button>
                    <div class="card">
                        <div class="card-body">
                            <h3 id="score2"><b>0</b></h3>
                            <div id="errors2" class="errors"></div>
                            <h6>TOTAL: <span id="total2"><?php echo $totals['2']; ?></span></h6>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Timer Controls -->
            <div class="row mt-4 text-center justify-content-center align-items-center">
                <div class="col-6">
                    <button id="hostStart" class="btn btn-success w-100 py-2"><b>Start Timer</b></button>
                </div>
                <div class="col-6">
                    <button id="hostStop" class="btn btn-danger w-100 py-2" disabled><b>Stop Timer</b></button>
                </div>
            </div>

            <!-- Timer Countdown Display -->
            <div class="row mt-3">
                <div class="col-12">
                    <h2 id="timerDisplay"
                        style="color:#ffcc00;text-shadow:0 0 10px #000; font-size:3rem; font-weight:bold; display:none;">
                        5
                    </h2>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center align-items-center gap-1">
                <form method="GET" class="m-0">
                    <button type="submit" name="next_round" value="1" class="btn btn-sm btn-outline-success"><i
                            class="fa fa-forward"></i></button>
                </form>
                <form method="GET" class="m-0">
                    <button type="submit" name="reset_game" value="1" class="btn btn-sm btn-outline-danger"><i
                            class="fa fa-refresh"></i></button>
                </form>
                <button id="revealOneBtn" class="btn btn-outline-warning btn-sm">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
        </div>

        <audio id="ding" src="ding.mp3" preload="auto"></audio>
        <audio id="buzzer" src="buzzer.mp3" preload="auto"></audio>
        <audio id="question_ding" src="question_ding.mp3" preload="auto"></audio>
        <audio id="countdown_tick" src="tick.mp3" preload="auto"></audio>
        <audio id="win" src="win.mp3" preload="auto"></audio>
        <div id="wrongX">❌</div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const cards = document.querySelectorAll('.answer-card');
                const playerButtons = document.querySelectorAll('.player-btn');
                const ding = document.getElementById('ding');
                const buzzer = document.getElementById('buzzer');
                const wrongX = document.getElementById('wrongX');
                
                let stealResolved = false;
                let roundLocked = false;
                let activePlayer = <?php echo isset($_SESSION['starter']) ? (int) $_SESSION['starter'] : 1; ?>;
                playerButtons.forEach(btn => {
                    const player = parseInt(btn.dataset.player);
                    btn.disabled = player !== activePlayer;
                    btn.classList.toggle('active', player === activePlayer);
                });

                let errors = { 1: 0, 2: 0 };
                let stealMode = false;
                let stealAttemptUsed = false;
                let round = <?php echo $round; ?>;
                let roundMultiplier = 1;
                let teamTotals = { 1: <?php echo $totals['1']; ?>, 2: <?php echo $totals['2']; ?> };

                document.getElementById('total1').textContent = teamTotals[1];
                document.getElementById('total2').textContent = teamTotals[2];

                // --- TIMER VARIABLES ---
                const timerDisplay = document.getElementById('timerDisplay');
                const hostStart = document.getElementById('hostStart');
                const hostStop = document.getElementById('hostStop');
                let timer = null;
                let timeLeft = 5;

                // --- UPDATE MULTIPLIER BASED ON ROUND ---
                function updateRoundMultiplier() {
                    if (round <= 2) roundMultiplier = 1;
                    else if (round === 3) roundMultiplier = 2;
                    else roundMultiplier = 3;
                }
                updateRoundMultiplier();

                // --- PLAYER BUTTONS ---
                playerButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (stealMode) return;
                        activePlayer = parseInt(btn.dataset.player);
                        playerButtons.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                    });
                });

                // --- WRONG X ANIMATION ---
                function showWrongX() {
                    wrongX.classList.add('show');
                    if (buzzer) { try { buzzer.currentTime = 0; buzzer.play(); } catch (e) { } }
                    setTimeout(() => wrongX.classList.remove('show'), 800);
                }

                // --- ADD ERROR ---
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
                        const stealingPlayer = player === 1 ? 2 : 1;
                        announceSteal(stealingPlayer);
                    }
                }

                // --- ANNOUNCE STEAL ---
                function announceSteal(player) {
                    showMessage(`Player ${player} can steal!`, "#ffc107");
                    activePlayer = player;
                    playerButtons.forEach(btn => {
                        const btnPlayer = parseInt(btn.dataset.player);
                        btn.disabled = btnPlayer !== player;        // disable non-active
                        btn.classList.toggle('active', btnPlayer === player); // highlight active
                    });
                }

                // --- KEYBOARD INPUT (ANSWER REVEALING) ---
                document.addEventListener('keydown', e => {
                    if (stealResolved) return;
                    if (e.key === 'Enter') {
                        const question = document.getElementById('faceoffQuestion');
                        const hint = document.getElementById('pressEnterHint');
                        const qding = document.getElementById('question_ding');
                        if (question && hint && question.classList.contains('d-none')) {
                            question.classList.remove('d-none');
                            hint.remove();

                            // play question reveal sound
                            if (qding) {
                                try { qding.currentTime = 0; qding.play(); } catch (e) { }
                            }

                            // fade-in animation
                            question.style.transition = 'opacity 0.5s ease';
                            question.style.opacity = 0;
                            setTimeout(() => question.style.opacity = 1, 10);
                        }
                    }

                    if (e.key === '0') {
                        stopTimer(false);
                    }

                    if (e.key === '+') {
                        // toggle between player 1 and 2
                        activePlayer = (activePlayer === 1) ? 2 : 1;

                        // visually update which button is active
                        playerButtons.forEach(btn => btn.classList.remove('active'));
                        const activeBtn = document.querySelector(`[data-player="${activePlayer}"]`);
                        if (activeBtn) activeBtn.classList.add('active');

                        // show a quick on-screen message
                        // showMessage(`Switched to ${document.querySelector(`[data-player="${activePlayer}"] b`).textContent}!`, "#ffc107");
                    }

                    if (e.code === 'Space') {
                        e.preventDefault(); // prevent accidental page scroll or button press

                        if (hostStart.disabled === false) {
                            // Timer is currently stopped → Start it
                            hostStart.click();
                        } else if (hostStop.disabled === false) {
                            // Timer is running → Stop it
                            hostStop.click();
                        }
                    }

                    const key = e.key;
                    const index = parseInt(key) - 1;
                    if (isNaN(index)) return;
                    if (stealResolved) return;
                    if (index >= 0 && index < cards.length) {
                        const card = cards[index];
                        const isRevealed = card.classList.contains('revealed');

                        if (!isRevealed && !card.dataset.disabled) {
                            card.classList.add('revealed');
                            stopTimer(false);
                            if (ding) { try { ding.currentTime = 0; ding.play(); } catch (e) { } }

                            const pts = parseInt(card.dataset.points);
                            const scoreEl = document.getElementById(`score${activePlayer}`);
                            scoreEl.textContent = parseInt(scoreEl.textContent) + pts * roundMultiplier;

                            const allRevealed = Array.from(cards).every(c => c.classList.contains('revealed'));
                            if (allRevealed) {
                                let totalPoints = 0;
                                cards.forEach(c => { totalPoints += parseInt(c.dataset.points); });
                                const scaled = totalPoints * roundMultiplier;
                                teamTotals[activePlayer] += scaled;
                                document.getElementById(`total${activePlayer}`).textContent = teamTotals[activePlayer];
                                saveTotals();

                                // ✅ Play win sound
                                if (win) {
                                    try { win.currentTime = 0; win.play(); } catch (e) { }
                                }

                                setTimeout(() => {
                                    revealUnansweredCards();
                                    showMessage("All answers revealed! Host may proceed to next round.", "#28a745");
                                }, 800);
                            }

                            if (stealMode && !stealAttemptUsed) {
                                stealAttemptUsed = true;
                                let totalPoints = 0;
                                cards.forEach(c => { if (c.classList.contains('revealed')) totalPoints += parseInt(c.dataset.points); });
                                const scaled = totalPoints * roundMultiplier;
                                teamTotals[activePlayer] += scaled;
                                document.getElementById(`total${activePlayer}`).textContent = teamTotals[activePlayer];
                                saveTotals();
                                announceStealResult(true);
                            }

                        } else {
                            addError(activePlayer);
                        }
                    } else {
                        addError(activePlayer);
                    }
                });

                // --- STEAL RESULT ---
                function announceStealResult(success) {
                    stealResolved = true;
                    const cards = document.querySelectorAll('.answer-card');
                    cards.forEach(card => {
                        if (!card.classList.contains('revealed')) {
                            // Remove ability to flip by pressing number keys
                            card.dataset.disabled = "1";
                        }
                    });
                    // Steal successful – reveal remaining cards but don't add unearned points
                    if (success) {
                        revealUnansweredCards();
                        showMessage(`Player ${activePlayer} steals the round!`, "#28a745");

                        // ✅ Play win.mp3 only on successful steal
                        if (win) {
                            try { win.currentTime = 0; win.play(); } catch (e) { }
                        }
                    } else {
                        // ❌ Steal failed – award points to the opposing team (the one who had the board originally)
                        revealUnansweredCards();
                        const originalPlayer = activePlayer === 1 ? 2 : 1;
                        let totalPoints = 0;
                        cards.forEach(c => {
                            if (c.classList.contains('revealed')) {
                                totalPoints += parseInt(c.dataset.points);
                            }
                        });

                        const scaled = totalPoints * roundMultiplier;
                        teamTotals[originalPlayer] += scaled;
                        document.getElementById(`total${originalPlayer}`).textContent = teamTotals[originalPlayer];
                        saveTotals();

                        showMessage(`Steal failed! Player ${originalPlayer} keeps the points!`, "#dc3545");

                        // ✅ Optional: play a “buzzer” or fail sound
                        if (buzzer) {
                            try { buzzer.currentTime = 0; buzzer.play(); } catch (e) { }
                        }
                    }

                    stealMode = false;
                    stealAttemptUsed = false;
                }

                // --- REVEAL UNANSWERED CARDS ---
                function revealUnansweredCards() {
                    // --- REVEAL ONE UNANSWERED CARD (Descending Order) ---
                    const revealOneBtn = document.getElementById('revealOneBtn');
                    if (revealOneBtn) {
                        revealOneBtn.addEventListener('click', () => {
                            // Get all unrevealed cards (descending order)
                            const unrevealed = Array.from(cards).filter(c => !c.classList.contains('revealed'));
                            if (unrevealed.length === 0) {
                                showMessage("All cards are already revealed!", "#6c757d");
                                return;
                            }

                            // Reveal the last one (bottom-most card)
                            const card = unrevealed[unrevealed.length - 1];
                            card.classList.add('revealed');
                            const inner = card.querySelector('.card-inner');
                            if (inner) inner.style.transform = "rotateY(180deg)";

                            const back = card.querySelector('.card-back');
                            if (back) {
                                back.style.backgroundColor = "#6c757d"; // gray tone
                                back.style.opacity = "0.7";
                            }

                            // Optional: play a soft ding for reveal
                            if (ding) {
                                try { ding.currentTime = 0; ding.play(); } catch (e) { }
                            }
                        });
                    }

                }
                // --- TIMER SYSTEM (Host Controlled) ---
                hostStart.addEventListener('click', () => {
                    clearInterval(timer);
                    timeLeft = 5;
                    timerDisplay.textContent = timeLeft;
                    timerDisplay.style.display = 'block';
                    hostStart.disabled = true;
                    hostStop.disabled = false;

                    // ✅ Play start countdown sound
                    const countdownStart = document.getElementById('countdown_start');
                    if (countdownStart) {
                        try { countdownStart.currentTime = 0; countdownStart.play(); } catch (e) { }
                    }
                    // ✅ Play tick.mp3 and stop it after 4 seconds
                    const tick = document.getElementById('countdown_tick');
                    if (tick) {
                        try {
                            tick.currentTime = 0;
                            tick.play();
                            setTimeout(() => {
                                tick.pause();
                                tick.currentTime = 0; // reset to start
                            }, 4000); // stop after 4 seconds
                        } catch (e) { }
                    }

                    timer = setInterval(() => {
                        timeLeft--;
                        timerDisplay.textContent = timeLeft;

                        if (timeLeft <= 0) {
                            stopTimer(true);
                        }
                    }, 1000);
                });

                hostStop.addEventListener('click', () => {
                    stopTimer(false);
                });

                function stopTimer(auto = false) {
                    clearInterval(timer);
                    timerDisplay.style.display = 'none';
                    hostStart.disabled = false;
                    hostStop.disabled = true;

                    // 🛑 Stop the tick sound immediately
                    const tick = document.getElementById('countdown_tick');
                    if (tick) {
                        try {
                            tick.pause();
                            tick.currentTime = 0; // reset sound
                        } catch (e) { }
                    }

                    // 🛑 Also stop the countdown_start sound if it's still playing
                    const countdownStart = document.getElementById('countdown_start');
                    if (countdownStart) {
                        try {
                            countdownStart.pause();
                            countdownStart.currentTime = 0;
                        } catch (e) { }
                    }

                    if (auto) {
                        showMessage(`Player ${activePlayer} ran out of time!`, "#dc3545");
                        addError(activePlayer);
                    }
                }

                // --- SHOW MESSAGE ---
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
                    msg.style.boxShadow = "0 0 20px rgba(0,0,0,0.5)";
                    document.body.appendChild(msg);
                    setTimeout(() => msg.remove(), 2500);
                }

                // --- SAVE TOTALS ---
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
    <?php
    exit();
} // end main round block

// ----------------- GAME OVER -----------------
if ($gameOver) {
    $totals = $_SESSION['totals'];
    $winner = $totals['1'] > $totals['2'] ? $_SESSION['players']['1'] : $_SESSION['players']['2'];
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>Game Over</title>
    </head>

    <body style="text-align:center;padding:80px;background:#012060;color:#fff;">
        <h1>🏆 GAME OVER 🏆</h1>
        <h2>Winner: <?php echo htmlspecialchars($winner); ?></h2>
        <a href="?reset_game=1"
            style="display:inline-block;margin-top:20px;padding:10px 20px;background:#007bff;color:#fff;border-radius:8px;text-decoration:none;">Play
            Again</a>
    </body>

    </html>
    <?php
    exit();
}