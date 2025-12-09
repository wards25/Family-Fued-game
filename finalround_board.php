<!DOCTYPE html>
<html>
<head>
<title>FAST MONEY BOARD</title>
<style>
body {
    background: #012060;
    font-family: Arial, sans-serif;
    color: white;
}

.board {
    width: 600px;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
}

.row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 32px;
    margin: 8px 0;
    padding: 10px;
    background: #001a4d;
    border-radius: 5px;
}

.answer {
    flex: 1;
    padding-left: 20px;
    text-align: left;
}

.points {
    width: 80px;
    text-align: center;
    background-color: #0056b3;
    padding: 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 22px;
    color: white;
}

.points-revealed {
    background-color: #28a745;
}

.total {
    width: 600px;
    margin: 20px auto;
    text-align: right;
    font-size: 28px;
    color: white;
}
</style>
</head>
<body>

<h1 style="text-align:center;color:white;">FAST MONEY</h1>

<div class="board" id="board"></div>
<div class="total">Total Points: <span id="total-points">0</span></div>

<script>
// Helper function to get URL parameters
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(window.location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Current player from URL (default 1)
const player = getUrlParameter('player') || 1;

// Store data and revealed state for **this player**
let boardData = [];
let revealedPoints = {};

// Global total points across players
let totalPoints = 0;

// Update the board UI
function updateBoard() {
    fetch(`fastmoney_api.php?player=${player}`)
        .then(r => r.json())
        .then(data => {
            boardData = data;
            let html = "";
            data.forEach(r => {
                const isRevealed = revealedPoints[r.question_id] ? "points-revealed" : "";
                const displayStyle = revealedPoints[r.question_id] ? "inline" : "none";

                html += `
                <div class="row">
                    <div class="answer">${r.answer_text ? r.answer_text : "-----"}</div>
                    <div class="points ${isRevealed}" id="points-${player}-${r.question_id}" style="display: ${displayStyle};">
                        ${r.points ? r.points : ""}
                    </div>
                </div>`;
            });
            document.getElementById("board").innerHTML = html;
        })
        .catch(err => console.error(err));
}

// Reveal points for a specific question
function revealPoints(id) {
    if (!revealedPoints[id]) {
        revealedPoints[id] = true;

        const pointsElement = document.getElementById(`points-${player}-${id}`);
        if (pointsElement) {
            pointsElement.style.display = "inline";
            pointsElement.classList.add("points-revealed");

            const pts = parseInt(pointsElement.textContent) || 0;
            totalPoints += pts;
            document.getElementById("total-points").textContent = totalPoints;
        }
    }
}

// Initial load and real-time update
updateBoard();
setInterval(updateBoard, 1000);

// Keypress to reveal points 1-5
document.addEventListener('keydown', function(event) {
    const key = event.key;
    if (key >= 1 && key <= 5) {
        const questionId = boardData[key - 1]?.question_id;
        if (questionId) revealPoints(questionId);
    }
});
</script>

</body>
</html>
