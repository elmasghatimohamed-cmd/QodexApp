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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Catégories</h1>
                <p class="text-sm text-slate-600">Gérez vos catégories.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/teacher/quizzes" class="text-sm text-blue-600 hover:underline">Mes quiz</a>
                <a href="/teacher/categories/create" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Nouvelle catégorie</a>
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
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Nom</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-slate-700">Description</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-slate-900"><?= htmlspecialchars($cat->name) ?></td>
                            <td class="px-4 py-3 text-sm text-slate-600"><?= htmlspecialchars($cat->description) ?></td>
                            <td class="px-4 py-3 text-right text-sm space-x-3">
                                <a href="/teacher/categories/edit?id=<?= (int)$cat->id ?>" class="text-blue-600 hover:underline">Modifier</a>
                                <form method="POST" action="/teacher/categories/delete" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
                                    <input type="hidden" name="id" value="<?= (int)$cat->id ?>">
                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Supprimer ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($categories)): ?>
            <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 text-sm text-slate-700">
                Aucune catégorie.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
