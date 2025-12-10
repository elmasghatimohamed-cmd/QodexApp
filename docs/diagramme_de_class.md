```mermaid


classDiagram
    %% ===============================
    %% USERS
    %% ===============================
    class User {
        +int id
        +string email
        +string password
        +string first_name
        +string last_name
        +string role
        +timestamp created_at
        +timestamp updated_at
        +timestamp deleted_at
        +timestamp last_login
        --
        +login(email, password) : bool
        +logout() : void
        +register(data) : bool
        +updateProfile(data) : bool
        +isTeacher() : bool
        +isStudent() : bool
    }

    %% ===============================
    %% CATEGORIES
    %% ===============================
    class Category {
        +int id
        +string name
        +text description
        +int enseignant_id
        +timestamp created_at
        +timestamp updated_at
        +timestamp deleted_at
        --
        +create(data) : bool
        +update(data) : bool
        +delete() : bool
        +getQuizzes() : list<Quiz>
    }

    %% ===============================
    %% QUIZZES
    %% ===============================
    class Quiz {
        +int id
        +string title
        +text description
        +int categorie_id
        +int enseignant_id
        +string status
        +int duration
        +timestamp created_at
        +timestamp updated_at
        +timestamp deleted_at
        --
        +create(data) : bool
        +update(data) : bool
        +delete() : bool
        +addQuestion(questionData) : bool
        +getQuestions() : list<Question>
        +calculateTotalPoints() : int
    }

    %% ===============================
    %% QUESTIONS
    %% ===============================
    class Question {
        +int id
        +int quiz_id
        +text text
        +string type_question
        +int points
        +int ordre
        +timestamp created_at
        +timestamp deleted_at
        --
        +create(data) : bool
        +update(data) : bool
        +delete() : bool
        +addAnswer(answerData) : bool
        +getAnswers() : list<Answer>
    }

    %% ===============================
    %% ANSWERS
    %% ===============================
    class Answer {
        +int id
        +int question_id
        +text text
        +bool is_correct
        +timestamp created_at
        +timestamp deleted_at
        --
        +create(data) : bool
        +update(data) : bool
        +delete() : bool
    }

    %% ===============================
    %% STUDENT QUIZZES
    %% ===============================
    class StudentQuiz {
        +int id
        +int quiz_id
        +int etudiant_id
        +decimal score
        +int total_points
        +decimal pourcentage
        +int temps_passe_minutes
        +timestamp started_at
        +timestamp completed_at
        --
        +startQuiz() : void
        +completeQuiz() : void
        +calculateScore() : void
        +getStudentAnswers() : list<StudentAnswer>
    }

    %% ===============================
    %% STUDENT ANSWERS
    %% ===============================
    class StudentAnswer {
        +int id
        +int student_quiz_id
        +int question_id
        +int answer_id
        +text reponse_texte
        +bool is_correct
        +timestamp created_at
        --
        +submitAnswer(answerData) : bool
        +checkCorrect() : bool
    }

    %% ===============================
    %% SESSIONS
    %% ===============================
    class Session {
        +string id
        +int user_id
        +text data
        +timestamp last_activity
        --
        +createSession(userId) : void
        +updateSession(data) : void
        +destroySession() : void
    }

    %% ===============================
    %% SECURITY LOGS
    %% ===============================
    class SecurityLog {
        +int id
        +int user_id
        +string action
        +string ip_address
        +text user_agent
        +text details
        +timestamp created_at
        --
        +logAction(userId, action, details) : void
        +getLogs(userId) : list<SecurityLog>
    }

    %% ===============================
    %% RELATIONS AVEC CASCADE
    %% ===============================
    User "1" -- "0..*" Category : enseignant_id [ON DELETE CASCADE]
    User "1" -- "0..*" Quiz : enseignant_id [ON DELETE CASCADE]
    Category "1" -- "0..*" Quiz : categorie_id [ON DELETE CASCADE]
    Quiz "1" -- "0..*" Question : quiz_id [ON DELETE CASCADE]
    Question "1" -- "0..*" Answer : question_id [ON DELETE CASCADE]
    Quiz "1" -- "0..*" StudentQuiz : quiz_id [ON DELETE CASCADE]
    User "1" -- "0..*" StudentQuiz : etudiant_id [ON DELETE CASCADE]
    StudentQuiz "1" -- "0..*" StudentAnswer : student_quiz_id [ON DELETE CASCADE]
    Question "1" -- "0..*" StudentAnswer : question_id [ON DELETE CASCADE]
    Answer "1" -- "0..1" StudentAnswer : answer_id [ON DELETE SET NULL]
    User "1" -- "0..*" Session : user_id [ON DELETE CASCADE]
    User "1" -- "0..*" SecurityLog : user_id [ON DELETE SET NULL]
