<?php
session_start();

// If form submitted, save players and go to start.php
if (isset($_POST['start_game'])) {
    $_SESSION['players'] = [
        1 => trim($_POST['player1']),
        2 => trim($_POST['player2'])
    ];
    $_SESSION['round'] = 1;
    $_SESSION['totals'] = [1 => 0, 2 => 0];
    $_SESSION['round_started'] = false;
    header("Location: start.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="logo.png">
<title>Ramosco Family Feud</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="fa-6/css/all.css" rel="stylesheet">

<style>
html, body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden; 
}

body {
    background-image: url('bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: 'Segoe UI', sans-serif;
}

.page-wrapper {
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.content {
    text-align: center;
}

.content img {
    max-height: 500px; 
    width: auto;
}

.card {
    background-color: #012060;
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0,0,0,0.4);
    color: #fff;
    padding: 2rem 2.5rem;
    width: 600px;
    max-width: 90vw;
}


</style>
</head>
<body>

<div class="page-wrapper">
    <div class="content">
        <img class="img-fluid mb-4" src="animation.gif" alt="Animation">

        <div class="card mx-auto">
            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="player1" class="form-control"
                               placeholder="TEAM 1" required autocomplete="off"
                               style="text-transform: uppercase;">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="player2" class="form-control"
                               placeholder="TEAM 2" required autocomplete="off"
                               style="text-transform: uppercase;">
                    </div>
                </div>

                <button type="submit" name="start_game" class="btn btn-warning btn-sm">
                    <i class="fa fa-play"></i>
                </button>
                <a class="btn btn-outline-secondary btn-sm" href="index.php">
                    <i class="fa-solid fa-home"></i>
                </a>
            </form>
        </div>
    </div>
</div>


<!-- Sound (same on every page) -->
<audio id="themeAudio" src="theme.mp3" loop></audio>
<div id="soundBtn" hidden>🔊</div>

<script>
// Shared sound logic
const audio = document.getElementById('themeAudio');
const soundBtn = document.getElementById('soundBtn');
let isPlaying = localStorage.getItem('soundOn') === 'true';

function updateIcon() {
    soundBtn.textContent = isPlaying ? '🔊' : '🔇';
}
updateIcon();

if (isPlaying) {
    audio.muted = false;
    audio.play().catch(err => console.log('Autoplay blocked:', err));
} else {
    audio.muted = true;
}

soundBtn.addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        audio.muted = true;
        isPlaying = false;
    } else {
        audio.muted = false;
        audio.play().catch(err => console.log('Audio play failed:', err));
        isPlaying = true;
    }
    localStorage.setItem('soundOn', isPlaying);
    updateIcon();
});
</script>
</body>
</html>
