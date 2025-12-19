<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Catégories</h1>
                <p class="text-sm text-slate-600">Choisissez une catégorie pour voir les quiz actifs.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/student/quizzes" class="text-sm text-blue-600 hover:underline">Quiz actifs</a>
                <a href="/student/results" class="text-sm text-blue-600 hover:underline">Mes résultats</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">Déconnexion</button>
                </form>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($categories as $cat): ?>
                <a href="/student/categories/quizzes?id=<?= (int)$cat['id'] ?>"
                   class="group block rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-slate-900 group-hover:text-blue-700">
                                <?= htmlspecialchars($cat['name']) ?>
                            </h2>
                            <?php if (!empty($cat['description'])): ?>
                                <p class="mt-1 text-sm text-slate-600 line-clamp-3">
                                    <?= htmlspecialchars($cat['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <span class="shrink-0 inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                            <?= (int)$cat['active_quiz_count'] ?> quiz
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($categories)): ?>
            <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-700">
                Aucune catégorie avec quiz actif pour le moment.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
