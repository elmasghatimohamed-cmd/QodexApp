<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz — <?= htmlspecialchars($category->name) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">
                    <?= htmlspecialchars($category->name) ?>
                </h1>
                <p class="text-sm text-slate-600">Quiz actifs disponibles dans cette catégorie.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/student/categories" class="text-sm text-blue-600 hover:underline">Retour catégories</a>
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

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Titre</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Durée</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-slate-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900"><?= htmlspecialchars($quiz->title) ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?= htmlspecialchars($quiz->duration) ?> min</td>
                            <td class="px-4 py-3 text-right">
                                <a href="/student/quiz/take?id=<?= (int) $quiz->id ?>"
                                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-700">
                                    Passer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($quizzes)): ?>
            <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-700">
                Aucun quiz actif dans cette catégorie.
            </div>
        <?php endif; ?>
    </div>
</body>

</html>