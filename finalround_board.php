<!DOCTYPE html>
<html>

<head>
    <title>FAST MONEY BOARD</title>

    <style>
        body {
            background: #001a33;
            font-family: Arial Black, sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        .board {
            width: 650px;
            margin: 30px auto;
            padding: 20px;
            border-radius: 12px;
            background: #023e8a;
            border: 6px solid #1a73e8;
            box-shadow: 0 0 30px #1a73e8;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 32px;
            margin: 12px 0;
            padding: 10px 20px;
            background: black;
            border-radius: 6px;
            border: 3px solid #00b4d8;
            box-shadow: inset 0 0 8px #0096c7;
        }

        .answer {
            flex: 1;
            text-align: left;
            letter-spacing: 2px;
        }

        .points {
            width: 80px;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            background: #333;
            color: yellow;
            font-size: 26px;
            font-weight: bold;
            border: 3px solid #ffc300;
            display: none;
        }

        .points-revealed {
            background: #0f0;
            color: black;
            border-color: #9bff00;
        }

        .total {
            width: 650px;
            margin: 10px auto;
            text-align: right;
            font-size: 32px;
            font-weight: bold;
            color: white;
            text-shadow: 0 0 10px #ffd60a;
        }

        h1 {
            text-align: center;
            font-size: 42px;
            margin-top: 20px;
            color: #ffd60a;
            text-shadow: 0 0 20px #ffc300;
        }

        .timer-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }

        .timer {
            margin: auto;
            width: 90px;
            height: 90px;
            background: #0077ff;
            border-radius: 50%;
            border: 6px solid #00b4d8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: white;
            font-weight: bold;
            box-shadow: 0 0 25px #00b4d8;
            margin-bottom: 10px;
        }

        .start-msg {
            font-size: 20px;
            color: #ffd60a;
            text-shadow: 0 0 10px #ffc300;
        }
    </style>

</head>

<body>

    <h1>FAST MONEY</h1>

    <div class="timer-wrapper">
        <div class="timer" id="timer">25</div>
        <div class="start-msg" id="start-msg" hidden></div>
    </div>
    <audio id="win-sound" src="win.mp3" preload="auto"></audio>
    <audio id="buzzer-sound" src="buzzer.mp3" preload="auto"></audio>
    <audio id="tick-sound" src="tick.mp3" preload="auto"></audio>
    <audio id="ding-sound" src="ding.mp3" preload="auto"></audio>

    <div class="board" id="board"></div>
    <div class="total">TOTAL: <span id="total-points">0</span></div>

    <script>
        // URL parameter
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            const results = regex.exec(window.location.search);
            return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        const player = getUrlParameter('player') || 1;
        let boardData = [];
        let revealedPoints = {};
        let totalPoints = 0;

        let tickAudio = document.getElementById("tick-sound");
        let dingAudio = document.getElementById("ding-sound");
        let audioAllowed = false;

        let timeLeft = 25;
        const timerElement = document.getElementById("timer");
        const startMsg = document.getElementById("start-msg");

        let timerInterval = null;
        let timerRunning = false;

        // Update board
        function updateBoard() {
            fetch(`fastmoney_api.php?player=${player}`)
                .then(r => r.json())
                .then(data => {
                    boardData = data;
                    let html = "";
                    data.forEach(r => {
                        const isRevealed = revealedPoints[r.question_id] ? "points-revealed" : "";
                        const displayStyle = revealedPoints[r.question_id] ? "inline-block" : "none";

                        html += `
                <div class="row">
                    <div class="answer">${r.answer_text || "-----"}</div>
                    <div class="points ${isRevealed}" 
                         id="points-${player}-${r.question_id}" 
                         style="display: ${displayStyle};">
                        ${r.points || ""}</div>
                </div>`;
                    });
                    document.getElementById("board").innerHTML = html;
                });
        }

        // Reveal points
        let buzzerAudio = document.getElementById("buzzer-sound");
        let winAudio = document.getElementById("win-sound");

        function revealPoints(id) {
            if (!revealedPoints[id]) {
                revealedPoints[id] = true;
                const pointsElement = document.getElementById(`points-${player}-${id}`);
                if (pointsElement) {
                    pointsElement.style.display = "inline-block";
                    pointsElement.classList.add("points-revealed");

                    const pts = parseInt(pointsElement.textContent) || 0;
                    totalPoints += pts;
                    document.getElementById("total-points").textContent = totalPoints;

                    // Play buzzer if points are 0
                    if (pts === 0 && audioAllowed) {
                        buzzerAudio.currentTime = 0;
                        buzzerAudio.play().catch(e => console.log("Buzzer play prevented:", e));
                    }
                    //when have points play ding
                    else if (totalPoints >= 200) {
                        winAudio.currentTime = 0;
                        winAudio.play().catch(e => console.log("Win play prevented:", e));
                    }
                    else {
                        dingAudio.play();
                    }
                }

                if (Object.keys(revealedPoints).length === boardData.length) {
                    saveTotalToDatabase();
                }
            }
        }
        // Fetch total points of player 1 if current player is 2
        if (player == 2) {
            fetch('fastmoney_get_total.php?player=1')
                .then(r => r.json())
                .then(data => {
                    if (data.total !== undefined) {
                        document.getElementById('total-points').textContent = data.total;
                        totalPoints = parseInt(data.total);
                    }
                })
                .catch(err => console.error("Error fetching player 1 total:", err));
        }


        // Allow audio after first interaction
        document.addEventListener("click", () => { audioAllowed = true; }, { once: true });
        document.addEventListener("keydown", () => { audioAllowed = true; }, { once: true });

        // Timer function
        function startTimer() {
            timerInterval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    timerRunning = false;
                    timerElement.textContent = "0";
                    tickAudio.pause();
                    tickAudio.currentTime = 0;
                    dingAudio.play();
                } else {
                    timerElement.textContent = timeLeft;
                    if (audioAllowed) {
                        tickAudio.currentTime = 0;
                        tickAudio.play().catch(e => console.log("Audio play prevented:", e));
                    }
                    timeLeft--;
                }
            }, 1000);
        }

        // Update board
        setInterval(() => {
            updateBoard();

            // Check for host messages
            fetch("fastmoney_last_result.php")
                .then(r => r.text())
                .then(status => {
                    if (status === "duplicate") {
                        if (audioAllowed) {
                            buzzerAudio.currentTime = 0;
                            buzzerAudio.play();
                        }

                        // Clear message so it doesn't repeat
                        fetch("fastmoney_last_result.php?msg=");
                    }
                });
        }, 1000);

        let saveSent = false;
        function saveTotalToDatabase() {
            if (saveSent) return;
            saveSent = true;

            fetch("fastmoney_save_total.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `player=${player}&total=${totalPoints}`
            })
                .then(r => r.text())
                .then(res => console.log("Saved:", res))
                .catch(err => console.error("Save error:", err));
        }

        // Key handling: SPACE to start/pause, 1–5 to reveal
        document.addEventListener('keydown', function (event) {
            // SPACE pressed
            if (event.code === "Space") {
                if (!timerRunning) {
                    startMsg.style.display = "none";
                    timerRunning = true;
                    startTimer();
                } else {
                    // Pause timer and play ding
                    tickAudio.pause();
                    clearInterval(timerInterval);
                    timerRunning = false;
                    dingAudio.play();
                }
            }

            // Reveal answers
            if (event.key >= 1 && event.key <= 5) {
                const questionId = boardData[event.key - 1]?.question_id;
                if (questionId) revealPoints(questionId);
            }
        });
    </script>

</body>

</html>