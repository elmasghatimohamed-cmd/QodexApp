<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz->title) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-10 px-4 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900"><?= htmlspecialchars($quiz->title) ?></h1>
                <p class="mt-1 text-slate-600"><?= htmlspecialchars($quiz->description) ?></p>
            </div>
            <a href="/student/quizzes" class="text-sm text-blue-600 hover:underline">Retour</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/student/quiz/submit" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <input type="hidden" name="quiz_id" value="<?= (int)$quiz->id ?>">

            <?php foreach ($questions as $q): ?>
                <div class="rounded-lg border border-slate-200 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-medium text-slate-900"><?= htmlspecialchars($q->text) ?></div>
                        <span class="text-xs text-slate-600"><?= (int)$q->points ?> pts</span>
                    </div>

                    <?php if ($q->type_question === 'reponse_courte'): ?>
                        <input type="text" name="answers[<?= (int)$q->id ?>]" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach (($answersByQuestion[$q->id] ?? []) as $a): ?>
                                <label class="flex items-center gap-2 text-sm text-slate-800">
                                    <input type="radio" name="answers[<?= (int)$q->id ?>]" value="<?= (int)$a->id ?>" required
                                        class="text-blue-600 focus:ring-blue-500">
                                    <?= htmlspecialchars($a->text) ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div>
                <label class="block text-sm font-medium text-slate-700">Temps passé (minutes)</label>
                <input type="number" name="temps_passe_minutes" value="0" min="0"
                    class="mt-1 block w-32 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                Soumettre
            </button>
        </form>
    </div>
</body>
</html>
