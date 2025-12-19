<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Créer un quiz</h1>
        <?php if (!empty($error)): ?>
            <div class="mb-3 rounded bg-red-50 text-red-700 px-3 py-2 text-sm"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="/teacher/quizzes/create" class="space-y-4 bg-white shadow rounded-lg p-6">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700">Titre</label>
                <input type="text" name="title" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catégorie</label>
                    <select name="categorie_id" required
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Durée (minutes)</label>
                    <input type="number" name="duration" value="30" min="1"
                        class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="border rounded-lg p-4 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Question 1</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Intitulé</label>
                        <input type="text" name="questions[0][text]" placeholder="Intitulé" required
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="questions[0][type_question]"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="qcm">QCM</option>
                            <option value="vrai_faux">Vrai/Faux</option>
                            <option value="reponse_courte">Réponse courte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Points</label>
                        <input type="number" name="questions[0][points]" value="1" min="1"
                            class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-sm font-medium text-gray-700 mb-1">Réponses (QCM / Vrai-Faux)</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="text" name="questions[0][answers][0][text]" placeholder="Réponse A"
                                class="flex-1 rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <label class="flex items-center gap-1 text-sm text-gray-700">
                                <input type="checkbox" name="questions[0][answers][0][is_correct]"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Correcte
                            </label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" name="questions[0][answers][1][text]" placeholder="Réponse B"
                                class="flex-1 rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <label class="flex items-center gap-1 text-sm text-gray-700">
                                <input type="checkbox" name="questions[0][answers][1][is_correct]"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                Correcte
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex items-center rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Créer</button>
                <a href="/teacher/quizzes"
                    class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Retour</a>
            </div>
        </form>
    </div>
</body>

</html>