<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Mes quiz</h1>
            <a href="/teacher/quizzes/create"
                class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Créer un
                quiz</a>
        </div>
        <?php if (!empty($error)): ?>
            <div class="mb-3 rounded bg-red-50 text-red-700 px-3 py-2 text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="mb-3 rounded bg-green-50 text-green-700 px-3 py-2 text-sm"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="overflow-hidden rounded-lg shadow bg-white">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Titre</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Durée</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($quiz->title) ?></td>
                            <td class="px-4 py-2 text-sm">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold <?= $quiz->status === 'actif' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' ?>">
                                    <?= htmlspecialchars($quiz->status) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600"><?= htmlspecialchars($quiz->duration) ?> min</td>
                            <td class="px-4 py-2 text-sm text-right space-x-3">
                                <a class="text-blue-600 hover:underline"
                                    href="/teacher/quizzes/edit?id=<?= $quiz->id ?>">Modifier</a>
                                <a class="text-indigo-600 hover:underline"
                                    href="/teacher/quizzes/results?quiz_id=<?= $quiz->id ?>">Résultats</a>
                                <form method="POST" action="/teacher/quizzes/delete" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                                    <input type="hidden" name="id" value="<?= $quiz->id ?>">
                                    <button type="submit" class="text-red-600 hover:underline"
                                        onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <a href="/teacher/categories" class="text-sm text-blue-600 hover:underline">Gérer les catégories</a>
            <form method="POST" action="/logout">
                <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">Déconnexion</button>
            </form>
        </div>
    </div>
</body>

</html>