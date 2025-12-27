<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz->title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .header-title p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        .header a {
            font-size: 14px;
            color: #2563eb;
            text-decoration: none;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        form {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .question {
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
        }

        .question-text {
            font-weight: 500;
            color: #1e293b;
            font-size: 15px;
        }

        .question-points {
            font-size: 12px;
            color: #64748b;
            white-space: nowrap;
        }

        .question input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: white;
            margin-top: 8px;
            transition: all 0.2s;
        }

        .question input[type="text"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .answers {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .answer-option {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            border-radius: 6px;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .answer-option:hover {
            background-color: #f1f5f9;
        }

        .answer-option input[type="radio"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .answer-option label {
            font-size: 14px;
            color: #1e293b;
            cursor: pointer;
            flex: 1;
        }

        .form-group {
            margin-top: 24px;
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group input[type="number"] {
            width: 150px;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: white;
            transition: all 0.2s;
        }

        .form-group input[type="number"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            background-color: #2563eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-title">
                <h1><?= htmlspecialchars($quiz->title) ?></h1>
                <p><?= htmlspecialchars($quiz->description) ?></p>
            </div>
            <a href="/student/quizzes">Retour</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/student/quiz/submit">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">
            <input type="hidden" name="quiz_id" value="<?= (int) $quiz->id ?>">

            <?php foreach ($questions as $q): ?>
                <div class="question">
                    <div class="question-header">
                        <div class="question-text"><?= htmlspecialchars($q->text) ?></div>
                        <span class="question-points"><?= (int) $q->points ?> pts</span>
                    </div>

                    <?php if ($q->type_question === 'reponse_courte'): ?>
                        <input type="text" name="answers[<?= (int) $q->id ?>]" required>
                    <?php else: ?>
                        <div class="answers">
                            <?php foreach (($answersByQuestion[$q->id] ?? []) as $a): ?>
                                <div class="answer-option">
                                    <input type="radio" name="answers[<?= (int) $q->id ?>]" value="<?= (int) $a->id ?>"
                                        id="answer_<?= (int) $a->id ?>" required>
                                    <label for="answer_<?= (int) $a->id ?>">
                                        <?= htmlspecialchars($a->text) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-group">
                <label>Temps passé (minutes)</label>
                <input type="number" name="temps_passe_minutes" value="0" min="0">
            </div>

            <button type="submit" class="btn-submit">Soumettre</button>
        </form>
    </div>
</body>

</html>