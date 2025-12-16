<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="page">
    <div class="card">
        <h1 class="title">Connexion</h1>

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

        <form method="POST" action="/login" class="form">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-primary">
                Se connecter
            </button>
        </form>

        <p class="footer-text">
            Pas de compte ?
            <a href="/register">Créer un compte</a>
        </p>
    </div>
</body>

</html>