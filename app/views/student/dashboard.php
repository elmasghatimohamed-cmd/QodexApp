<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-4 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Dashboard Étudiant</h1>
                <p class="text-sm text-slate-600">Quiz disponibles et historique des résultats.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="/student/categories" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Catégories</a>
                <a href="/student/quizzes" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Quiz actifs</a>
                <a href="/student/results" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-700 border border-slate-200 hover:bg-slate-50">Mes résultats</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Déconnexion</button>
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Quiz disponibles</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int)$availableQuizzes ?></div>
                <div class="mt-4">
                    <a href="/student/quizzes" class="text-sm font-medium text-blue-600 hover:underline">Voir les quiz</a>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Quiz complétés</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= (int)$totalAttempts ?></div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm text-slate-600">Moyenne générale</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900"><?= htmlspecialchars((string)$averageScore) ?>%</div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">Résultats récents</h2>
                <a href="/student/results" class="text-sm font-medium text-blue-600 hover:underline">Voir tout</a>
            </div>

            <?php if (empty($recentAttempts)): ?>
                <div class="p-5 text-sm text-slate-700">
                    Vous n'avez pas encore passé de quiz.
                    <?php if ((int)$availableQuizzes > 0): ?>
                        <div class="mt-2">
                            <a href="/student/quizzes" class="text-sm font-medium text-blue-600 hover:underline">Commencer maintenant</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-200">
                    <?php foreach ($recentAttempts as $attempt): ?>
                        <?php
                        $p = (float)($attempt['pourcentage'] ?? 0);
                        $badge = 'bg-red-50 text-red-700';
                        if ($p >= 75) {
                            $badge = 'bg-green-50 text-green-700';
                        } elseif ($p >= 50) {
                            $badge = 'bg-amber-50 text-amber-700';
                        }
                        ?>
                        <div class="flex items-start justify-between gap-4 p-5">
                            <div>
                                <div class="font-semibold text-slate-900"><?= htmlspecialchars($attempt['quiz_title']) ?></div>
                                <div class="mt-1 text-sm text-slate-600">
                                    Terminé le <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($attempt['completed_at']))) ?>
                                    • Temps: <?= (int)$attempt['temps_passe_minutes'] ?> min
                                    • Score: <?= htmlspecialchars($attempt['score']) ?>/<?= htmlspecialchars($attempt['total_points']) ?>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold <?= $badge ?>">
                                <?= round($p, 1) ?>%
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
