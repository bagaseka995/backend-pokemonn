<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['activate'])) {
    $poke_id = $_GET['activate'];
    mysqli_query($conn, "UPDATE user_pokemon SET is_active = 0 WHERE user_id = '$user_id'");
    mysqli_query($conn, "UPDATE user_pokemon SET is_active = 1 WHERE id = '$poke_id' AND user_id = '$user_id'");
    header("Location: my-team.php");
    exit;
}

if (isset($_GET['release'])) {
    $poke_id = $_GET['release'];
    
    $cek = mysqli_query($conn, "SELECT is_active FROM user_pokemon WHERE id = '$poke_id'");
    $data = mysqli_fetch_assoc($cek);
    
    if ($data['is_active'] == 1) {
        echo "<script>alert('Gabisa lepas Partner Utama! Ganti partner dulu.'); window.location='my-team.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM user_pokemon WHERE id = '$poke_id' AND user_id = '$user_id'");
        header("Location: my-team.php");
    }
    exit;
}

if (isset($_POST['rename_pokemon'])) {
    $poke_id = $_POST['id'];
    $new_nick = mysqli_real_escape_string($conn, $_POST['nickname']);
    mysqli_query($conn, "UPDATE user_pokemon SET nickname = '$new_nick' WHERE id = '$poke_id' AND user_id = '$user_id'");
    header("Location: my-team.php");
    exit;
}

if (isset($_POST['catch_new'])) {
    $q = mysqli_query($conn, "SELECT * FROM master_pokemon ORDER BY RAND() LIMIT 1");
    $res = mysqli_fetch_assoc($q);
    
    $master_id = $res['id'];
    $nick = $res['name'];
    
    mysqli_query($conn, "INSERT INTO user_pokemon (user_id, pokemon_id, nickname) VALUES ('$user_id', '$master_id', '$nick')");
    echo "<script>alert('Dapat " . $nick . "!'); window.location='my-team.php';</script>";
    exit;
}

$queryMyTeam = mysqli_query($conn, "
    SELECT up.*, mp.name as real_name, mp.element_type, mp.image_path 
    FROM user_pokemon up
    JOIN master_pokemon mp ON up.pokemon_id = mp.id
    WHERE up.user_id = '$user_id'
    ORDER BY up.is_active DESC, up.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team Management</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages/my-team.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Modal Style Sederhana */
        #renameModal {
            display: none; position: fixed; z-index: 999; left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;
        }
        .modal-content {
            background-color: #fefefe; padding: 20px; border-radius: 10px; width: 300px; text-align: center;
        }
        .input-nick { padding: 8px; width: 80%; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn-save { background: #ff7675; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

    <header class="dashboard-header">
        <div class="nav-left">
            <img src="assets/logo-pokemon.png" alt="Logo">
        </div>
        <div class="nav-center">
            <a href="my-team.php" style="color: white; text-decoration: none; font-weight: bold;">My Team</a>
        </div>
        <div class="nav-right">
            <i class="far fa-user-circle"></i>
            <span><?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="dashboard.php" class="logout-icon" style="margin-left: 15px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </header>

    <div class="team-page-container">

        <?php while($row = mysqli_fetch_assoc($queryMyTeam)) : ?>
            
            <?php 
                // Variasi Warna Kartu (Ganjil Hijau, Genap Orange)
                $themeClass = ($row['id'] % 2 == 0) ? 'theme-green' : 'theme-orange';
                $isActiveStr = ($row['is_active'] == 1) ? 'true' : 'false';
            ?>

            <div class="pokemon-card <?= $themeClass; ?>" data-active="<?= $isActiveStr; ?>">
                <div class="card-img-col">
                    <img src="assets/<?= $row['image_path']; ?>" alt="Pokemon" class="poke-thumb">
                </div>
                <div class="card-info-col">
                    <h3 class="poke-name"><?= $row['real_name']; ?></h3>
                    <p class="poke-detail">Nickname: <span class="nickname-text"><?= htmlspecialchars($row['nickname']); ?></span></p>
                    <p class="poke-detail">Level: <?= $row['current_level']; ?></p>
                    <p class="poke-detail">Element: <?= $row['element_type']; ?></p>
                    
                    <div class="card-actions">
                        
                        <?php if($row['is_active'] == 0) : ?>
                            <a href="?activate=<?= $row['id']; ?>" class="btn-status btn-orange-fill" style="text-decoration:none; display:inline-block; text-align:center;">
                                Set As Active
                            </a>
                        <?php else : ?>
                            <button class="btn-status btn-orange-fill" style="cursor: default;">ACTIVATED</button>
                        <?php endif; ?>

                        <button class="btn-icon-action btn-edit" onclick="openModal(<?= $row['id']; ?>, '<?= $row['nickname']; ?>')">
                            <i class="fas fa-pen"></i>
                        </button>
                        
                        <?php if($row['is_active'] == 0) : ?>
                            <a href="?release=<?= $row['id']; ?>" class="btn-icon-action btn-delete" onclick="return confirm('Yakin lepas pokemon ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php endwhile; ?>
        <form method="POST" class="card-add-new" style="cursor: pointer;">
            <button type="submit" name="catch_new" style="background:none; border:none; width:100%; height:100%; cursor:pointer; color:inherit;">
                <i class="fas fa-plus icon-plus-large"></i>
                <br>
                <span class="text-catch-new">Catch New Pokemon</span>
            </button>
        </form>

    </div>

    <div id="renameModal">
        <div class="modal-content">
            <h3>Ganti Nama</h3>
            <form method="POST">
                <input type="hidden" name="id" id="modal_id">
                <input type="text" name="nickname" id="modal_nick" class="input-nick" required maxlength="12">
                <br>
                <button type="button" onclick="closeModal()" style="cursor:pointer;">Batal</button>
                <button type="submit" name="rename_pokemon" class="btn-save">Simpan</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('renameModal');
        const inputId = document.getElementById('modal_id');
        const inputNick = document.getElementById('modal_nick');

        function openModal(id, nick) {
            inputId.value = id;
            inputNick.value = nick;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>