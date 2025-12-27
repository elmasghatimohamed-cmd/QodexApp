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

        .header-nav {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
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

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }

        .category-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
            display: block;
        }

        .category-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .category-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .category-card:hover .category-title {
            color: #2563eb;
        }

        .category-description {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .category-badge {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .empty-state {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            font-size: 14px;
            color: #334155;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1>Catégories</h1>
                <p>Choisissez une catégorie pour voir les quiz actifs.</p>
            </div>
            <div class="header-nav">
                <a href="/student/quizzes">Quiz actifs</a>
                <a href="/student/results">Mes résultats</a>
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

        <?php if (empty($categories)): ?>
            <div class="empty-state">
                Aucune catégorie avec quiz actif pour le moment.
            </div>
        <?php else: ?>
            <div class="categories-grid">
                <?php foreach ($categories as $cat): ?>
                    <a href="/student/categories/quizzes?id=<?= (int) $cat['id'] ?>" class="category-card">
                        <div class="category-header">
                            <div style="flex: 1;">
                                <div class="category-title">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </div>
                                <?php if (!empty($cat['description'])): ?>
                                    <p class="category-description">
                                        <?= htmlspecialchars($cat['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <span class="category-badge">
                                <?= (int) $cat['active_quiz_count'] ?> quiz
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>