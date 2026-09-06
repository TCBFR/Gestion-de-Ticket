<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/function.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']    ?? '');
    $mdp   = trim($_POST['password'] ?? '');

    if (empty($email) || empty($mdp)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nom, email, mot_de_passe, role FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $u = $stmt->fetch();

        if ($u && password_verify($mdp, $u['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $u['id'];
            $_SESSION['user_name'] = $u['nom'];
            $_SESSION['user_role'] = $u['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}

// ── Mot de passe unique pour tous les comptes démo ───────────────────────────
$mdpParDefaut = 'role123';

// ── Chargement dynamique depuis la BDD ───────────────────────────────────────
$comptes = $pdo->query('
    SELECT nom, email, role
    FROM utilisateurs
    ORDER BY role, nom
')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .login-wrapper { max-width: 620px; }
        .login-box     { padding: 48px 52px; }
    </style>
</head>
<body class="login-page">

<div class="login-wrapper">

    <div class="login-box">
        <div class="logo">🎫</div>
        <h1>Connexion</h1>
        <p class="sous-titre">Accédez à votre espace de gestion</p>

        <?php if ($erreur): ?>
            <div class="error"><?= h($erreur) ?></div>
        <?php endif; ?>

        <form method="post" id="loginForm">
            <div class="champ">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus
                       placeholder="votre@email.com"
                       value="<?= h($_POST['email'] ?? '') ?>">
            </div>
            <div class="champ">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required
                       placeholder="••••••••">
            </div>
            <button type="submit">Se connecter →</button>
        </form>
    </div>

    <div class="comptes-demo">
        <h3>🔑 Comptes de démo — cliquez pour remplir</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Mot de passe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comptes as $c): ?>
                        <tr class="demo-row"
                            onclick="fillLogin(<?= htmlspecialchars(json_encode($c['email']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($mdpParDefaut), ENT_QUOTES) ?>)"
                            title="Cliquer pour remplir le formulaire">
                            <td><?= h($c['email']) ?></td>
                            <td><?= h($c['role']) ?></td>
                            <td><code><?= h($mdpParDefaut) ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    function fillLogin(email, password) {
        document.getElementById('email').value    = email;
        document.getElementById('password').value = password;
    }
</script>

</body>
</html>