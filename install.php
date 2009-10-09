<?php
header('Content-type: text/plain');
$_CONFIG = parse_ini_file('data/config.ini');

$db = new PDO('sqlite:'.$_CONFIG['SQLITE_DATABASE']);

@$db->exec('DROP TABLE micro_blog');
$db->exec('CREATE TABLE micro_blog (id integer primary key, data BLOB not NULL, _date char(10) not NULL)');

function write_params($data, &$db) {
    $stmt = $db->prepare("INSERT INTO micro_blog (data, _date) VALUES (?, ?)");
    $stmt->bindParam(1, $data);
    $stmt->bindParam(2, time());
    $stmt->execute();
    return $db->lastInsertId() ? true : false;
}

function read_params($flag, &$db) {
    $stmt = $db->prepare("SELECT id, data, _date FROM micro_blog ORDER BY _date DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$data = 'Master, I wanna cannibalize, and it\'s can not wait...';
if (write_params($data, $db)) {
    die('Everything is OK!');
}
