-- Создание базы данных, если она еще не существует
CREATE DATABASE IF NOT EXISTS mydb;

CREATE USER IF NOT EXISTS 'user'@'%' IDENTIFIED BY 'examplepassword';
GRANT SELECT,UPDATE,INSERT ON mydb.* TO 'user'@'%';
FLUSH PRIVILEGES;
-- Использование созданной базы данных
USE mydb;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL
);

CREATE TABLE IF NOT EXISTS surveys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS survey_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    survey_id INT NOT NULL,
    question_text TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS survey_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS survey_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    survey_id INT NOT NULL,
    question_id INT NOT NULL,
    option_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (survey_id) REFERENCES surveys(id),
    FOREIGN KEY (question_id) REFERENCES survey_questions(id),
    FOREIGN KEY (option_id) REFERENCES survey_options(id)
);


INSERT INTO users (username, password, role) VALUES
    ('user1', 'password1', 'user'),
    ('user2', 'password2', 'user'),
    ('admin', 'adminpassword', 'admin');

INSERT INTO surveys (title) VALUES
    ('Опрос 1'),
    ('Опрос 2'),
    ('Опрос 3');


-- Добавление вопросов и вариантов ответов для опроса 1
INSERT INTO survey_questions (survey_id, question_text) VALUES
    (1, 'Ваша страна?'),
    (1, 'Сколько вам лет?');

INSERT INTO survey_options (question_id, option_text) VALUES
    (1, 'США'),
    (1, 'Норвегия'),
    (1, 'Германия'),
    (2, 'Меньше 18'),
    (2, '18-30'),
    (2, 'Больше 30');

INSERT INTO survey_questions (survey_id, question_text) VALUES
    (2, 'Какой цвет вам нравится больше всего?'),
    (2, 'Какой вид музыки вы предпочитаете?');

INSERT INTO survey_options (question_id, option_text) VALUES
    (3, 'Синий'),
    (3, 'Зеленый'),
    (3, 'Красный'),
    (4, 'Поп'),
    (4, 'Рок'),
    (4, 'Классика');

-- Добавление вопросов и вариантов ответов для опроса 3
INSERT INTO survey_questions (survey_id, question_text) VALUES
    (3, 'Сколько раз в неделю вы занимаетесь спортом?'),
    (3, 'Что вы предпочитаете делать в выходные?');

INSERT INTO survey_options (question_id, option_text) VALUES
    (5, 'Никогда'),
    (5, '1-2 раза'),
    (5, '3-4 раза'),
    (6, 'Путешествовать'),
    (6, 'Смотреть фильмы'),
    (6, 'Читать книги');