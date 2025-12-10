--@block
CREATE DATABASE IF NOT EXISTS quizapp_db;

--@block
USE quizapp_db;

--@block
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_deleted_at (deleted_at)
);

--@block
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    enseignant_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_enseignant_id (enseignant_id),
    INDEX idx_deleted_at (deleted_at)
);

--@block
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    categorie_id INT NOT NULL,
    enseignant_id INT NOT NULL,
    status ENUM('actif', 'inactif') DEFAULT 'actif',
    duration INT DEFAULT 30, -- en minutes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_categorie_id (categorie_id),
    INDEX idx_enseignant_id (enseignant_id),
    INDEX idx_status (status),
    INDEX idx_deleted_at (deleted_at)
);

--@block
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    text TEXT NOT NULL,
    type_question ENUM('qcm', 'vrai_faux', 'reponse_courte') DEFAULT 'qcm',
    points INT DEFAULT 1,
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_deleted_at (deleted_at)
);

--@block
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    text TEXT NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id),
    INDEX idx_deleted_at (deleted_at)
);


--@block
CREATE TABLE student_quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    etudiant_id INT NOT NULL,
    score DECIMAL(5,2) DEFAULT 0,
    total_points INT DEFAULT 0,
    pourcentage DECIMAL(5,2) DEFAULT 0,
    temps_passe_minutes INT DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (etudiant_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uq_quiz_etudiant (quiz_id, etudiant_id),
    INDEX idx_quiz_id (quiz_id),
    INDEX idx_etudiant_id (etudiant_id),
    INDEX idx_completed_at (completed_at)
);


--@block
CREATE TABLE student_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_quiz_id INT NOT NULL,
    question_id INT NOT NULL,
    answer_id INT NULL,
    reponse_texte TEXT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_quiz_id) REFERENCES student_quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (answer_id) REFERENCES answers(id) ON DELETE SET NULL,
    INDEX idx_student_quiz_id (student_quiz_id),
    INDEX idx_question_id (question_id)
);

--@block
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NULL,
    data TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);


--@block
CREATE TABLE security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);