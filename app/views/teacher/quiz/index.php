<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
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
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
        }

        .btn-primary {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
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
            margin-bottom: 24px;
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

        th:last-child {
            text-align: right;
        }

        tbody tr {
            border-top: 1px solid #e2e8f0;
        }

        td {
            padding: 12px 16px;
            font-size: 14px;
        }

        td:first-child {
            color: #1e293b;
        }

        td:last-child {
            text-align: right;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-active {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .link {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
        }

        .link:hover {
            text-decoration: underline;
        }

        .link-danger {
            color: #dc2626;
        }

        .btn-delete {
            background: none;
            border: none;
            padding: 0;
            font-size: 14px;
            color: #dc2626;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }

        .footer-links {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .footer-links button {
            background: none;
            border: none;
            padding: 0;
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
        }

        .footer-links button:hover {
            color: #1e293b;
        }

        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            .actions {
                flex-direction: column;
                align-items: flex-end;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Mes quiz</h1>
            <a href="/teacher/quizzes/create" class="btn-primary">Créer un quiz</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Status</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz->title) ?></td>
                            <td>
                                <span class="badge <?= $quiz->status === 'actif' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= htmlspecialchars($quiz->status) ?>
                                </span>
                            </td>
                            <td style="color: #64748b;"><?= htmlspecialchars($quiz->duration) ?> min</td>
                            <td>
                                <div class="actions">
                                    <a class="link" href="/teacher/quizzes/edit?id=<?= $quiz->id ?>">Modifier</a>
                                    <a class="link" href="/teacher/quizzes/results?quiz_id=<?= $quiz->id ?>">Résultats</a>
                                    <form method="POST" action="/teacher/quizzes/delete" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                                        <input type="hidden" name="id" value="<?= $quiz->id ?>">
                                        <button type="submit" class="btn-delete"
                                            onclick="return confirm('Supprimer ?')">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-links">
            <a href="/teacher/categories" class="link">Gérer les catégories</a>
            <form method="POST" action="/logout" style="display: inline;">
                <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                <button type="submit">Déconnexion</button>
            </form>
        </div>
    </div>
</body>

</html>