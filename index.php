<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laba");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
        
        if ($result->num_rows > 0) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
        } else {
            echo "Неверные учетные данные.";
        }
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
    } elseif (isset($_POST['insert'])) {
        $data = $_POST['data'];
        $comment = $_POST['comment'];
        $conn->query("INSERT INTO edit (data, comment) VALUES ('$data', '$comment')");
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $data = $_POST['data'];
        $comment = $_POST['comment'];
        $conn->query("UPDATE edit SET data='$data', comment='$comment' WHERE id='$id'");
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $conn->query("DELETE FROM edit WHERE id='$id'");
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Веб-система</title>
	<link rel="stylesheet" href="index.css">
</head>
<body>
    <?php if (!isset($_SESSION['loggedin'])): ?>
        <form method="post">
            <input type="text" name="username" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="login">Войти</button>
        </form>
    <?php else: ?>
        <form method="post">
            <button type="submit" name="logout">Выйти</button>
        </form>

        <h2>Редактирование данных</h2>
        <form method="post">
            <input type="text" name="data" placeholder="Данные" required>
            <input type="text" name="comment" placeholder="Комментарий" required>
            <button type="submit" name="insert">Добавить</button>
        </form>

        <table border="1">
            <tr>
                <th>ID</th>
                <th>Данные</th>
                <th>Комментарий</th>
                <th>Действия</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM edit");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['data']}</td>";
                echo "<td>{$row['comment']}</td>";
                echo "<td>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <input type='text' name='data' value='{$row['data']}' required>
                            <input type='text' name='comment' value='{$row['comment']}' required>
                            <button type='submit' name='update'>Обновить</button>
                        </form>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='delete'>Удалить</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php endif; ?>
</body>
</html>