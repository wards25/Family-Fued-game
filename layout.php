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
    margin: 0;
    height: 100%;
    overflow: hidden;
    background-color: #000;
}
iframe {
    border: none;
    width: 100%;
    height: 100%;
}
#soundBtn {
    position: fixed;
    top: 20px;
    right: 20px;
    font-size: 30px;
    cursor: pointer;
    color: #fff;
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    padding: 10px;
    z-index: 9999;
}
</style>
</head>
<body>

<!-- Persistent background music -->
<audio id="themeAudio" src="theme.mp3" loop></audio>
<div id="soundBtn">🔊</div>

<!-- Main content area (your pages load here) -->
<iframe id="contentFrame" src="index.php" name="contentFrame"></iframe>

<script>
const audio = document.getElementById('themeAudio');
const soundBtn = document.getElementById('soundBtn');

let isPlaying = localStorage.getItem('soundOn') === 'true';
audio.muted = !isPlaying;

function updateIcon() {
    soundBtn.textContent = isPlaying ? '🔊' : '🔇';
}

window.addEventListener('load', () => {
    updateIcon();
    if (isPlaying) {
        audio.play().catch(() => console.log('Autoplay blocked'));
    }
});

soundBtn.addEventListener('click', () => {
    if (isPlaying) {
        audio.pause();
        audio.muted = true;
        isPlaying = false;
    } else {
        audio.muted = false;
        audio.play().catch(() => console.log('Play failed'));
        isPlaying = true;
    }
    localStorage.setItem('soundOn', isPlaying);
    updateIcon();
});
</script>

</body>
</html>
