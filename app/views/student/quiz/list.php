<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Quiz actifs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Quiz actifs</h1>
            <div class="flex items-center gap-3">
                <a href="/student/results" class="text-sm text-blue-600 hover:underline">Mes résultats</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">Déconnexion</button>
                </form>
            </div>
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
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Durée</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-800"><?= htmlspecialchars($quiz->title) ?></td>
                            <td class="px-4 py-2 text-sm text-gray-600"><?= htmlspecialchars($quiz->duration) ?> min</td>
                            <td class="px-4 py-2 text-sm text-right">
                                <a href="/student/quiz/take?id=<?= $quiz->id ?>"
                                    class="inline-flex items-center rounded bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-700">Passer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>