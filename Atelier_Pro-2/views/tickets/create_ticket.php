<?php
// public/create_ticket.php — rôle : secrétaire

require_once '../config/config.php';
require_once '../config/function.php';

requireRole('secretaire');

$userName = $_SESSION['user_name'];
$erreur   = '';
$succes   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre    = trim($_POST['titre']        ?? '');
    $corps    = trim($_POST['corps']        ?? '');
    $adresse  = trim($_POST['adresse_lieu'] ?? '');
    $priorite = $_POST['priorite']          ?? 'medium';
    $catId    = $_POST['categorie_id']      ?? '';

    if (empty($titre) || empty($corps) || empty($adresse)) {
        $erreur = 'Le titre, la description et l\'adresse sont obligatoires.';
    } elseif (!in_array($priorite, ['low', 'medium', 'hard'], true)) {
        $erreur = 'Priorité invalide.';
    } elseif (empty($catId) || !ctype_digit((string)$catId)) {
        $erreur = 'Veuillez choisir une catégorie.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (titre, corps, adresse_lieu, priorite, categorie_id, statut)
            VALUES (?, ?, ?, ?, ?, 'en attente')
        ");
        $stmt->execute([$titre, $corps, $adresse, $priorite, (int)$catId]);
        $succes = 'Ticket « ' . h($titre) . ' » créé avec succès.';
        $_POST  = [];
    }
}

$categories = $pdo->query('SELECT id, nom FROM categories ORDER BY nom')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un ticket</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>

<div class="page-header">
    <h1>🎫 Créer un ticket</h1>
    <div class="user-info">
        <?= h($userName) ?> <span class="badge">secrétaire</span>
        &nbsp;|&nbsp; <a href="dashboard.php">← Dashboard</a>
    </div>
</div>

<?php if ($erreur): ?>
    <div class="error"><?= h($erreur) ?></div>
<?php endif; ?>

<?php if ($succes): ?>
    <div class="success"><?= $succes ?></div>
<?php endif; ?>

<div class="form-card">
    <form method="post">

        <div class="champ">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" required
                   placeholder="Résumé court du problème"
                   value="<?= h($_POST['titre'] ?? '') ?>">
        </div>

        <div class="champ">
            <label for="corps">Description *</label>
            <textarea id="corps" name="corps" rows="5" required
                      placeholder="Décrivez le problème en détail…"><?= h($_POST['corps'] ?? '') ?></textarea>
        </div>

        <div class="champ">
            <label for="adresse_lieu">Adresse / lieu *</label>
            <input type="text" id="adresse_lieu" name="adresse_lieu" required
                   placeholder="Ex : 12 rue de la Paix, Bâtiment B"
                   value="<?= h($_POST['adresse_lieu'] ?? '') ?>">
        </div>

        <div class="form-row">
            <div class="champ">
                <label for="priorite">Priorité</label>
                <select id="priorite" name="priorite">
                    <option value="low"    <?= ($_POST['priorite'] ?? '') === 'low'          ? 'selected' : '' ?>>🟢 Basse</option>
                    <option value="medium" <?= ($_POST['priorite'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>🟡 Moyenne</option>
                    <option value="hard"   <?= ($_POST['priorite'] ?? '') === 'hard'         ? 'selected' : '' ?>>🔴 Haute</option>
                </select>
            </div>

            <div class="champ">
                <label for="categorie_id">Catégorie *</label>
                <select id="categorie_id" name="categorie_id" required>
                    <option value="">— Choisir —</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= h($cat['id']) ?>"
                            <?= ($_POST['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= h($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit">✅ Créer le ticket</button>
            <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
        </div>

    </form>
</div>

</body>
</html>