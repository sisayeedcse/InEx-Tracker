<?php

$databasePath = __DIR__ . '/database.sqlite';
$sqlPath = __DIR__ . '/database-data-export.sql';

if (!file_exists($sqlPath)) {
    echo "SQL export file not found at $sqlPath\n";
    exit(1);
}

try {
    $db = new PDO('sqlite:' . $databasePath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get list of tables
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(PDO::FETCH_COLUMN);

    // Turn off foreign keys temporarily for truncation and import
    $db->exec('PRAGMA foreign_keys=OFF;');

    echo "Clearing existing data from tables...\n";
    foreach ($tables as $table) {
        $db->exec('DELETE FROM "' . $table . '"');
    }

    echo "Importing SQL data...\n";
    $sql = file_get_contents($sqlPath);
    $db->exec($sql);

    // Turn foreign keys back on
    $db->exec('PRAGMA foreign_keys=ON;');

    echo "Database successfully restored from $sqlPath.\n";
} catch (Exception $e) {
    echo "Error restoring database: " . $e->getMessage() . "\n";
    exit(1);
}
