<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Modifier un quiz</h1>
        <?php if (!empty($error)): ?>
            <div class="mb-3 rounded bg-red-50 text-red-700 px-3 py-2 text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/teacher/quizzes/edit" class="space-y-4 bg-white shadow rounded-lg p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($quiz->id) ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700">Titre</label>
                <input type="text" name="title" value="<?= htmlspecialchars($quiz->title) ?>" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($quiz->description) ?></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie_id" required
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= $quiz->categorie_id == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="actif" <?= $quiz->status === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= $quiz->status === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Durée (minutes)</label>
                    <input type="number" name="duration" value="<?= htmlspecialchars($quiz->duration) ?>" min="1"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Enregistrer</button>
                <a href="/teacher/quizzes"
                    class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Retour</a>
            </div>
        </form>
    </div>
</body>

</html>