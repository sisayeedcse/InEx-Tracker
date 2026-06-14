<?php

$databasePath = __DIR__ . '/database.sqlite';
$jsonPath = __DIR__ . '/database-data-export.json';
$sqlPath = __DIR__ . '/database-data-export.sql';

$db = new PDO('sqlite:' . $databasePath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

$data = [];
foreach ($tables as $table) {
    $data[$table] = $db->query('SELECT * FROM "' . $table . '"')->fetchAll(PDO::FETCH_ASSOC);
}

file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$sql = [];
$sql[] = 'PRAGMA foreign_keys=OFF;';
$sql[] = 'BEGIN TRANSACTION;';

foreach ($data as $table => $rows) {
    if (!$rows) {
        continue;
    }

    $columns = array_keys($rows[0]);
    $quotedColumns = array_map(static fn ($column) => '"' . $column . '"', $columns);
    $sql[] = '-- ' . $table;

    foreach ($rows as $row) {
        $values = [];
        foreach ($columns as $column) {
            $value = $row[$column];
            if ($value === null) {
                $values[] = 'NULL';
            } elseif (is_int($value) || is_float($value) || (is_string($value) && is_numeric($value) && !preg_match('/^0[0-9]+$/', $value))) {
                $values[] = (string) $value;
            } else {
                $escaped = str_replace("'", "''", (string) $value);
                $values[] = "'" . $escaped . "'";
            }
        }

        $sql[] = 'INSERT INTO "' . $table . '" (' . implode(', ', $quotedColumns) . ') VALUES (' . implode(', ', $values) . ');';
    }
}

$sql[] = 'COMMIT;';
file_put_contents($sqlPath, implode(PHP_EOL, $sql) . PHP_EOL);

echo 'Exported ' . count($tables) . ' tables to JSON and SQL.' . PHP_EOL;
