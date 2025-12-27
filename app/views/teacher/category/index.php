<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
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

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
            border: none;
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
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
            font-weight: 500;
            color: #1e293b;
        }

        td:nth-child(2) {
            color: #64748b;
        }

        td:last-child {
            text-align: right;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Catégories</h1>
                <p>Gérez vos catégories.</p>
            </div>
            <div class="header-actions">
                <a href="/teacher/quizzes" class="link">Mes quiz</a>
                <a href="/teacher/categories/create" class="btn btn-primary">Nouvelle catégorie</a>
                <form method="POST" action="/logout" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="link link-danger"
                        style="border: none; background: none; cursor: pointer; padding: 0;">Déconnexion</button>
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

        <?php if (empty($categories)): ?>
            <div class="empty-state">
                Aucune catégorie.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= htmlspecialchars($cat->name) ?></td>
                                <td><?= htmlspecialchars($cat->description ?? '') ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/teacher/categories/edit?id=<?= (int) $cat->id ?>" class="link">Modifier</a>
                                        <form method="POST" action="/teacher/categories/delete" style="display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                                            <input type="hidden" name="id" value="<?= (int) $cat->id ?>">
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
        <?php endif; ?>
    </div>
</body>

</html>