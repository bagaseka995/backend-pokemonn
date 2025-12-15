<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($queryUser);

$cekPokemon = mysqli_query($conn, "SELECT * FROM user_pokemon WHERE user_id = '$user_id'");

if (mysqli_num_rows($cekPokemon) == 0) {
    $team = $user['team'];
    
    $pokemon_id = 1; 

    if ($team == 'Blanche') {
        $pokemon_id = 3; 
        $nick = "Squirtle";
    } elseif ($team == 'Candela') {
        $pokemon_id = 1; 
        $nick = "Charmander";
    } elseif ($team == 'Spark') {
        $pokemon_id = 4; 
        $nick = "Pikachu";
    }

    $insertPoke = "INSERT INTO user_pokemon (user_id, pokemon_id, nickname, is_active) 
                   VALUES ('$user_id', '$pokemon_id', '$nick', 1)";
    
    mysqli_query($conn, $insertPoke);
    
    header("Refresh:0"); 
}

$queryPartner = mysqli_query($conn, "
    SELECT mp.name 
    FROM user_pokemon up
    JOIN master_pokemon mp ON up.pokemon_id = mp.id
    WHERE up.user_id = '$user_id' AND up.is_active = 1
    LIMIT 1
");

$partnerName = "None"; 
if (mysqli_num_rows($queryPartner) > 0) {
    $dataPartner = mysqli_fetch_assoc($queryPartner);
    $partnerName = $dataPartner['name'];
}

$max_xp = 1000; 
$xp_percent = ($user['xp'] / $max_xp) * 100;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokemon Dashboard</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="dashboard-header">
        <div class="nav-left">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Pok%C3%A9mon_GO_logo.svg/1200px-Pok%C3%A9mon_GO_logo.svg.png" alt="Logo">
        </div>
        <div class="nav-center">
            <a href="my-team.php" style="color: white; text-decoration: none;">My Team</a>
        </div>
        <div class="nav-right">
            <a href="profile.php" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <i class="far fa-user-circle"></i>
                <span><?= htmlspecialchars($user['username']); ?></span>
            </a>
            
            <a href="logout.php" class="logout-icon" style="margin-left: 15px;" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <div class="dashboard-container">
        
        <img id="user-character" src="<?= $user['avatar']; ?>" alt="Character" class="dashboard-char-img">

        <div class="user-widget">
            <div class="widget-title">LEVEL <?= $user['level']; ?></div> 
            
            <div class="progress-bg">
                <div class="progress-fill" style="width: <?= $xp_percent; ?>%;"></div> 
            </div> 
            
            <div class="widget-stats">
                <span>Wins: <?= $user['wins']; ?></span>
                <span>Lose: <?= $user['losses']; ?></span>
            </div>
            
            <div class="widget-partner" id="partner-name">Partner: <?= $partnerName; ?></div>
        </div>

        <div class="btn-battle-container">
            <a href="battle.php" style="text-decoration: none;">
                <button class="btn-battle">
                    <span>BATTLE</span>
                </button>
            </a>
        </div>

    </div>

</body>
</html>