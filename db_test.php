<?php
try {
    $db = new PDO('pgsql:host=ep-summer-heart-aqunm4f1-pooler.c-8.us-east-1.aws.neon.tech;port=5432;dbname=neondb;sslmode=require', 'neondb_owner', 'npg_KfOMgJh9Fw1x');
    $stmt = $db->query('SELECT schema_name FROM information_schema.schemata');
    echo "SCHEMAS:\n";
    while ($row = $stmt->fetch()) { echo $row['schema_name'] . "\n"; }
    
    echo "\nUSERS TABLE COUNT:\n";
    $stmt2 = $db->query("SELECT count(*) FROM schema_auth.users");
    echo $stmt2->fetchColumn() . "\n";
    
    echo "\nUSER EMAIL:\n";
    $stmt3 = $db->query("SELECT email FROM schema_auth.users LIMIT 1");
    echo $stmt3->fetchColumn() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
