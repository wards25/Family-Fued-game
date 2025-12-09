<!DOCTYPE html>
<html>
<head>
<title>FAST MONEY BOARD</title>
<style>
.board{
    width:600px;
    margin:auto;
    background:linear-gradient(#013,#004);
    padding:20px;
    border-radius:10px;
    color:white;
    font-family:Arial;
}
.row{
    font-size:32px;
    margin:8px 0;
    display:flex;
    justify-content:space-between;
    padding:10px;
    background:#001a4d;
}
</style>
</head>
<body style="background:#012060">

<h1 style="text-align:center;color:white">FAST MONEY</h1>

<div class="board" id="board"></div>

<script>
function updateBoard(){
    fetch("fastmoney_api.php")
        .then(r=>r.json())
        .then(data=>{
            let html = "";
            data.forEach(r=>{
                html += `
                <div class="row">
                    <div>${r.answer_text ? r.answer_text : "-----"}</div>
                    <div>${r.points ? r.points : ""}</div>
                </div>`;
            });
            document.getElementById("board").innerHTML = html;
        });
}

setInterval(updateBoard, 500);
</script>

</body>
</html>
