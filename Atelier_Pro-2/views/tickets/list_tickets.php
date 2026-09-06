<?php
// public/list_tickets.php — rôle : secrétaire

require_once '../config/config.php';
require_once '../config/function.php';

requireRole('secretaire');

$userName = $_SESSION['user_name'];

// ── Suppression ───────────────────────────────────────────────────────────────
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM tickets WHERE id = ?');
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: index.php?action=list_tickets');
    exit;
}

// ── Liste des tickets (agents municipaux assignés uniquement) ─────────────────
$tickets = $pdo->query("
    SELECT t.id, t.titre, t.corps, t.adresse_lieu, t.priorite, t.statut,
           t.date_creation, u.nom AS assign_nom
    FROM tickets t
    JOIN categories c   ON t.categorie_id = c.id
    LEFT JOIN utilisateurs u ON t.assign_to = u.id
    WHERE c.nom = 'Agent municipal'
      AND (u.role = 'agent municipal' OR u.role IS NULL)
    ORDER BY t.date_creation DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets agents municipaux</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* ─── Page header ───────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1a1a2e;
        }

        .page-header nav {
            font-size: 0.9rem;
        }

        /* ─── Tableau ───────────────────────────────────── */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #e2e5ea;
            margin-top: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 11px 14px;
            text-align: left;
            border-bottom: 1px solid #e9ecf0;
            font-size: 0.875rem;
        }

        th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f5f8ff; }

        /* ─── Badge statut ──────────────────────────────── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #e0e7ff;
            color: #3730a3;
            white-space: nowrap;
        }

        /* ─── Actions ───────────────────────────────────── */
        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .actions a {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 500;
            text-decoration: none;
            background: #6b7280;
            color: #fff;
            transition: background 0.15s;
        }

        .actions a:hover { background: #4b5563; }

        .actions a.delete { background: #dc2626; }
        .actions a.delete:hover { background: #b91c1c; }

        /* ─── Message vide ──────────────────────────────── */
        .empty {
            color: #9ca3af;
            font-style: italic;
            margin-top: 16px;
        }
    </style>
</head>
<body>

<div class="page-header">
    <h1>🗂️ Tickets agents municipaux</h1>
    <nav>
        <a href="index.php?action=dashboard">← Dashboard</a>
    </nav>
</div>

<?php if (empty($tickets)): ?>
    <p class="empty">Aucun ticket assigné à un agent municipal pour le moment.</p>
<?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Adresse</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Assigné à</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tickets as $t): ?>
                <tr>
                    <td><?= h($t['titre']) ?></td>
                    <td><?= h(tronquer($t['corps'])) ?></td>
                    <td><?= h($t['adresse_lieu']) ?></td>
                    <td><?= h(labelPriorite($t['priorite'])) ?></td>
                    <td><span class="badge"><?= h(labelStatut($t['statut'])) ?></span></td>
                    <td><?= h($t['assign_nom']) ?></td>
                    <td><?= h($t['date_creation']) ?></td>
                    <td class="actions">
                        <a href="index.php?action=edit_ticket&id=<?= h($t['id']) ?>">Modifier</a>
                        <a href="index.php?action=list_tickets&delete=<?= h($t['id']) ?>"
                           class="delete"
                           onclick="return confirm('Supprimer ce ticket ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</body>
</html>