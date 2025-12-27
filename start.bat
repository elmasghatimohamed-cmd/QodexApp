@echo off
echo ================================================
echo  🚀 DÉMARRAGE QUIZAPP - PLATEFORME DE QUIZ
echo ================================================
echo.

REM -------------------------------------------------
REM 1. VÉRIFICATION PHP
REM -------------------------------------------------
echo [1/3] Vérification de PHP...
where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo ❌ ERREUR: PHP n'est pas installé ou pas dans le PATH
    echo.
    echo INSTALLATION RAPIDE:
    echo 1. Téléchargez PHP: https://windows.php.net/downloads/releases/php-8.2.13-Win32-vs16-x64.zip
    echo 2. Extrayez dans C:\php
    echo 3. Ajoutez C:\php au PATH
    echo 4. Redémarrez le terminal
    echo.
    pause
    exit /b 1
)

echo ✅ PHP trouvé: 
php --version | findstr /i "php version"

REM -------------------------------------------------
REM 2. CONFIGURATION PHP
REM -------------------------------------------------
echo [2/3] Configuration PHP...
echo Création du fichier php.ini temporaire...

(
echo ; Configuration PHP temporaire pour QuizApp
echo extension_dir = "ext"
echo extension=php_pdo_mysql.dll
echo extension=php_mysqli.dll
echo extension=php_mbstring.dll
echo extension=php_openssl.dll
echo.
echo error_reporting = E_ALL
echo display_errors = On
echo display_startup_errors = On
echo log_errors = On
echo error_log = "php_errors.log"
echo.
echo date.timezone = "Europe/Paris"
echo session.save_path = "%TEMP%"
echo session.use_strict_mode = 1
echo session.use_cookies = 1
echo session.use_only_cookies = 1
echo session.cookie_httponly = 1
echo session.cookie_samesite = "Strict"
echo.
echo upload_max_filesize = 10M
echo post_max_size = 10M
) > php_temp.ini

REM -------------------------------------------------
REM 3. CRÉATION INDEX.PHP SI MANQUANT DANS PUBLIC
REM -------------------------------------------------
if not exist "public\index.php" (
    echo Création du fichier public\index.php...
    
    (
    echo ^<?php
    echo // index.php - Point d'entrée principal
    echo session_start^();
    echo.
    echo echo "^<h1^>✅ QuizApp - Plateforme de Quiz^</h1^>";
    echo echo "^<p^>Serveur démarré avec succès !^</p^>";
    echo echo "^<p^>Date: " . date^('Y-m-d H:i:s'^) . "^</p^>";
    echo echo "^<p^>PHP Version: " . phpversion^(^) . "^</p^>";
    echo.
    echo // Tester la base de données
    echo try {
    echo     require_once __DIR__ . '/../config/Database.php';
    echo     $db = new Database^(^);
    echo     $pdo = $db-^>getConnection^(^);
    echo     $stmt = $pdo-^>query^("SELECT VERSION^(^) as version"^);
    echo     $result = $stmt-^>fetch^(^);
    echo     echo "^<p^>✅ Base de données: " . $result['version'] . "^</p^>";
    echo     echo "^<p^>✅ Connexion MySQL réussie^</p^>";
    echo } catch ^(Exception $e^) {
    echo     echo "^<p^>❌ Erreur DB: " . $e-^>getMessage^(^) . "^</p^>";
    echo }
    echo.
    echo // Liens
    echo echo "^<h2^>Liens rapides:^</h2^>";
    echo echo "^<ul^>";
    echo echo "^<li^>^<a href='/login'^>Connexion^</a^>^</li^>";
    echo echo "^<li^>^<a href='/register'^>Inscription^</a^>^</li^>";
    echo echo "^<li^>^<a href='/teacher/dashboard'^>Dashboard Enseignant^</a^>^</li^>";
    echo echo "^<li^>^<a href='/student/dashboard'^>Dashboard Étudiant^</a^>^</li^>";
    echo echo "^<li^>^<a href='/teacher/quizzes'^>Gestion des Quiz^</a^>^</li^>";
    echo echo "^</ul^>";
    echo.
    echo // Routeur basique
    echo $uri = $_SERVER['REQUEST_URI'] ?? '/';
    echo switch ^($uri^) {
    echo     case '/login':
    echo         if ^(file_exists^('app/Views/auth/login.php'^)^) {
    echo             require 'app/Views/auth/login.php';
    echo         }
    echo         break;
    echo     default:
    echo         // Afficher déjà fait
    echo }
    echo ?^>
    ) > public\index.php
)

REM -------------------------------------------------
REM 4. DÉMARRAGE DU SERVEUR
REM -------------------------------------------------
echo [3/3] Démarrage du serveur...
echo.

REM Trouver un port libre
set PORT=8000
:check_port
netstat -ano | findstr :%PORT% >nul
if %ERRORLEVEL% equ 0 (
    set /a PORT+=1
    goto check_port
)

echo ✅ Port %PORT% disponible
echo 🌐 URL: http://localhost:%PORT%/
echo 📂 Dossier racine du serveur: %cd%\public
echo.
echo 📋 POUR TESTER:
echo 1. Ouvrez Chrome/Firefox
echo 2. Allez à: http://localhost:%PORT%/
echo 3. Utilisez Ctrl+C pour arrêter
echo.
echo ================================================
echo.

REM Démarrer le serveur avec le dossier public comme racine
php -c php_temp.ini -S localhost:%PORT% -t public/

REM Nettoyage
if exist php_temp.ini del php_temp.ini