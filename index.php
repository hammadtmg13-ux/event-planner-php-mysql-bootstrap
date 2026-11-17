<?php
require 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$upcomingOnly = isset($_GET['upcoming']) && $_GET['upcoming'] === '1';

// Fetch events with optional search and filters
$sql = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :search OR location LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($categoryFilter !== 'all') {
    $sql .= " AND category = :category";
    $params[':category'] = $categoryFilter;
}
if ($upcomingOnly) {
    $sql .= " AND event_date >= CURDATE()";
}
$sql .= " ORDER BY event_date ASC, event_time ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Event stats
$totalEvents = (int)$pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$upcomingEvents = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()")->fetchColumn();
$attendingEvents = (int)$pdo->query("SELECT COUNT(*) FROM events WHERE attending = 1")->fetchColumn();
$totalGuests = (int)$pdo->query("SELECT COALESCE(SUM(guests), 0) FROM events")->fetchColumn();

$categories = [
    'birthday' => 'Birthday',
    'meeting'  => 'Meeting',
    'party'    => 'Party',
    'study'    => 'Study',
    'sports'   => 'Sports',
    'other'    => 'Other',
];

$categoryColors = [
    'birthday' => 'bg-danger',
    'meeting'  => 'bg-primary',
    'party'    => 'bg-warning text-dark',
    'study'    => 'bg-info text-dark',
    'sports'   => 'bg-success',
    'other'    => 'bg-secondary',
];

function formatDate($date) {
    return date('D, d M Y', strtotime($date));
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function isPast($date) {
    return strtotime($date) < strtotime(date('Y-m-d'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Simple Event Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="text-center mb-4">
        <h1 class="app-title">EventHub</h1>
        <p class="text-muted">Every event you plan, kept in one place.</p>
    </header>

    <!-- Event stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0"><?php echo $totalEvents; ?></h3><small class="text-muted">Total events</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-primary"><?php echo $upcomingEvents; ?></h3><small class="text-muted">Upcoming</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-success"><?php echo $attendingEvents; ?></h3><small class="text-muted">Attending</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-warning"><?php echo $totalGuests; ?></h3><small class="text-muted">Total guests</small>
            </div></div>
        </div>
    </div>

    <!-- Add event form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Plan a New Event</h5>
            <form action="add_event.php" method="POST" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="title" class="form-control" placeholder="Event title" required maxlength="255">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <?php foreach ($categories as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="event_date" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="time" name="event_time" class="form-control" value="18:00" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="guests" class="form-control" placeholder="Guests" min="0" value="0" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="location" class="form-control" placeholder="Location" required maxlength="255">
                </div>
                <div class="col-md-6">
                    <input type="text" name="description" class="form-control" placeholder="Short description" maxlength="500">
                </div>
                <div class="col-12 d-grid d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary px-4">Save Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search and filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="search" name="search" class="form-control" placeholder="Search events or locations..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="all" <?php echo $categoryFilter === 'all' ? 'selected' : ''; ?>>All categories</option>
                <?php foreach ($categories as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo $categoryFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
        <div class="col-md-2 d-grid">
            <a href="index.php?upcoming=1" class="btn <?php echo $upcomingOnly ? 'btn-primary' : 'btn-outline-primary'; ?>">Upcoming</a>
        </div>
    </form>

    <!-- Event list -->
    <?php if (empty($events)): ?>
        <div class="alert alert-info text-center">No events found. Plan your first event above.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6">
                    <div class="card event-card shadow-sm <?php echo $event['attending'] ? 'event-attending' : ''; ?> <?php echo isPast($event['event_date']) ? 'event-past' : ''; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                                <?php if ($event['attending']): ?><span class="fs-5" title="Attending">&#9989;</span><?php endif; ?>
                            </div>
                            <p class="mb-2">
                                <span class="badge <?php echo $categoryColors[$event['category']]; ?>"><?php echo $categories[$event['category']]; ?></span>
                                <span class="text-muted small ms-1">&#128197; <?php echo formatDate($event['event_date']); ?></span>
                                <span class="text-muted small ms-1">&#128336; <?php echo formatTime($event['event_time']); ?></span>
                            </p>
                            <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                            <?php if (!empty($event['description'])): ?>
                                <p class="mb-3 text-muted small"><?php echo htmlspecialchars($event['description']); ?></p>
                            <?php endif; ?>
                            <p class="mb-3"><span class="text-muted small">&#128100; <?php echo $event['guests']; ?> guests</span>
                            <?php if (isPast($event['event_date'])): ?><span class="badge bg-secondary ms-2">Past event</span><?php endif; ?></p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="toggle_attending.php?id=<?php echo $event['id']; ?>" class="btn btn-sm <?php echo $event['attending'] ? 'btn-success' : 'btn-outline-success'; ?>"><?php echo $event['attending'] ? 'Attending' : 'Mark Attending'; ?></a>
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete();">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <footer class="text-center text-muted small mt-5">
        Built by Hammad with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
