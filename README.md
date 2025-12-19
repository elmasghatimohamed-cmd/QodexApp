# Qodex - Application de gestion de quiz en PHP

## Description

QuizApp est une application web développée en PHP suivant l’architecture MVC.  
Elle permet la gestion de quiz avec deux rôles distincts : **enseignant** et **étudiant**.

L’enseignant peut créer et gérer des catégories, des quiz, des questions et consulter les résultats.  
L’étudiant peut s’inscrire, se connecter, consulter les quiz disponibles, y participer et visualiser ses résultats.

## Fonctionnalités

### Espace Enseignant

- Authentification sécurisée
- Gestion des catégories
- Création et modification des quiz
- Gestion des questions
- Consultation des résultats des étudiants

### Espace Étudiant

- Inscription et connexion
- Accès aux quiz disponibles
- Participation aux quiz
- Consultation des résultats

## Architecture

Le projet suit une architecture **MVC (Model – View – Controller)** :

- **Models** : gestion des entités et logique métier
- **Repositories** : accès aux données
- **Controllers** : gestion des requêtes et logique applicative
- **Views** : interface utilisateur

## Sécurité

Les mesures de sécurité suivantes ont été mises en place :

- Gestion des sessions
- Validation et sanitation des données
- Protection CSRF
- Requêtes préparées contre les injections SQL
- Contrôle d’accès par rôle (enseignant / étudiant)

## Technologies utilisées

- PHP
- MySQL
- HTML / CSS
- Architecture MVC
