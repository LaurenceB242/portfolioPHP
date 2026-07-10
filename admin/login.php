<?php
session_start();

if (isset($_SESSION['admin_connecte']) && $_SESSION['admin_connecte'] === true) {
    header('Location: admin.php');
    exit();
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant']);
    $password = $_POST['mot_de_passe'];

    // Liaison vers ton dossier config officiel
    require_once __DIR__ . '/../config/database.php';

    $req = $pdo->prepare('SELECT * FROM utilisateurs WHERE identifiant = ?');
    $req->execute([$identifiant]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        session_regenerate_id(true);
        $_SESSION['admin_connecte'] = true;
        $_SESSION['admin_user'] = $user['identifiant'];
        header('Location: admin.php');
        exit();
    } else {
        if (!$user) {
            $erreur = "Identifiant inconnu.";
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../build/css/style.css">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #130b15;
            font-family: sans-serif;
        }
        .login-form {
            background-color: #2d2030;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 360px;
            box-sizing: border-box;
        }
        .login-form h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 22px;
            letter-spacing: 1px;
            font-weight: 600;
            color: white;
        }
        input, button {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: none;
            box-sizing: border-box;
            font-size: 14px;
        }
        input { background-color: #1a121c; color: white; }
        button { background-color: #a855f7; color: white; font-weight: bold; cursor: pointer; transition: background 0.2s; }
        button:hover { background-color: #9333ea; }
        .error-msg { color: #ff5555; background: rgba(255, 85, 85, 0.1); border: 1px solid #ff5555; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
<div class="login-container">
    <form class="login-form" action="login.php" method="POST">
        <h2 style="text-align: center; margin-top: 0; font-weight: 300; color: white;">ADMINISTRATION</h2>

        <?php if($erreur): ?>
            <div class="error-msg"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <input type="text" name="identifiant" placeholder="Identifiant" required>
        <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>
</body>
</html>