<?php
// Connect to or create SQLite database
$db = new SQLite3('emojis.db');
$db->exec("CREATE TABLE IF NOT EXISTS emojis (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  emoji TEXT NOT NULL,
  name TEXT NOT NULL
)");

// Add new emoji
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['emoji'], $_POST['name'])) {
  $emoji = htmlspecialchars($_POST['emoji']);
  $name = htmlspecialchars($_POST['name']);
  $stmt = $db->prepare("INSERT INTO emojis (emoji, name) VALUES (:emoji, :name)");
  $stmt->bindValue(':emoji', $emoji);
  $stmt->bindValue(':name', $name);
  $stmt->execute();
  header("Location: ".$_SERVER['PHP_SELF']);
  exit();
}

// Search emoji
$search = $_GET['search'] ?? '';
$searchEscaped = htmlspecialchars($search);
$results = $db->query("SELECT * FROM emojis WHERE name LIKE '%$searchEscaped%' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Emoji Dictionary</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body class="section">
  <div class="container box">
    <h1 class="title">Emoji Dictionary</h1>

    <form method="get" class="mb-5 card">
      <input class="input card-item" type="text" name="search" placeholder="Type emoji name to search" value="<?= $searchEscaped ?>">
    </form>

    <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
      <div class="box is-flex is-justify-content-space-between is-align-items-center">
        <span style="font-size: 24px"><?= $row['emoji'] ?></span>
        <span><?= htmlspecialchars($row['name']) ?></span>
      </div>
    <?php endwhile; ?>

    <hr>

    <h2 class="subtitle mt-5">Add New Emoji</h2>
    <form method="post">
      <div class="field card">
        <input class="input card-item" type="text" name="emoji" placeholder="Emoji (e.g., 😊)" required>
      </div>
      <div class="field">
        <input class="input" type="text" name="name" placeholder="Name (e.g., smile)" required>
      </div>
      <button class="button is-primary rounded-md px-4 hover:ring-4" type="submit">Save Emoji</button>
    </form>
  </div>
</body>
</html>
