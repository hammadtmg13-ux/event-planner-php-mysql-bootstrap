<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: index.php');
    exit;
}

$categories = [
    'birthday' => 'Birthday',
    'meeting'  => 'Meeting',
    'party'    => 'Party',
    'study'    => 'Study',
    'sports'   => 'Sports',
    'other'    => 'Other',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '18:00';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $guests = max(0, (int)($_POST['guests'] ?? 0));

    if ($title !== '' && $eventDate !== '' && $location !== '') {
        $stmt = $pdo->prepare("UPDATE events SET title = :title, category = :category, event_date = :event_date, event_time = :event_time, location = :location, description = :description, guests = :guests WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':category' => $category,
            ':event_date' => $eventDate,
            ':event_time' => $eventTime,
            ':location' => $location,
            ':description' => $description,
            ':guests' => $guests,
            ':id' => $id,
        ]);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - EventHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="text-center mb-4">
        <h1 class="app-title">Edit Event</h1>
        <p class="text-muted">Update the details of your event.</p>
    </header>
    <div class="card shadow-sm mx-auto" style="max-width: 720px;">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Event title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($event['title']); ?>" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo $event['category'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="event_date" class="form-control" value="<?php echo $event['event_date']; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Time</label>
                    <input type="time" name="event_time" class="form-control" value="<?php echo substr($event['event_time'], 0, 5); ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Guests</label>
                    <input type="number" name="guests" class="form-control" min="0" value="<?php echo $event['guests']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($event['description']); ?>" maxlength="500">
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <footer class="text-center text-muted small mt-5">
        Built by Hammad with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
