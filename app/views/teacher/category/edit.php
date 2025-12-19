<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier catégorie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Modifier catégorie</h1>
                <p class="text-sm text-slate-600">Mettez à jour les informations.</p>
            </div>
            <a href="/teacher/categories" class="text-sm text-blue-600 hover:underline">Retour</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/teacher/categories/edit" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <input type="hidden" name="id" value="<?= (int)$category->id ?>">

            <div>
                <label class="block text-sm font-medium text-slate-700">Nom</label>
                <input type="text" name="name" value="<?= htmlspecialchars($category->name) ?>" required
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" rows="4"
                    class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200"><?= htmlspecialchars($category->description) ?></textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Enregistrer</button>
                <a href="/teacher/categories" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
