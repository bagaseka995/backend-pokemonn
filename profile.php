<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = mysqli_real_escape_string($conn, $_POST['password']);
    
    if (!empty($new_password)) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username = '$new_username', password = '$password_hash' WHERE id = '$user_id'";
    } else {
        $sql = "UPDATE users SET username = '$new_username' WHERE id = '$user_id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['username'] = $new_username;
        echo "<script>alert('Profile berhasil diupdate!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Gagal update!');</script>";
    }
}

if (isset($_POST['delete_account'])) {
    mysqli_query($conn, "DELETE FROM user_pokemon WHERE user_id = '$user_id'");
    mysqli_query($conn, "DELETE FROM users WHERE id = '$user_id'");
        session_destroy();
    echo "<script>alert('Akun berhasil dihapus. Selamat tinggal!'); window.location='login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background-image: url('assets/background-pokemon.png'); 
            background-size: cover; background-position: center top; 
            background-repeat: no-repeat; background-attachment: fixed;
            height: 100vh; width: 100%; overflow: hidden;
        }

        .overlay-dim {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 50; backdrop-filter: blur(3px);
        }

        .dashboard-header {
            width: 100%; height: 100px; display: flex; justify-content: space-between;
            align-items: center; padding: 0 40px; position: absolute; top: 0; left: 0; z-index: 40;
        }

        .nav-left img { height: 60px; width: auto; }
        
        .nav-center, .nav-right {
            font-size: 24px; font-weight: 700; color: #FFFFFF;
            text-shadow: 0px 2px 4px rgba(0,0,0,0.5);
        }
        
        .nav-right { display: flex; align-items: center; gap: 15px; }

        .profile-card {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            z-index: 100; width: 500px; background: rgba(255, 255, 255, 0.95);
            border-radius: 20px; padding: 40px; text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .profile-card h2 {
            font-size: 28px; font-weight: 700; color: #000; margin-bottom: 20px;
            text-shadow: 1px 1px 0px #FFF, -1px -1px 0 #FFF;
        }

        .profile-pic-container {
            width: 150px; height: 150px; background-color: #E0E0E0;
            border-radius: 50%; margin: 0 auto 30px auto; display: flex;
            justify-content: center; align-items: center;
            border: 3px solid #2196F3; overflow: hidden;
        }
        
        /* CSS Tambahan: Biar gambar avatar pas */
        .profile-pic-container img {
            width: 100%; height: 100%; object-fit: cover;
        }

        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 5px; }
        .form-group i { margin-right: 5px; }
        
        .form-group input {
            width: 100%; padding: 12px 15px; border-radius: 10px;
            border: 1px solid #ccc; background-color: #FFF; font-size: 14px;
            outline: none; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }

        .action-buttons { display: flex; justify-content: space-between; gap: 15px; margin-top: 30px; }

        .btn-profile {
            flex: 1; padding: 12px; border: none; border-radius: 25px;
            font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 14px;
            color: #000; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: transform 0.2s;
            background: linear-gradient(to bottom, #FFD700, #FFAA00);
            border: 2px solid #FFF;
        }

        .btn-profile:hover { transform: scale(1.05); filter: brightness(1.1); }
        .btn-delete { background: linear-gradient(to bottom, #FF4B2B, #FF416C); color: white; }

        .close-btn {
            position: absolute; top: 15px; right: 20px; font-size: 24px;
            color: #999; text-decoration: none; cursor: pointer;
        }
        .close-btn:hover { color: #333; }
    </style>
</head>
<body>

    <header class="dashboard-header">
        <div class="nav-left">
            <img src="assets/logo-pokemon.png" alt="Logo">
        </div>
        <div class="nav-center">
            <a href="my-team.php" style="color:white; text-decoration:none;">My Team</a>
        </div>
        <div class="nav-right">
            <i class="far fa-user-circle"></i>
            <span><?= htmlspecialchars($user['username']); ?></span> <a href="logout.php" style="color:white;"><i class="fas fa-sign-out-alt" style="margin-left: 15px;"></i></a>
        </div>
    </header>

    <div class="overlay-dim"></div>

    <div class="profile-card">
        <a href="dashboard.php" class="close-btn">&times;</a>

        <h2>Edit Profile</h2>

        <div class="profile-pic-container">
            <img src="<?= $user['avatar']; ?>" alt="Profile Picture" onerror="this.src='assets/default.png'">
        </div>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-key"></i> New Password</label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengganti">
            </div>

            <div class="action-buttons">
                <button type="submit" name="update_profile" class="btn-profile">Save Changes</button>
                
                <button type="submit" name="delete_account" class="btn-profile btn-delete" onclick="return confirm('Yakin ingin MENGHAPUS akun? Semua data akan hilang selamanya!')">
                    Delete Account
                </button>
            </div>
        </form>
    </div>

</body>
</html>