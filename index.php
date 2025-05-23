<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDoリスト</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <header class="header">
            <div class="header-left">
                <h1>ToDoリスト</h1>
                <div id="date" class="date"></div>
            </div>
            <div class="header-right">
                <div id="clock" class="clock"></div>
                <a href="logout.php" class="logout-btn">ログアウト</a>
            </div>
        </header>
        
        <main class="main-content">
            <div class="form-container">
                <form id="todoForm" class="todo-form">
                    <div class="form-group">
                        <label for="todoInput">タスク名 <span class="required">*</span></label>
                        <input type="text" id="todoInput" placeholder="新しいタスクを入力" required>
                        <div class="error-message" id="taskError"></div>
                    </div>

                    <div class="form-group">
                        <label for="deadlineInput">締め切り日 <span class="required">*</span></label>
                        <input type="date" id="deadlineInput" required>
                        <div class="error-message" id="deadlineError"></div>
                    </div>

                    <div class="form-group">
                        <label for="detailInput">詳細</label>
                        <textarea id="detailInput" placeholder="タスクの詳細を入力"></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span class="btn-text">タスクを追加</span>
                        <span class="btn-icon">+</span>
                    </button>
                </form>
            </div>

            <div class="list-container">
                <div id="todoList" class="todo-list">
                    <!-- タスクがここに動的に追加されます -->
                </div>
            </div>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html> 