<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant</title>
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
        }

        .btn {
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background-color: white;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn:hover {
            background-color: #f1f5f9;
        }

        .btn-danger {
            background-color: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 24px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: #1e293b;
        }

        .stat-link {
            margin-top: 16px;
        }

        .stat-link a {
            font-size: 14px;
            font-weight: 500;
            color: #2563eb;
            text-decoration: none;
        }

        .stat-link a:hover {
            text-decoration: underline;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header h2 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }

        .card-header a {
            font-size: 14px;
            font-weight: 500;
            color: #2563eb;
            text-decoration: none;
        }

        .card-header a:hover {
            text-decoration: underline;
        }

        .card-body {
            padding: 20px;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-info h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .result-meta {
            font-size: 14px;
            color: #64748b;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .header-actions {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .result-item {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Dashboard Étudiant</h1>
                <p>Quiz disponibles et historique des résultats.</p>
            </div>
            <div class="header-actions">
                <a href="/student/categories" class="btn">Catégories</a>
                <a href="/student/quizzes" class="btn">Quiz actifs</a>
                <a href="/student/results" class="btn">Mes résultats</a>
                <form method="POST" action="/logout" style="display: inline;">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="btn btn-danger">Déconnexion</button>
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Quiz disponibles</div>
                <div class="stat-value"><?= (int) $availableQuizzes ?></div>
                <div class="stat-link">
                    <a href="/student/quizzes">Voir les quiz</a>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Quiz complétés</div>
                <div class="stat-value"><?= (int) $totalAttempts ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Moyenne générale</div>
                <div class="stat-value"><?= htmlspecialchars((string) $averageScore) ?>%</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Résultats récents</h2>
                <a href="/student/results">Voir tout</a>
            </div>

            <?php if (empty($recentAttempts)): ?>
                <div class="card-body">
                    Vous n'avez pas encore passé de quiz.
                    <?php if ((int) $availableQuizzes > 0): ?>
                        <div style="margin-top: 8px;">
                            <a href="/student/quizzes"
                                style="font-size: 14px; font-weight: 500; color: #2563eb; text-decoration: none;">Commencer
                                maintenant</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($recentAttempts as $attempt): ?>
                    <?php
                    $p = (float) ($attempt['pourcentage'] ?? 0);
                    $badgeClass = 'badge-danger';
                    if ($p >= 75) {
                        $badgeClass = 'badge-success';
                    } elseif ($p >= 50) {
                        $badgeClass = 'badge-warning';
                    }
                    ?>
                    <div class="result-item">
                        <div class="result-info">
                            <h3><?= htmlspecialchars($attempt['quiz_title']) ?></h3>
                            <div class="result-meta">
                                Terminé le <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($attempt['completed_at']))) ?>
                                • Temps: <?= (int) $attempt['temps_passe_minutes'] ?> min
                                • Score:
                                <?= htmlspecialchars($attempt['score']) ?>/<?= htmlspecialchars($attempt['total_points']) ?>
                            </div>
                        </div>
                        <span class="badge <?= $badgeClass ?>">
                            <?= round($p, 1) ?>%
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>