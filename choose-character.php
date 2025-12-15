<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['select'])) {
    $team = $_GET['select'];
    $user_id = $_SESSION['user_id'];
    
    $avatar = "assets/default.png";
    if ($team == 'Blanche') {
        $avatar = "assets/blanche.jpg"; 
    } elseif ($team == 'Candela') {
        $avatar = "assets/candela.png";
    } elseif ($team == 'Spark') {
        $avatar = "assets/spark.png";
    }

    $query = "UPDATE users SET team = '$team', avatar = '$avatar' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Selamat bergabung dengan Team $team!');
                window.location.href = 'dashboard.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Character</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/choose-character.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <h1 class="page-title">Choose Your Character!</h1>

    <div class="character-container">
        
        <div class="char-card card-blue">
            <h3 class="char-name">BLANCHE</h3>
            <img src="assets/blanche.png" alt="Blanche" class="char-img">
            <a href="?select=Blanche" class="btn-select">SELECT</a>
        </div>

        <div class="char-card card-red">
            <h3 class="char-name">CANDELA</h3>
            <img src="assets/candela.png" alt="Candela" class="char-img">
            <a href="?select=Candela" class="btn-select">SELECT</a>
        </div>

        <div class="char-card card-yellow">
            <h3 class="char-name">SPARK</h3>
            <img src="assets/spark.png" alt="Spark" class="char-img">
            <a href="?select=Spark" class="btn-select">SELECT</a>
        </div>

    </div>

</body>
</html>