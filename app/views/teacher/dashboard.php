<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant</title>
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
        }

        .recent-section h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .quiz-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .quiz-item:last-child {
            border-bottom: none;
        }

        .quiz-title {
            font-weight: bold;
            color: #333;
        }

        .quiz-meta {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status.actif {
            background: #dcfce7;
            color: #166534;
        }

        .status.inactif {
            background: #fee2e2;
            color: #991b1b;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard Enseignant</h1>
            <div class="nav-links">
                <a href="/teacher/categories">Mes Catégories</a>
                <a href="/teacher/quizzes">Mes Quiz</a>
                <a href="/teacher/results">Résultats</a>
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
                <h3>Total Quiz</h3>
                <div class="number"><?= $totalQuizzes ?></div>
            </div>
            <div class="stat-card">
                <h3>Quiz Actifs</h3>
                <div class="number"><?= $activeQuizzes ?></div>
            </div>
            <div class="stat-card">
                <h3>Catégories</h3>
                <div class="number"><?= $totalCategories ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Tentatives</h3>
                <div class="number"><?= $totalAttempts ?></div>
            </div>
        </div>

        <div class="recent-section">
            <h2>Quiz Récents</h2>
            <?php if (empty($recentQuizzes)): ?>
                <p style="color: #666; padding: 20px 0;">Aucun quiz créé pour le moment.</p>
                <a href="/teacher/quizzes/create"
                    style="display: inline-block; background: #2563eb; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none;">Créer
                    mon premier quiz</a>
            <?php else: ?>
                <?php foreach ($recentQuizzes as $quiz): ?>
                    <div class="quiz-item">
                        <div>
                            <div class="quiz-title"><?= htmlspecialchars($quiz->title) ?></div>
                            <div class="quiz-meta">
                                Créé le <?= date('d/m/Y', strtotime($quiz->created_at)) ?>
                                <?php if ($quiz->duration): ?>
                                    • Durée: <?= $quiz->duration ?> min
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="status <?= $quiz->status ?>">
                            <?= $quiz->status === 'actif' ? 'Actif' : 'Inactif' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>