<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Créer un quiz</title>

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

        .span-2 {
            grid-column: span 2;
        }

        .question-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-top: 20px;
        }

        .question-box h3 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .answers-title {
            font-size: 14px;
            margin: 16px 0 8px;
            color: #374151;
        }

        .answer {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 8px;
        }

        .answer input[type="text"] {
            flex: 1;
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
        <h1>Créer un quiz</h1>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/teacher/quizzes" class="card">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">

            <div class="field">
                <label>Titre</label>
                <input type="text" name="title" required>
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="grid-3">
                <div class="field">
                    <label>Catégorie</label>
                    <select name="categorie_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                        <?php endforeach; ?>
                    </select>

                    </select>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>

                <div class="field">
                    <label>Durée (minutes)</label>
                    <input type="number" name="duration" value="30" min="1">
                </div>
            </div>

            <div class="question-box">
                <h3>Question 1</h3>

                <div class="grid-3">
                    <div class="field span-2">
                        <label>Intitulé</label>
                        <input type="text" name="questions[0][text]" required>
                    </div>

                    <div class="field">
                        <label>Type</label>
                        <select name="questions[0][type_question]">
                            <option value="qcm">QCM</option>
                            <option value="vrai_faux">Vrai/Faux</option>
                            <option value="reponse_courte">Réponse courte</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Points</label>
                        <input type="number" name="questions[0][points]" value="1" min="1">
                    </div>
                </div>

                <p class="answers-title">Réponses (QCM / Vrai-Faux)</p>

                <div class="answer">
                    <input type="text" name="questions[0][answers][0][text]" placeholder="Réponse A">
                    <label>
                        <input type="checkbox" name="questions[0][answers][0][is_correct]">
                        Correcte
                    </label>
                </div>

                <div class="answer">
                    <input type="text" name="questions[0][answers][1][text]" placeholder="Réponse B">
                    <label>
                        <input type="checkbox" name="questions[0][answers][1][is_correct]">
                        Correcte
                    </label>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn-primary">Créer</button>
                <a href="/teacher/quizzes" class="btn-secondary">Retour</a>
            </div>
        </form>
    </div>
</body>

</html>