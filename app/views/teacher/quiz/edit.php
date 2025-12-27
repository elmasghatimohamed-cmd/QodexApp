<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un quiz</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, sans-serif;
        }

        body {
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 16px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .card {
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .field {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        label {
            font-size: 14px;
            margin-bottom: 6px;
            color: #374151;
        }

        input,
        textarea,
        select {
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
        }

        textarea {
            resize: vertical;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            border: 1px solid #d1d5db;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: #374151;
            background: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #f3f4f6;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Modifier un quiz</h1>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/teacher/quizzes" class="card">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars($quiz->id) ?>">

            <div class="field">
                <label>Titre</label>
                <input type="text" name="title" value="<?= htmlspecialchars($quiz->title) ?>" required>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description"><?= htmlspecialchars($quiz->description) ?></textarea>
            </div>

            <div class="grid-3">
                <div class="field">
                    <label>Catégorie</label>
                    <select name="categorie_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= $quiz->categorie_id == $cat->id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option value="actif" <?= $quiz->status === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= $quiz->status === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>

                <div class="field">
                    <label>Durée (minutes)</label>
                    <input type="number" name="duration" value="<?= htmlspecialchars($quiz->duration) ?>" min="1">
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary">Enregistrer</button>
                <a href="/teacher/quizzes" class="btn-secondary">Retour</a>
            </div>
        </form>
    </div>
</body>

</html>