<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$qUser = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$userData = mysqli_fetch_assoc($qUser);

$qMyPoke = mysqli_query($conn, "
    SELECT up.*, mp.name, mp.image_path, mp.id as master_id 
    FROM user_pokemon up
    JOIN master_pokemon mp ON up.pokemon_id = mp.id
    WHERE up.user_id = '$user_id' AND up.is_active = 1
");

if (mysqli_num_rows($qMyPoke) == 0) {
    echo "<script>alert('Pilih Partner Pokemon dulu di My Team!'); window.location='my-team.php';</script>";
    exit;
}
$myPoke = mysqli_fetch_assoc($qMyPoke);

$mySpeciesID = $myPoke['master_id'];
$qEnemy = mysqli_query($conn, "SELECT * FROM master_pokemon WHERE id != '$mySpeciesID' ORDER BY RAND() LIMIT 1");
$enemyPoke = mysqli_fetch_assoc($qEnemy);

if (isset($_GET['result'])) {
    $hasil = $_GET['result']; 
    
    if ($hasil == 'win') {
        $newXP = $userData['xp'] + 1000;
        $newWins = $userData['wins'] + 1;
        $levelUp = floor($newXP / 1000) + 1;
        
        $updateUser = "UPDATE users SET xp = '$newXP', wins = '$newWins', level = '$levelUp' WHERE id = '$user_id'";
        mysqli_query($conn, $updateUser);
        
        $pokeID = $myPoke['id'];
        mysqli_query($conn, "UPDATE user_pokemon SET current_level = current_level + 1 WHERE id = '$pokeID'");
        
    } elseif ($hasil == 'lose') {
        $newLosses = $userData['losses'] + 1;
        mysqli_query($conn, "UPDATE users SET losses = '$newLosses' WHERE id = '$user_id'");
    }
    
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Battle Arena (First to 3)</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/battle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="dashboard-header">
        <div class="nav-left">
            <img src="assets/logo-pokemon.png" alt="Logo">
        </div>
        <div class="nav-right">
            <a href="dashboard.php" class="logout-icon" onclick="return confirm('Kabur dari battle? Progress skor akan hilang!')">
                <i class="fas fa-times-circle"></i>
            </a>
        </div>
    </header>

    <div class="battle-overlay" id="battle-overlay"></div>

    <div class="battle-container" id="battle-container">
        
        <div class="spotlight-player" id="spotlight-player"></div>
        <div class="spotlight-bot" id="spotlight-bot"></div>
        
        <div class="scoreboard">
            
            <div class="score-col">
                <div class="score-text player-text">YOU</div>
                <div class="score-badge" id="score-player">0</div>
            </div>
            
            <div class="center-col">
                <div class="to-win-text">TO WIN: 3</div>
                <div class="vs-text-static">VS</div>
            </div>
            
            <div class="score-col">
                <div class="score-text enemy-text">ENEMY</div>
                <div class="score-badge" id="score-bot">0</div>
            </div>

        </div>

        <div class="bot-area">
            <img src="assets/bot.png" alt="Enemy" class="bot-char" onerror="this.src='assets/default.png'">
        </div>

        <div class="skill-display-container" id="skill-display-container">
            <div><div class="skill-display skill-display-player" id="player-skill-display"><i class="fas fa-question"></i></div></div>
            <div><div class="skill-display skill-display-bot" id="bot-skill-display"><i class="fas fa-question"></i></div></div>
        </div>

        <div class="player-area">
            <img src="<?= $userData['avatar']; ?>" alt="Trainer" class="player-char">
            <img src="assets/<?= $myPoke['image_path']; ?>" class="battle-poke-img my-poke-pos">
        </div>

        <div class="battle-controls" id="battle-controls">
            <button class="btn-action btn-attack" title="Attack" id="btn-attack"><i class="fas fa-fist-raised"></i></button>
            <button class="btn-action btn-skill" title="Skill" id="btn-skill"><i class="fas fa-bolt"></i></button>
            <button class="btn-action btn-defend" title="Block" id="btn-defend"><i class="fas fa-shield-alt"></i></button>
        </div>

        <div class="battle-result" id="battle-result"></div>

    </div>

    <div class="battle-modal" id="battle-modal">
        <div class="modal-content" id="modal-content">
            <div class="modal-title" id="modal-title">VICTORY!</div>
            <div class="modal-reward" id="modal-reward">Score: 3 - 1</div>
            <div class="modal-actions">
                <button class="modal-btn" id="btn-finish">Claim Rewards</button>
            </div>
        </div>
    </div>

    <script>
        let isBattleActive = false;
        let playerScore = 0;
        let botScore = 0;
        const TARGET_SCORE = 3;

        const scoreElPlayer = document.getElementById('score-player');
        const scoreElBot = document.getElementById('score-bot');
        const battleControls = document.getElementById('battle-controls');
        const battleOverlay = document.getElementById('battle-overlay');
        const spotlightPlayer = document.getElementById('spotlight-player');
        const spotlightBot = document.getElementById('spotlight-bot');
        const battleResult = document.getElementById('battle-result');
        const skillDisplayContainer = document.getElementById('skill-display-container');
        const playerSkillDisplay = document.getElementById('player-skill-display');
        const botSkillDisplay = document.getElementById('bot-skill-display');
        const battleModal = document.getElementById('battle-modal');
        const modalContent = document.getElementById('modal-content');
        const modalTitle = document.getElementById('modal-title');
        const modalReward = document.getElementById('modal-reward');
        const btnFinish = document.getElementById('btn-finish');

        function determineRoundWinner(pChoice, bChoice) {
            if (pChoice === bChoice) return 'draw';
            if (
                (pChoice === 'attack' && bChoice === 'skill') ||
                (pChoice === 'skill' && bChoice === 'defend') ||
                (pChoice === 'defend' && bChoice === 'attack')
            ) {
                return 'win';
            }
            return 'lose';
        }

        function getBotChoice() {
            const choices = ['attack', 'skill', 'defend'];
            return choices[Math.floor(Math.random() * choices.length)];
        }

        function getIcon(choice) {
            if(choice == 'attack') return 'fa-fist-raised';
            if(choice == 'skill') return 'fa-bolt';
            if(choice == 'defend') return 'fa-shield-alt';
            return 'fa-question';
        }

        async function performBattle(playerChoice) {
            if (isBattleActive) return;
            isBattleActive = true;
            battleControls.style.opacity = '0.5';
            battleControls.style.pointerEvents = 'none';

            battleOverlay.classList.add('active');
            spotlightPlayer.classList.add('active');
            spotlightBot.classList.add('active');

            const botChoice = getBotChoice();
            playerSkillDisplay.innerHTML = `<i class="fas ${getIcon(playerChoice)}"></i>`;
            botSkillDisplay.innerHTML = `<i class="fas ${getIcon(botChoice)}"></i>`;
            skillDisplayContainer.classList.add('show');

            await new Promise(r => setTimeout(r, 1500));

            const roundResult = determineRoundWinner(playerChoice, botChoice);

            let resultText = "";
            if (roundResult === 'win') {
                playerScore++;
                scoreElPlayer.innerText = playerScore;
                resultText = "ROUND WON!";
                scoreElPlayer.style.color = "#00ff00";
                setTimeout(() => scoreElPlayer.style.color = "white", 500);
            } else if (roundResult === 'lose') {
                botScore++;
                scoreElBot.innerText = botScore;
                resultText = "ROUND LOST!";
                scoreElBot.style.color = "#ff0000";
                setTimeout(() => scoreElBot.style.color = "white", 500);
            } else {
                resultText = "DRAW!";
            }

            battleResult.textContent = resultText;
            battleResult.className = 'battle-result show';
            if(roundResult === 'win') battleResult.classList.add('result-win');
            if(roundResult === 'lose') battleResult.classList.add('result-lose');

            await new Promise(r => setTimeout(r, 1500));

            if (playerScore >= TARGET_SCORE || botScore >= TARGET_SCORE) {
                finishMatch();
            } else {
                resetRound();
            }
        }

        function resetRound() {
            battleResult.classList.remove('show');
            skillDisplayContainer.classList.remove('show');
            battleOverlay.classList.remove('active');
            spotlightPlayer.classList.remove('active');
            spotlightBot.classList.remove('active');
            battleControls.style.opacity = '1';
            battleControls.style.pointerEvents = 'all';
            isBattleActive = false;
        }

        function finishMatch() {
            skillDisplayContainer.classList.remove('show');
            battleResult.classList.remove('show');
            battleControls.style.display = 'none';

            if (playerScore >= TARGET_SCORE) {
                modalContent.className = 'modal-content modal-win';
                modalTitle.innerText = "VICTORY!";
                modalReward.innerText = `Final Score: ${playerScore} - ${botScore}\nXP +1000`;
                btnFinish.innerText = "Claim Victory";
                btnFinish.onclick = function() { window.location.href = '?result=win'; };
            } else {
                modalContent.className = 'modal-content modal-lose';
                modalTitle.innerText = "DEFEATED!";
                modalReward.innerText = `Final Score: ${playerScore} - ${botScore}`;
                btnFinish.innerText = "Return to Base";
                btnFinish.onclick = function() { window.location.href = '?result=lose'; };
            }
            battleModal.classList.add('show');
        }

        document.getElementById('btn-attack').onclick = () => performBattle('attack');
        document.getElementById('btn-skill').onclick = () => performBattle('skill');
        document.getElementById('btn-defend').onclick = () => performBattle('defend');
    </script>
</body>
</html>