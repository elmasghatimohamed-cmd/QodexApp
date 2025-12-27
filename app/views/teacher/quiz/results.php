<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats</title>
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
            margin-bottom: 16px;
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

        .filter-form {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .filter-form select {
            flex: 1;
            max-width: 400px;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
        }

        .btn-primary {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
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

        .student-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .student-name {
            font-weight: 500;
            color: #1e293b;
        }

        .student-email {
            font-size: 12px;
            color: #64748b;
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
                min-width: 800px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Résultats</h1>
                <p>Tentatives sur vos quiz (sans données sensibles).</p>
            </div>
            <div class="header-nav">
                <a href="/teacher/quizzes">Retour aux quiz</a>
                <form method="POST" action="/logout" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
        </div>

        <form method="GET" action="/teacher/quizzes" class="filter-form">
            <select name="quiz_id">
                <option value="">Tous les quiz</option>
                <?php foreach ($teacherQuizzes as $q): ?>
                    <option value="<?= (int) $q->id ?>" <?= ((int) $quizId === (int) $q->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($q->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primary">Filtrer</button>
        </form>

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
                            <th>Étudiant</th>
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
                                    <div class="student-info">
                                        <span class="student-name">
                                            <?= htmlspecialchars($attempt['student_first_name'] . ' ' . $attempt['student_last_name']) ?>
                                        </span>
                                        <span class="student-email"><?= htmlspecialchars($attempt['student_email']) ?></span>
                                    </div>
                                </td>
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
                <?php $quizIdParam = !empty($quizId) ? ('&quiz_id=' . (int) $quizId) : ''; ?>
                <div class="pagination">
                    <a href="/teacher/quizzes/results?page=<?= max(1, (int) $page - 1) ?><?= $quizIdParam ?>"
                        class="<?= ($page <= 1) ? 'disabled' : '' ?>">Précédent</a>

                    <div class="pagination-info">Page <?= (int) $page ?> / <?= (int) $totalPages ?></div>

                    <a href="/teacher/quizzes/results?page=<?= min((int) $totalPages, (int) $page + 1) ?><?= $quizIdParam ?>"
                        class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">Suivant</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>