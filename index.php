<?php
require_once 'db.php';

// 1. CREATE: Insert a new task
if (isset($_POST['add'])) {
    $name = trim($_POST['task_name'] ?? '');
    $desc = trim($_POST['task_description'] ?? '');
    if ($name !== '' && $desc !== '') {
        $stmt = $pdo->prepare("INSERT INTO tasks (task_name, task_description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);
    }
    header("Location: index.php"); // prevents duplicate insert on refresh
    exit;
}

// 2. UPDATE: Toggle STATUS between pending and completed
if (isset($_POST['toggle'])) {
    $stmt = $pdo->prepare("UPDATE tasks SET STATUS = IF(STATUS='pending', 'completed', 'pending') WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    header("Location: index.php");
    exit;
}

// 3. DELETE: Remove a task
if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    header("Location: index.php");
    exit;
}

// 4. READ: Fetch all tasks
$tasks = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="site-header">
        <h1>Task List</h1>
        <p>Your tasks are saved in a database, so they stay put when you close the browser.</p>
    </header>

    <main>
        <section class="card" aria-labelledby="add-heading">
            <h2 id="add-heading">Add a task</h2>
            <form method="POST" action="index.php">
                <label for="task_name">Task name</label>
                <input type="text" id="task_name" name="task_name" maxlength="255" required>

                <label for="task_description">Description</label>
                <textarea id="task_description" name="task_description" rows="3" required></textarea>

                <button type="submit" name="add" class="btn btn-primary">Add task</button>
            </form>
        </section>

        <section aria-labelledby="list-heading">
            <h2 id="list-heading">Your tasks (<?= count($tasks) ?>)</h2>

            <?php if (empty($tasks)): ?>
                <p class="empty">No tasks yet. Add your first one above.</p>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <li class="task <?= $task['STATUS'] === 'completed' ? 'is-done' : '' ?>">
                            <div class="task-body">
                                <h3><?= htmlspecialchars($task['task_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= nl2br(htmlspecialchars($task['task_description'], ENT_QUOTES, 'UTF-8')) ?></p>
                                <small>
                                    <span class="badge badge-<?= htmlspecialchars($task['STATUS'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($task['STATUS'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    Added <?= htmlspecialchars($task['created_at'], ENT_QUOTES, 'UTF-8') ?>
                                </small>
                            </div>

                            <div class="task-actions">
                                <form method="POST" action="index.php">
                                    <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                                    <button type="submit" name="toggle" class="btn">
                                        <?= $task['STATUS'] === 'pending' ? 'Mark completed' : 'Mark pending' ?>
                                    </button>
                                </form>

                                <form method="POST" action="index.php" class="js-delete-form">
                                    <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
