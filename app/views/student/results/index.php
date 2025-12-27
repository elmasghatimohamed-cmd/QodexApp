<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes résultats</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .header-title p {
            font-size: 14px;
            color: #64748b;
        }

        .header-nav {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .header-nav a,
        .header-nav button {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }

        .header-nav a:hover,
        .header-nav button:hover {
            text-decoration: underline;
        }

        .header-nav button {
            color: #64748b;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .table-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f8f9fa;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        tbody tr {
            border-top: 1px solid #e2e8f0;
        }

        td {
            padding: 12px 16px;
            font-size: 14px;
            color: #334155;
        }

        td:first-child {
            color: #1e293b;
        }

        .empty-state {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #334155;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        .pagination a {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
        }

        .pagination a:hover {
            text-decoration: underline;
        }

        .pagination a.disabled {
            pointer-events: none;
            opacity: 0.4;
        }

        .pagination-info {
            font-size: 14px;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 700px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Mes résultats</h1>
                <p>Historique de vos scores.</p>
            </div>
            <div class="header-nav">
                <a href="/student/categories">Catégories</a>
                <a href="/student/quizzes">Quiz actifs</a>
                <form method="POST" action="/logout" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($attempts)): ?>
            <div class="empty-state">
                Aucun résultat pour le moment.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>%</th>
                            <th>Terminé</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attempts as $attempt): ?>
                            <tr>
                                <td><?= htmlspecialchars($attempt['quiz_title']) ?></td>
                                <td>
                                    <?= htmlspecialchars($attempt['score']) ?>/<?= htmlspecialchars($attempt['total_points']) ?>
                                </td>
                                <td><?= round((float) $attempt['pourcentage'], 2) ?>%</td>
                                <td><?= htmlspecialchars($attempt['completed_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (($totalPages ?? 1) > 1): ?>
                <div class="pagination">
                    <a href="/student/results?page=<?= max(1, (int) $page - 1) ?>"
                        class="<?= ($page <= 1) ? 'disabled' : '' ?>">Précédent</a>

                    <div class="pagination-info">Page <?= (int) $page ?> / <?= (int) $totalPages ?></div>

                    <a href="/student/results?page=<?= min((int) $totalPages, (int) $page + 1) ?>"
                        class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">Suivant</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>