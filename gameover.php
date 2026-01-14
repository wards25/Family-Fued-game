<?php
session_start();
$totals = $_SESSION['totals'];
$winnerPlayer = ($totals['1'] > $totals['2']) ? 1 : 2;
$winnerName = $_SESSION['players'][$winnerPlayer];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Round Winner</title>
    <meta>
    <style>
        body {
            background: #012060;
            color: #fff;
            text-align: center;
            padding: 80px;
            font-family: Arial;
            margin-top: 100px;
        }

        #confetti-canvas {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 9999;
        }

        .team-win {
            font-size: 90px;
        }

        .round-win {
            font-size: 100px;
        }
    </style>
</head>

<body>
    <canvas id="confetti-canvas"></canvas>
    <h1 class="round-win">🏆 GAME WINNER! 🏆</h1>
    <h1 class='team-win'>TEAM <?php echo htmlspecialchars($winnerName); ?> WINS THE GAME!</h1>
    <p></p>
    <audio id="win-sound" src="win.mp3"></audio>
</body>

</html>
<script>
    const canvas = document.getElementById("confetti-canvas");
    const ctx = canvas.getContext("2d");

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const confetti = [];
    const colors = ["#ff0", "#0f0", "#0ff", "#f0f", "#ff5722"];
    let winAudio = document.getElementById("win-sound");

    winAudio.currentTime = 0;
    winAudio.play().catch(e => console.log("Win play prevented:", e));

    for (let i = 0; i < 300; i++) {
        confetti.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 6 + 2,
            d: Math.random() * 10,
            color: colors[Math.floor(Math.random() * colors.length)]
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        confetti.forEach(c => {
            ctx.beginPath();
            ctx.fillStyle = c.color;
            ctx.arc(c.x, c.y, c.r, 0, Math.PI * 2);
            ctx.fill();
        });
        update();
    }

    function update() {
        confetti.forEach(c => {
            c.y += Math.cos(c.d) + 1 + c.r / 2;
            c.x += Math.sin(c.d);

            if (c.y > canvas.height) {
                c.y = -10;
                c.x = Math.random() * canvas.width;
            }
        });
    }

    setInterval(draw, 20);

    window.addEventListener("resize", () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    });
</script>