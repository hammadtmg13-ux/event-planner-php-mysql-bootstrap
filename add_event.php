<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $eventDate = $_POST['event_date'] ?? '';
    $eventTime = $_POST['event_time'] ?? '18:00';
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $guests = max(0, (int)($_POST['guests'] ?? 0));

    if ($title !== '' && $eventDate !== '' && $location !== '') {
        $stmt = $pdo->prepare("INSERT INTO events (title, category, event_date, event_time, location, description, guests) VALUES (:title, :category, :event_date, :event_time, :location, :description, :guests)");
        $stmt->execute([
            ':title' => $title,
            ':category' => $category,
            ':event_date' => $eventDate,
            ':event_time' => $eventTime,
            ':location' => $location,
            ':description' => $description,
            ':guests' => $guests,
        ]);
    }
}

header('Location: index.php');
exit;
