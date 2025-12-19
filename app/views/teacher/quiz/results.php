<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Résultats</h1>
                <p class="text-sm text-slate-600">Tentatives sur vos quiz (sans données sensibles).</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/teacher/quizzes" class="text-sm text-blue-600 hover:underline">Retour aux quiz</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">Déconnexion</button>
                </form>
            </div>
        </div>

        <form method="GET" action="/teacher/quizzes/results" class="mb-4 flex flex-col sm:flex-row gap-3">
            <select name="quiz_id" class="w-full sm:w-96 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                <option value="">Tous les quiz</option>
                <?php foreach ($teacherQuizzes as $q): ?>
                    <option value="<?= (int)$q->id ?>" <?= ((int)$quizId === (int)$q->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($q->title) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Filtrer
            </button>
        </form>

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
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Quiz</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Étudiant</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Score</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">%</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Terminé</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($attempts as $attempt): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900">
                                <?= htmlspecialchars($attempt['quiz_title']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <div class="font-medium text-slate-900">
                                    <?= htmlspecialchars($attempt['student_first_name'] . ' ' . $attempt['student_last_name']) ?>
                                </div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($attempt['student_email']) ?></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">
                                <?= htmlspecialchars($attempt['score']) ?>/<?= htmlspecialchars($attempt['total_points']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?= round((float)$attempt['pourcentage'], 2) ?>%</td>
                            <td class="px-4 py-3 text-sm text-slate-700"><?= htmlspecialchars($attempt['completed_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($attempts)): ?>
            <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-700">
                Aucun résultat pour le moment.
            </div>
        <?php endif; ?>

        <?php if (($totalPages ?? 1) > 1): ?>
            <?php $quizIdParam = !empty($quizId) ? ('&quiz_id=' . (int)$quizId) : ''; ?>
            <div class="mt-6 flex items-center justify-between">
                <a
                    class="text-sm text-blue-600 hover:underline <?= ($page <= 1) ? 'pointer-events-none opacity-40' : '' ?>"
                    href="/teacher/quizzes/results?page=<?= max(1, (int)$page - 1) ?><?= $quizIdParam ?>">Précédent</a>

                <div class="text-sm text-slate-600">Page <?= (int)$page ?> / <?= (int)$totalPages ?></div>

                <a
                    class="text-sm text-blue-600 hover:underline <?= ($page >= $totalPages) ? 'pointer-events-none opacity-40' : '' ?>"
                    href="/teacher/quizzes/results?page=<?= min((int)$totalPages, (int)$page + 1) ?><?= $quizIdParam ?>">Suivant</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
