<?php use App\Middleware\CSRFMiddleware; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body class="page">
    <div class="card">
        <h1 class="title">Inscription</h1>

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

        <form method="POST" action="/register" class="form">
            <input type="hidden" name="csrf_token" value="<?= CSRFMiddleware::getToken(); ?>">

            <div class="form-group">
                <label>Prénom</label>
                <input type="text" name="first_name" required>
            </div>

            <div class="form-group">
                <label>Nom</label>
                <input type="text" name="last_name" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required>
            </div>

            <div class="form-group">
                <label>Rôle</label>
                <select name="role">
                    <option value="etudiant">Étudiant</option>
                    <option value="enseignant">Enseignant</option>
                </select>
            </div>

            <button type="submit" class="btn-primary">
                Créer le compte
            </button>
        </form>

        <p class="footer-text">
            Déjà inscrit ?
            <a href="/login">Connexion</a>
        </p>
    </div>
</body>

</html>