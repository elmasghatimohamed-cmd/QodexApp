<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz actifs</title>
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
            max-width: 1000px;
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

        .header-nav {
            display: flex;
            gap: 16px;
            align-items: center;
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

        td:nth-child(2) {
            color: #64748b;
        }

        td:last-child {
            text-align: right;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            background-color: #2563eb;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        @media (max-width: 768px) {
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
            <h1>Quiz actifs</h1>
            <div class="header-nav">
                <a href="/student/results">Mes résultats</a>
                <form method="POST" action="/logout" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
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
                        <th>Durée</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz->title) ?></td>
                            <td><?= htmlspecialchars($quiz->duration) ?> min</td>
                            <td>
                                <a href="/student/quiz/take?id=<?= $quiz->id ?>" class="btn-primary">Passer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>