<?php
session_start();
include('dbconnect.php');

// CONFIG
$maxRounds = 4;

// INITIAL SESSION SETUP
if (!isset($_SESSION['round']))
    $_SESSION['round'] = 1;
if (!isset($_SESSION['totals']))
    $_SESSION['totals'] = ['1' => 0, '2' => 0];
if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = [
        '1' => 'PLAYER 1',
        '2' => 'PLAYER 2'
    ];
} else {
    $_SESSION['players']['1'] = strtoupper($_SESSION['players']['1']);
    $_SESSION['players']['2'] = strtoupper($_SESSION['players']['2']);
}

// Handle name submission
if (isset($_POST['set_names'])) {
    $_SESSION['players']['1'] = !empty($_POST['player1'])
        ? strtoupper(htmlspecialchars($_POST['player1']))
        : 'PLAYER 1';

    $_SESSION['players']['2'] = !empty($_POST['player2'])
        ? strtoupper(htmlspecialchars($_POST['player2']))
        : 'PLAYER 2';

    header("Location: start.php");
    exit();
}

// Handle Play/Pass result from pre-round face-off
if (isset($_POST['choose_playpass'])) {
    $starter = isset($_POST['starter']) ? (int) $_POST['starter'] : 1;
    $_SESSION['starter'] = $starter;
    $_SESSION['pre_round_done'] = true;

    // Update totals for pre-round points
    if (isset($_POST['pr_points'])) {
        $points = (int) $_POST['pr_points'];
        $_SESSION['totals'][$starter] += $points;
    }

    header("Location: start.php");
    exit();
}


// Start round button
if (isset($_POST['start_round'])) {
    $_SESSION['round_started'] = true;
    header("Location: start.php");
    exit();
}

// Next round
if (isset($_GET['next_round'])) {
    $_SESSION['round']++;
    unset($_SESSION['round_started'], $_SESSION['pre_round_done'], $_SESSION['starter']);
    header("Location: start.php");
    exit();
}

// Reset game
if (isset($_GET['reset_game'])) {
    $_SESSION['round'] = 1;
    $_SESSION['totals'] = ['1' => 0, '2' => 0];
    $_SESSION['players'] = ['1' => 'Player 1', '2' => 'Player 2'];
    unset($_SESSION['round_started'], $_SESSION['pre_round_done'], $_SESSION['starter']);
    header("Location: index.php");
    exit();
}

// Derived values
$round = (int) $_SESSION['round'];
$totals = $_SESSION['totals'];
$players = $_SESSION['players'];
$gameOver = ($round > $maxRounds);

