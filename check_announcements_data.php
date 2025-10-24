<?php
// Check announcements data in database
require 'app/Config/Database.php';

$db = \Config\Database::connect();
$query = $db->query('SELECT id, title, category, priority, description FROM ci_announcements ORDER BY id DESC LIMIT 10');

echo "=== ANNOUNCEMENTS DATA FROM DATABASE ===" . PHP_EOL . PHP_EOL;

foreach($query->getResult() as $row) {
    echo "ID: " . $row->id . PHP_EOL;
    echo "Title: " . $row->title . PHP_EOL;
    echo "Category: " . ($row->category ?? 'NULL') . PHP_EOL;
    echo "Priority: " . ($row->priority ?? 'NULL') . PHP_EOL;
    echo "Description: " . substr($row->description, 0, 50) . "..." . PHP_EOL;
    echo "---" . PHP_EOL;
}
