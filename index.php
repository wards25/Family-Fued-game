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
body, html {
    height: 100%;
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f8f9fa;
    background-image: url('bg.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
}
.center-content {
    text-align: center;
}
#soundBtn {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
    color: #fff;
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    padding: 10px;
}
</style>
</head>
<body>

<div class="center-content">
    <!-- <img class="img-fluid mb-4" src="logo.png" alt="Logo"> -->
    <img class="img-fluid mb-4" src="animation.gif" alt="Animation">
    <div class="form-group">
        <a class="btn btn-warning btn-sm" href="players.php" id="startGameBtn"><i class="fa-solid fa-play"></i> START GAME</a>
    </div>
</div>

<!-- Sound icon -->
<audio id="themeAudio" src="theme.mp3" loop></audio>
<div id="soundBtn">🔊</div>

<script>
// Get audio element and sound button
const audio = document.getElementById('themeAudio');
const soundBtn = document.getElementById('soundBtn');

// Check if the audio state (playing or muted) is saved in localStorage
let isPlaying = localStorage.getItem('soundOn') === 'true';
audio.muted = !isPlaying;  // Set the audio to play or mute based on localStorage value

function updateIcon() {
    // Update the icon to represent the sound state (on/off)
    soundBtn.textContent = isPlaying ? '🔊' : '🔇';
}

// Update icon when page loads
window.addEventListener('load', () => {
    updateIcon();

    // Try to resume playing if it's already set to play
    if (isPlaying) {
        audio.play().catch(err => console.log('Autoplay blocked or failed to resume audio:', err));
    }
});

// Toggle sound state and update localStorage
soundBtn.addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        audio.muted = true;
        isPlaying = false;
    } else {
        audio.muted = false;
        audio.play().catch(err => console.log('Audio play failed', err));
        isPlaying = true;
    }

    // Save the sound state in localStorage to persist between page loads
    localStorage.setItem('soundOn', isPlaying);
    updateIcon();
});
</script>

</body>
</html>
