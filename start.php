<?php
session_start();
include('dbconnect.php');

// CONFIG
$maxRounds = 4;

if (!isset($_SESSION['starter'])) {
    $_SESSION['starter'] = 1; // Player 1 always starts
}

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
// if (isset($_POST['choose_playpass'])) {
//     $starter = isset($_POST['starter']) ? (int) $_POST['starter'] : 1;
//     $_SESSION['starter'] = $starter;
//     $_SESSION['pre_round_done'] = true;

//     // Update totals for pre-round points
//     if (isset($_POST['pr_points'])) {
//         $points = (int) $_POST['pr_points'];
//         $_SESSION['totals'][$starter] += $points;
//     }

//     header("Location: start.php");
//     exit();
// }


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

include_once('main_round.php');
