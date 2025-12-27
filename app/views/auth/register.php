<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
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

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background-color: white;
            transition: all 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            color: white;
            background-color: #2563eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button[type="submit"]:hover {
            background-color: #1d4ed8;
        }

        .footer-text {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            margin-top: 24px;
        }

        .footer-text a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>Inscription</h1>
            <p class="subtitle">Créez un compte étudiant ou enseignant.</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register">
                <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmation</label>
                        <input type="password" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rôle</label>
                    <select name="role">
                        <option value="etudiant">Étudiant</option>
                        <option value="enseignant">Enseignant</option>
                    </select>
                </div>

                <button type="submit">Créer le compte</button>
            </form>

            <p class="footer-text">
                Déjà inscrit ?
                <a href="/login">Connexion</a>
            </p>
        </div>
    </div>
</body>

</html>