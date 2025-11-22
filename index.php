<?php
$host = "localhost";
$dbname = "todolist";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ajouter une tâche
    if (isset($_POST["title"]) && !empty(trim($_POST["title"]))) {
        $title = trim($_POST["title"]);
        $query = $pdo->prepare("INSERT INTO todo (title) VALUES (?)");
        $query->execute([$title]);
    }

    // Supprimer une tâche
    if (isset($_GET["delete"])) {
        $id = intval($_GET["delete"]);
        $pdo->prepare("DELETE FROM todo WHERE id=?")->execute([$id]);
    }

    // Marquer comme fait / non fait
    if (isset($_GET["toggle"])) {
        $id = intval($_GET["toggle"]);
        $pdo->prepare("UPDATE todo SET done = NOT done WHERE id=?")->execute([$id]);
    }

    // Modifier une tâche
    if (isset($_POST["edit_id"]) && isset($_POST["edit_title"])) {
        $id = intval($_POST["edit_id"]);
        $title = trim($_POST["edit_title"]);
        $pdo->prepare("UPDATE todo SET title=? WHERE id=?")->execute([$title, $id]);
    }

    // Récupérer les tâches
    $todos = $pdo->query("SELECT * FROM todo ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List PHP</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

    <h1>ToDo List</h1>

    <!-- Formulaire d'ajout -->
    <form method="POST" class="add-form">
        <input type="text" name="title" placeholder="Nouvelle tâche..." required>
        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste -->
    <ul class="todo-list">
        <?php foreach ($todos as $todo): ?>
            <li class="<?= $todo['done'] ? 'done' : 'pending' ?>">
                <span><?= htmlspecialchars($todo["title"]) ?></span>

                <div class="actions">
                    <a href="?toggle=<?= $todo['id'] ?>" class="btn toggle">
                        <?= $todo["done"] ? "Annuler" : "Fait" ?>
                    </a>

                    <button onclick="editTask(<?= $todo['id'] ?>, '<?= htmlspecialchars($todo['title'], ENT_QUOTES) ?>')" class="btn edit">Modifier</button>

                    <a href="?delete=<?= $todo['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn delete">Supprimer</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Formulaire de modification caché -->
    <div id="edit-popup" class="popup hidden">
        <form method="POST" class="popup-form">
            <h3>Modifier la tâche</h3>
            <input type="hidden" name="edit_id" id="edit_id">
            <input type="text" name="edit_title" id="edit_title" required>
            <button type="submit">Enregistrer</button>
            <button type="button" onclick="closePopup()">Annuler</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
