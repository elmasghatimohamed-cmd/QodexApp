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
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 36px;
            font-weight: bold;
            color: #2563eb;
        }

        .recent-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .recent-section h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .result-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-title {
            font-weight: bold;
            color: #333;
        }

        .result-meta {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .score {
            font-size: 24px;
            font-weight: bold;
        }

        .score.good {
            color: #16a34a;
        }

        .score.average {
            color: #ea580c;
        }

        .score.poor {
            color: #dc2626;
        }

        .nav-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .nav-links a {
            text-decoration: none;
            color: #2563eb;
            padding: 8px 16px;
            border-radius: 4px;
            background: #eff6ff;
        }

        .nav-links a:hover {
            background: #dbeafe;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .logout {
            background: #dc2626;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }

        .logout:hover {
            background: #b91c1c;
        }

        .cta-button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }

        .cta-button:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard Étudiant</h1>
            <div class="nav-links">
                <a href="/student/quizzes">Quiz Disponibles</a>
                <a href="/student/results">Mes Résultats</a>
                <a href="/logout" class="logout">Déconnexion</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3>Quiz Disponibles</h3>
                <div class="number"><?= $availableQuizzes ?></div>
            </div>
            <div class="stat-card">
                <h3>Quiz Complétés</h3>
                <div class="number"><?= $totalAttempts ?></div>
            </div>
            <div class="stat-card">
                <h3>Moyenne Générale</h3>
                <div class="number"><?= $averageScore ?>%</div>
            </div>
        </div>

        <?php if ($availableQuizzes > 0): ?>
            <div style="text-align: center; margin-bottom: 30px;">
                <a href="/student/quizzes" class="cta-button">Passer un Quiz</a>
            </div>
        <?php endif; ?>

        <div class="recent-section">
            <h2>Résultats Récents</h2>
            <?php if (empty($recentAttempts)): ?>
                <p style="color: #666; padding: 20px 0;">Vous n'avez pas encore passé de quiz.</p>
                <?php if ($availableQuizzes > 0): ?>
                    <a href="/student/quizzes" class="cta-button">Commencer Maintenant</a>
                <?php endif; ?>
            <?php else: ?>
                <?php foreach ($recentAttempts as $attempt): ?>
                    <?php
                    $scoreClass = 'poor';
                    if ($attempt->pourcentage >= 75)
                        $scoreClass = 'good';
                    elseif ($attempt->pourcentage >= 50)
                        $scoreClass = 'average';
                    ?>
                    <div class="result-item">
                        <div>
                            <div class="result-title">Quiz #<?= $attempt->quiz_id ?></div>
                            <div class="result-meta">
                                Complété le <?= date('d/m/Y à H:i', strtotime($attempt->completed_at)) ?>
                                • Temps: <?= $attempt->temps_passe_minutes ?> min
                                • Score: <?= $attempt->score ?>/<?= $attempt->total_points ?>
                            </div>
                        </div>
                        <div class="score <?= $scoreClass ?>">
                            <?= round($attempt->pourcentage, 1) ?>%
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>