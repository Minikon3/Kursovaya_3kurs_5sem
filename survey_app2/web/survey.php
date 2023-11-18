<?php
// Подключение к базе данных
$mysqli = new mysqli("db", "root", "examplepassword", "mydb");

if ($mysqli->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysqli->connect_error);
}

// Проверка, что пользователь аутентифицирован
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit();
}

// Получение идентификатора выбранного опроса
if (isset($_GET["id"])) {
    $surveyId = $_GET["id"];
} else {
    header("Location: user.php");
    exit();
}

// Получение идентификатора текущего пользователя
$userId = $_SESSION["id"]; // Предположим, что у вас есть идентификатор пользователя

// Проверка, проходил ли пользователь этот опрос
$query = "SELECT * FROM survey_responses WHERE user_id = $userId AND survey_id = $surveyId";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    // Пользователь уже прошел этот опрос
    $surveyCompletedMessage = "Вы уже прошли этот опрос.";
}

// Получение вопросов и вариантов ответов для выбранного опроса
$query = "SELECT sq.id AS question_id, sq.question_text, so.id AS option_id, so.option_text
          FROM survey_questions AS sq
          LEFT JOIN survey_options AS so ON sq.id = so.question_id
          WHERE sq.survey_id = $surveyId";
$result = $mysqli->query($query);

// Создание массива для хранения вопросов и вариантов ответов
$questions = array();

while ($row = $result->fetch_assoc()) {
    $questionId = $row["question_id"];
    $questionText = $row["question_text"];
    $optionId = $row["option_id"];
    $optionText = $row["option_text"];

    if (!isset($questions[$questionId])) {
        $questions[$questionId] = array(
            "text" => $questionText,
            "options" => array()
        );
    }

    if (!empty($optionText)) {
        $questions[$questionId]["options"][$optionId] = $optionText;
    }
}

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Получение данных из формы
    $surveyId = $_POST["survey_id"];
    $userId = $_SESSION["id"]; // Предположим, что у вас есть идентификатор пользователя

    // Вставка результатов опроса в базу данных
    foreach ($_POST as $field => $optionId) {
        if (strpos($field, "question_") === 0) {
            $questionId = substr($field, strlen("question_"));
            $query = "INSERT INTO survey_responses (user_id, survey_id, question_id, option_id)
                      VALUES ($userId, $surveyId, $questionId, $optionId)";
            $mysqli->query($query);
        }
    }

    header("Location: user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Опрос</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            text-align: center;
        }
        /* Стиль для контейнера опроса */
        .survey-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Стиль для заголовка опроса */
        .survey-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        /* Стиль для ссылки "Вернуться к списку опросов" */
        .survey-container a {
            display: block;
            margin-bottom: 10px;
            color: #007bff;
            text-decoration: none;
        }

        .survey-container a:hover {
            text-decoration: underline;
        }

        /* Стиль для вопросов и вариантов ответов */
        .survey-container p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .survey-container label {
            display: block;
            margin-bottom: 5px;
        }

        /* Стиль для кнопки "Отправить" */
        .survey-container input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .survey-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="survey-container">
        <h2>Опрос</h2>
        <?php if (isset($surveyCompletedMessage)) : ?>
            <p><?php echo $surveyCompletedMessage; ?></p>
            <a href="user.php">Вернуться к списку опросов</a>
        <?php else : ?>
            <a href="user.php">Вернуться к списку опросов</a>
            <form action="" method="post">
                <input type="hidden" name="survey_id" value="<?php echo $surveyId; ?>">
                <?php foreach ($questions as $questionId => $question) : ?>
                    <p><?php echo $question["text"]; ?></p>
                    <?php foreach ($question["options"] as $optionId => $optionText) : ?>
                        <label>
                            <input type="radio" name="question_<?php echo $questionId; ?>" value="<?php echo $optionId; ?>">
                            <?php echo $optionText; ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <input type="submit" value="Отправить">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