// ----------------- PRE-ROUND FACE-OFF -----------------
if (!$gameOver && empty($_SESSION['pre_round_done']) && empty($_SESSION['round_started'])) {

    // fetch question and answers
    $pr_sql = "SELECT * FROM pre_round_questions WHERE round = $round LIMIT 1";
    $pr_res = $conn->query($pr_sql);

    if ($pr_res && $pr_res->num_rows > 0) {
        $pr_q = $pr_res->fetch_assoc();
        $pr_answers_res = $conn->query("SELECT * FROM pre_round_answers WHERE question_id = " . (int) $pr_q['id'] . " ORDER BY points DESC");
        $pr_answers = [];
        while ($r = $pr_answers_res->fetch_assoc())
            $pr_answers[] = $r;
    } else {
        $pr_q = ['question_text' => '⚠️ No pre-round question found.'];
        $pr_answers = [];
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Pre-Round Face-Off | Round <?php echo $round; ?></title>
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
                min-height: 100vh;
            }

            h1,
            h4 {
                color: #ffcc00;
                text-shadow: 0 0 10px #000;
            }

            .pr-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
            }

            .pr-card {
                background-color: #012060;
                color: #fff;
                border: 3px solid #fff;
                padding: 28px;
                border-radius: 12px;
                width: 90%;
                max-width: 1100px;
            }

            .pr-answers {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                width: 100%;
                max-width: 1300px;
                margin: 0 auto 2rem auto;
            }

            @media(max-width:768px) {
                .pr-answers {
                    grid-template-columns: repeat(1, 1fr);
                }
            }

            .pr-answer-card {
                width: 100%;
                height: 120px;
                perspective: 1000px;
                display: block;
            }

            .pr-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                text-align: center;
                transition: transform 0.6s;
                transform-style: preserve-3d;
                cursor: pointer;
            }

            .pr-answer-card.revealed .pr-card-inner {
                transform: rotateY(180deg);
            }

            .pr-front,
            .pr-back {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 12px;
                backface-visibility: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }

            .pr-front {
                background: #1a1a1a;
                border: 3px solid #ffffff;
                color: #ffffff;
                font-size: 1.6rem;
            }

            .pr-back {
                background: #4278be;
                color: #fff;
                transform: rotateY(180deg);
                border: 3px solid #ffcc00;
                padding: 10px;
                text-align: center;
                font-size: 1rem;
            }

            .pr-back .ans-text {
                text-transform: uppercase;
                font-size: 1.05rem;
                color: #ffcc00;
                text-shadow: 0 0 5px #000;
            }

            .pr-back .points {
                background: #dc3545;
                color: #fff;
                padding: 6px 12px;
                border-radius: 8px;
                font-weight: bold;
                margin-left: 8px;
            }

            .info {
                color: #fff;
                margin-top: 16px;
                font-size: 1rem;
            }

            .pr-answer-card.wrong .pr-card-inner {
                animation: flashRed 0.6s ease;
            }

            @keyframes flashRed {

                0%,
                100% {
                    transform: rotateY(0deg);
                    background-color: #1a1a1a;
                }

                50% {
                    background-color: #dc3545;
                }
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
            <h3 class="text-warning">PRE-ROUND FACE-OFF</h3>

            <!-- Question -->
            <div class="card text-light shadow mb-4" style="background-color: #012060;">
                <div class="card-body">
                    <h2 id="faceoffQuestion" class="fw-bold text-light d-none" style="font-size:45px;">
                        <?php echo htmlspecialchars($pr_q['question_text']); ?>
                    </h2>
                    <h2 id="pressEnterHint" class="fw-bold text-light" style="font-size:35px; color:#ffcc00;">
                    </h2>
                </div>
            </div>

            <div class="pr-answers" id="prAnswers">
                <?php foreach ($pr_answers as $i => $ans): ?>
                    <div class="pr-answer-card" data-pts="<?php echo (int) $ans['points']; ?>"
                        data-ans="<?php echo htmlspecialchars($ans['answer_text']); ?>" data-index="<?php echo $i; ?>">
                        <div class="pr-card-inner">
                            <div class="pr-front text-warning" style="background-color:#012060;"><?php echo $i + 1; ?></div>
                            <div class="pr-back" style="background-color:#4278be;">
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="ans-text"
                                        style="font-size:50px;"><?php echo htmlspecialchars($ans['answer_text']); ?></span>
                                    <span class="points text-white px-2 py-1 rounded ms-2"
                                        style="font-size:45px;"><?php echo (int) $ans['points']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <br><br><br><br>
        </div>

        <!-- hidden form for Play/Pass -->
        <form method="POST" id="prForm" style="display:none;">
            <input type="hidden" name="starter" id="prStarter" value="">
            <input type="hidden" name="choose_playpass" id="prChoice" value="">
            <input type="hidden" name="pr_points" id="prPoints" value="">
        </form>

        <audio id="preround_sound" src="preround.mp3" preload="auto"></audio>
        <audio id="prDing" src="ding.mp3" preload="auto"></audio>
        <audio id="prBuzzer" src="buzzer.mp3" preload="auto"></audio>
        <audio id="question_ding" src="question_ding.mp3" preload="auto"></audio>
        <audio id="countdown_tick" src="tick.mp3" preload="auto"></audio>
        <div id="wrongX">❌</div>

        <script>
            (function () {
                const cards = Array.from(document.querySelectorAll('.pr-answer-card'));
                const ding = document.getElementById('prDing');
                const buzzer = document.getElementById('prBuzzer');
                const players = <?php echo json_encode($players); ?>;

                let currentPlayer = 1;
                let playerScores = { 1: 0, 2: 0 };
                let playerAnswers = { 1: null, 2: null };
                let hasAnswered = { 1: false, 2: false };
                let zeroPressed = { 1: false, 2: false };
                let faceoffOver = false;

                const topPoints = Math.max(...cards.map(c => parseInt(c.dataset.pts) || 0));

                // ======== TOOLBAR + HOST TIMING CONTROLS ========
                const toolbar = document.createElement('div');

                toolbar.innerHTML = `
                <!-- Player Buttons -->
                <div class="row g-2 text-center align-items-center justify-content-center">
                    <div class="col-6">
                    <button id="btnP1" class="btn btn-outline-light w-100 py-2" style="background: #6c757d; color: #ffffff; border-radius: 8px; border: 1px solid #fff;"><b>${players[1]}</b></button>
                    </div>
                    <div class="col-6">
                    <button id="btnP2" class="btn btn-outline-light w-100 py-2" style="background: #6c757d; color: #ffffff; border-radius: 8px; border: 1px solid #fff;"><b>${players[2]}</b></button>
                    </div>
                </div>

                <!-- Current Player Label -->
                <div class="row mt-2 text-center">
                    <div class="col-12">
                    <div id="turnLabel" style="color:#fff;font-weight:bold;font-size:1.2rem;">
                        Current Team Answering: <span style="color:#ffcc00;text-shadow:0 0 8px #000;">${players[currentPlayer]}</span>
                    </div>
                    </div>
                </div>

                <!-- Host Buttons -->
                <div class="row mt-3 text-center align-items-center justify-content-center">
                    <div class="col-6">
                    <button id="hostStart" class="btn btn-success w-100 py-2"><b>Start</b></button>
                    </div>
                    <div class="col-6">
                    <button id="hostStop" class="btn btn-danger w-100 py-2" disabled><b>Stop</b></button>
                    </div>
                </div>
                `;

                document.querySelector('.container').append(toolbar);

                const btnP1 = document.getElementById('btnP1');
                const btnP2 = document.getElementById('btnP2');
                const hostStart = document.getElementById('hostStart');
                const hostStop = document.getElementById('hostStop');
                const turnLabel = document.getElementById('turnLabel');

                // Countdown display (top-right)
                const countdownDisplay = document.createElement('div');
                countdownDisplay.style.cssText = `
        position: fixed;
        top: 16px;
        right: 16px;
        background: rgba(0,0,0,0.75);
        color: #ffcc00;
        font-size: 1.6rem;
        font-weight: 700;
        padding: 8px 12px;
        border-radius: 8px;
        border: 2px solid #fff;
        z-index: 9999;
        display: none;
        `;
                document.body.appendChild(countdownDisplay);

                // ======== STATE ========
                let timer = null;
                let timeLimit = 5; // seconds
                let timerRunning = false;
                let acceptAnswers = false; // only true after host presses Stop

                function setActivePlayer(num) {
                    currentPlayer = num;
                    btnP1.style.background = num === 1 ? '#28a745' : '#6c757d';
                    btnP2.style.background = num === 2 ? '#28a745' : '#6c757d';
                    turnLabel.innerHTML = `Current Team Answering: <span style="color:#ffcc00;">${players[num]}</span>`;
                    // When active player changes, ensure host controls are reset
                    stopTimerSilently();
                    acceptAnswers = false;
                    hostStop.disabled = true;
                }

                // button handlers for choosing active player (host convenience)
                btnP1.onclick = () => setActivePlayer(1);
                btnP2.onclick = () => setActivePlayer(2);

                // ======== TIMER CONTROLS (HOST) ========
                function startTimerForPlayer() {
                    stopTimerSilently();
                    hostStart.disabled = true;
                    hostStop.disabled = false;
                    acceptAnswers = false;
                    timerRunning = true;

                    btnP1.disabled = true;
                    btnP2.disabled = true;


                    // ✅ Play start countdown sound
                    const countdownStart = document.getElementById('countdown_start');
                    if (countdownStart) {
                        try { countdownStart.currentTime = 0; countdownStart.play(); } catch (e) { }
                    }

                    const tick = document.getElementById('countdown_tick');
                    let timeLeft = timeLimit;
                    countdownDisplay.textContent = timeLeft;
                    countdownDisplay.style.display = 'block';

                    // ✅ Play tick.mp3 and stop it after 4 seconds
                    if (tick) {
                        try {
                            tick.currentTime = 0;
                            tick.play();
                            setTimeout(() => {
                                tick.pause();
                                tick.currentTime = 0; // reset to start
                            }, 5000); // stop after 4 seconds
                        } catch (e) { }
                    }

                    timer = setInterval(() => {
                        timeLeft--;
                        countdownDisplay.textContent = timeLeft;

                        if (timeLeft <= 0) {
                            stopTimerSilently();
                            handleHostTimeout();
                        }
                    }, 1000);
                }

                function stopTimerSilently() {
                    clearInterval(timer);
                    timer = null;
                    timerRunning = false;
                    countdownDisplay.style.display = 'none';
                    hostStart.disabled = false;

                    // Stop the tick sound when the timer stops
                    const tick = document.getElementById('countdown_tick');
                    if (tick) {
                        try {
                            tick.pause();
                            tick.currentTime = 0;  // reset to start
                        } catch (e) {
                            console.warn('Error stopping tick sound:', e);
                        }
                    }
                }

                function hostStopPressed() {
                    // host pressed Stop — accept answers now
                    stopTimerSilently();
                    acceptAnswers = true;
                    hostStop.disabled = true;
                    hostStart.disabled = false;
                    // announce(`Host stopped — ${players[currentPlayer]} may answer now.`, 1800);
                }

                // When host forgets to stop within timeLimit
                function handleHostTimeout() {
                    // announce(`${players[currentPlayer]} marked WRONG (host did not stop in time).`, 2200);
                    // show big X
                    wrongX.classList.add('show');
                    setTimeout(() => wrongX.classList.remove('show'), 1000);
                    buzzer.currentTime = 0; buzzer.play();

                    // mark current player as answered wrong
                    playerScores[currentPlayer] = 0;
                    playerAnswers[currentPlayer] = null;
                    hasAnswered[currentPlayer] = true;

                    // decide next action
                    const other = currentPlayer === 1 ? 2 : 1;
                    if (hasAnswered[other]) {
                        faceoffOver = true;
                        decideWinner();
                        return;
                    }

                    // Otherwise switch to the other player and let them have their chance
                    currentPlayer = other;
                    setActivePlayer(currentPlayer);
                    announce(`${players[currentPlayer]} now has the chance!`);
                }

                // Host Start/Stop button wiring
                hostStart.addEventListener('click', () => {
                    if (faceoffOver) return;
                    startTimerForPlayer();
                });
                hostStop.addEventListener('click', () => {
                    if (faceoffOver) return;
                    hostStopPressed();
                });

                function setActivePlayer(num) {
                    currentPlayer = num;
                    btnP1.style.background = num === 1 ? '#28a745' : '#6c757d';
                    btnP2.style.background = num === 2 ? '#28a745' : '#6c757d';
                    turnLabel.innerHTML = `Current Team Answering: <span style="color:#ffcc00;">${players[num]}</span>`;
                }
                btnP1.onclick = () => setActivePlayer(1);
                btnP2.onclick = () => setActivePlayer(2);

                // ======== UTILITIES ========
                function revealCard(card) {
                    if (!card || card.classList.contains('revealed')) return 0;

                    card.classList.add('revealed');
                    const pts = parseInt(card.dataset.pts) || 0;
                    const inner = card.querySelector('.pr-card-inner');

                    if (pts > 0) {
                        if (ding) { try { ding.currentTime = 0; ding.play(); } catch (e) { } }
                        inner.style.borderColor = '#28a745';
                    } else {
                        if (buzzer) { try { buzzer.currentTime = 0; buzzer.play(); } catch (e) { } }
                        inner.classList.add('wrong');
                        setTimeout(() => inner.classList.remove('wrong'), 600);
                        inner.style.borderColor = '#dc3545';
                    }

                    return pts;
                }

                function announce(msg, duration = 2500) {
                    const box = document.createElement('div');
                    box.textContent = msg;
                    Object.assign(box.style, {
                        position: 'fixed', top: '20px', left: '50%', transform: 'translateX(-50%)',
                        background: '#012060', color: '#fff', padding: '12px 24px', borderRadius: '10px',
                        border: '3px solid #ffcc00', fontWeight: 'bold', fontSize: '1.1rem', zIndex: '9999'
                    });
                    document.body.appendChild(box);
                    setTimeout(() => box.remove(), duration);
                }

                function showPlayPassOverlay(winner) {
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100%';
                    overlay.style.height = '100%';
                    overlay.style.display = 'flex';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.style.background = 'rgba(0,0,0,0.6)';
                    overlay.style.zIndex = '9999';

                    overlay.innerHTML = `
            <div style="background:#012060;border:3px solid #fff;padding:30px;border-radius:12px;text-align:center;color:#fff; width:800px;">
                <h2 style="color:#ffffff;margin-bottom:8px;font-size:45px;"><b class="text-warning" style="font-size:55px;">${players[winner]}</b><br> WINS THE FACE-OFF!</h2>
                <p style="margin-bottom:18px;">Do you want to <strong>PLAY</strong> or <strong>PASS</strong> this round?</p>
                <div style="display:flex;gap:16px;justify-content:center;">
                    <button id="prPlay" class="btn btn-success btn-sm"><i class="fa-solid fa-play"></i> Play</button>
                    <button id="prPass" class="btn btn-warning btn-sm"><i class="fa-solid fa-forward"></i> Pass</button>
                </div>
            </div>
        `;
                    document.body.appendChild(overlay);

                    document.getElementById('prPlay').addEventListener('click', () => {
                        document.getElementById('prStarter').value = winner;
                        document.getElementById('prChoice').value = 'play';
                        document.getElementById('prPoints').value = playerScores[winner]; // send points
                        document.getElementById('prForm').submit();
                    });
                    document.getElementById('prPass').addEventListener('click', () => {
                        const other = (winner === 1) ? 2 : 1;
                        document.getElementById('prStarter').value = other;
                        document.getElementById('prChoice').value = 'pass';
                        document.getElementById('prPoints').value = playerScores[winner]; // send points
                        document.getElementById('prForm').submit();
                    });

                }

                // ======== FACE-OFF LOGIC ========
                document.addEventListener('keydown', e => {
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

                    // --- "+" TO SWITCH ACTIVE PLAYER ---
                    if (e.key === '+') {
                        e.preventDefault();

                        // toggle between Player 1 and 2
                        currentPlayer = currentPlayer === 1 ? 2 : 1;
                        setActivePlayer(currentPlayer);

                        // quick highlight flash for feedback
                        const activeBtn = currentPlayer === 1 ? btnP1 : btnP2;
                        activeBtn.style.transform = 'scale(1.1)';
                        setTimeout(() => activeBtn.style.transform = 'scale(1)', 200);

                        // announcement message
                        // announce(`Switched to ${players[currentPlayer]}`, 1200);
                    }

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

                    if (faceoffOver) return;

                    const key = parseInt(e.key, 10);
                    if (isNaN(key)) return;

                    const idx = key - 1;
                    const card = cards[idx];

                    // ===== ZERO = Not on board =====
                    if (key === 0) {
                        wrongX.classList.add('show');
                        hostStopPressed();
                        if (buzzer) { try { buzzer.currentTime = 0; buzzer.play(); } catch (e) { } }
                        setTimeout(() => wrongX.classList.remove('show'), 800);

                        // announce(`${players[currentPlayer]}'s answer is NOT on the board!`);
                        playerScores[currentPlayer] = 0;
                        playerAnswers[currentPlayer] = null;
                        hasAnswered[currentPlayer] = true;
                        zeroPressed[currentPlayer] = true;

                        const other = currentPlayer === 1 ? 2 : 1;
                        if (hasAnswered[other]) {
                            faceoffOver = true;
                            decideWinner();
                            return;
                        }

                        // Otherwise switch to other player
                        currentPlayer = other;
                        setActivePlayer(currentPlayer);
                        return;
                        return;
                    }

                    if (!card) return;

                    if (timerRunning) {
                        hostStopPressed();
                    }

                    const ansText = card.dataset.ans || '';
                    const pts = parseInt(card.dataset.pts) || 0;

                    if (ansText.trim() === '' || pts === 0) {
                        // Wrong / empty answer
                        hostStopPressed();
                        const inner = card.querySelector('.pr-card-inner');
                        inner.classList.add('wrong');
                        setTimeout(() => inner.classList.remove('wrong'), 600);
                        if (buzzer) { try { buzzer.currentTime = 0; buzzer.play(); } catch (e) { } }
                        // announce('Wrong! No answer in this slot.');
                        playerScores[currentPlayer] = 0;
                        playerAnswers[currentPlayer] = null;
                        hasAnswered[currentPlayer] = true;

                        const other = currentPlayer === 1 ? 2 : 1;
                        if (!hasAnswered[other]) {
                            currentPlayer = other;
                            setActivePlayer(currentPlayer);
                            // announce(`${players[currentPlayer]} now has the chance!`);
                        } else {
                            // Both players answered → decide winner
                            faceoffOver = true;
                            decideWinner();
                        }
                        return;
                    }

                    // Reveal card normally
                    const pointsEarned = revealCard(card);
                    playerScores[currentPlayer] = pointsEarned;
                    playerAnswers[currentPlayer] = ansText;
                    hasAnswered[currentPlayer] = true;

                    if (pointsEarned === topPoints) {
                        // Top answer ends face-off immediately
                        faceoffOver = true;
                        setTimeout(() => showPlayPassOverlay(currentPlayer), 1200);
                        return;
                    }

                    const other = currentPlayer === 1 ? 2 : 1;
                    if (!hasAnswered[other]) {
                        currentPlayer = other;
                        setActivePlayer(currentPlayer);
                        // announce(`${players[currentPlayer]} now has the chance!`);
                    }
                    else {
                        // Both players answered → decide winner
                        faceoffOver = true;
                        decideWinner();
                    }
                });

                function decideWinner() {
                    const p1 = playerScores[1];
                    const p2 = playerScores[2];

                    // Both zero → continue face-off
                    if (p1 === 0 && p2 === 0) {
                        hasAnswered = { 1: false, 2: false };
                        zeroPressed = { 1: false, 2: false };
                        faceoffOver = false;
                        return;
                    }

                    let winner;
                    if (p1 > p2) winner = 1;
                    else if (p2 > p1) winner = 2;
                    else winner = Math.random() < 0.5 ? 1 : 2;

                    setTimeout(() => {
                        announce(`${players[1]}: ${playerAnswers[1] || '-'} (${p1} pts) | ${players[2]}: ${playerAnswers[2] || '-'} (${p2} pts)`, 3000);
                    }, 800);

                    setTimeout(() => showPlayPassOverlay(winner), 3000);
                }
            })();
        </script>

    </body>

    </html>
    <?php
    exit();
}
include_once('main_round.php');
