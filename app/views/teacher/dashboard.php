<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard Enseignant</h1>
                <p class="text-sm text-slate-600">Gestion des catégories, quiz et résultats.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="/teacher/categories"
                    class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Catégories</a>
                <a href="/teacher/quizzes"
                    class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Quiz</a>
                <a href="/teacher/quizzes/results"
                    class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Résultats</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Déconnexion</button>
                </form>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Total quiz</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int) $totalQuizzes ?></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Quiz actifs</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int) $activeQuizzes ?></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Catégories</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int) $totalCategories ?></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Tentatives</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int) $totalAttempts ?></div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">Quiz récents</h2>
                <a href="/teacher/quizzes/create" class="text-sm font-medium text-blue-600 hover:underline">Créer un
                    quiz</a>
            </div>

            <?php if (empty($recentQuizzes)): ?>
                <div class="p-5 text-sm text-slate-700">Aucun quiz créé pour le moment.</div>
            <?php else: ?>
                <div class="divide-y divide-slate-200">
                    <?php foreach ($recentQuizzes as $quiz): ?>
                        <div class="flex items-start justify-between gap-4 p-5">
                            <div>
                                <div class="font-semibold text-slate-900"><?= htmlspecialchars($quiz->title) ?></div>
                                <div class="mt-1 text-sm text-slate-600">
                                    Créé le <?= htmlspecialchars(date('d/m/Y', strtotime($quiz->created_at))) ?>
                                    <?php if ($quiz->duration): ?>
                                        • Durée: <?= (int) $quiz->duration ?> min
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?= $quiz->status === 'actif' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-700' ?>">
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