<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, sans-serif;
        }

        body {
            margin: 0;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 16px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Header */
        .header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 16px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .subtitle {
            font-size: 14px;
            color: #475569;
        }

        .nav-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .link-btn {
            padding: 8px 12px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
        }

        .link-btn:hover {
            background: #f1f5f9;
        }

        .btn-danger {
            background: #dc2626;
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #14532d;
        }

        /* Stats cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            font-size: 14px;
            color: #475569;
        }

        .stat-value {
            margin-top: 8px;
            font-size: 30px;
            font-weight: 600;
        }

        /* Recent quizzes */
        .panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .panel-header a {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .panel-header a:hover {
            text-decoration: underline;
        }

        .panel-body {
            display: flex;
            flex-direction: column;
        }

        .quiz-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
        }

        .quiz-item:first-child {
            border-top: none;
        }

        .quiz-title {
            font-weight: 600;
        }

        .quiz-meta {
            font-size: 14px;
            color: #475569;
            margin-top: 4px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            align-self: flex-start;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background: #f1f5f9;
            color: #334155;
        }

        @media (max-width: 640px) {
            .quiz-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div>
                <h1>Dashboard Enseignant</h1>
                <div class="subtitle">Gestion des catégories, quiz et résultats.</div>
            </div>

            <div class="nav-actions">
                <a href="/teacher/categories" class="link-btn">Catégories</a>
                <a href="/teacher/quizzes" class="link-btn">Quiz</a>
                <a href="/teacher/quizzes/results" class="link-btn">Résultats</a>

                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="btn-danger">Déconnexion</button>
                </form>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total quiz</div>
                <div class="stat-value"><?= (int) $totalQuizzes ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Quiz actifs</div>
                <div class="stat-value"><?= (int) $activeQuizzes ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Catégories</div>
                <div class="stat-value"><?= (int) $totalCategories ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Tentatives</div>
                <div class="stat-value"><?= (int) $totalAttempts ?></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <strong>Quiz récents</strong>
                <a href="/teacher/quizzes/create">Créer un quiz</a>
            </div>

            <?php if (empty($recentQuizzes)): ?>
                <div class="panel-body">
                    <div class="quiz-item">
                        <div class="quiz-meta">Aucun quiz créé pour le moment.</div>
                    </div>
                </div>
            <?php else: ?>
                <div class="panel-body">
                    <?php foreach ($recentQuizzes as $quiz): ?>
                        <div class="quiz-item">
                            <div>
                                <div class="quiz-title"><?= htmlspecialchars($quiz->title) ?></div>
                                <div class="quiz-meta">
                                    Créé le <?= date('d/m/Y', strtotime($quiz->created_at)) ?>
                                    <?php if ($quiz->duration): ?>
                                        • Durée <?= (int) $quiz->duration ?> min
                                    <?php endif; ?>
                                </div>
                            </div>

                            <span class="badge <?= $quiz->status === 'actif' ? 'badge-active' : 'badge-inactive' ?>">
                                <?= htmlspecialchars($quiz->status) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>