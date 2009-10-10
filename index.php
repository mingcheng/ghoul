<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Ghoul - Simple MiniBlog
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-10-09
 * @link   http://www.gracecode.com/
 */

function do_auth() {
    global $_CONFIG;
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="'.$_CONFIG['SITE_TITLE'].'"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    } else {
        if ($_CONFIG['AUTH_PASSWORD'] !== $_SERVER['PHP_AUTH_PW'] || $_CONFIG['AUTH_USERNAME'] !== $_SERVER['PHP_AUTH_USER']) {
            exit;
        }
    }
}

// 配置项 - 请修改 data/config.ini 文件
$_CONFIG = parse_ini_file('data/config.ini');

preg_match('/'.str_replace('/', '\/', $_CONFIG['REQUEST_URI_BASE']).'(\w+)\/*(\d*)\/*/i', $_SERVER["REQUEST_URI"], $match);
if (empty($match) || !$match[1]) {
    $match[1] = 'show';
}
$action = $match[1];

if ($action == 'delete' || $action == 'post' || ($_CONFIG['AUTH_OBTRUSION'] && $match[1] = 'show')) {
    do_auth();
}

if (!is_writeable($_CONFIG['SQLITE_DATABASE'])) {
    die('Database is not writeable.');
}
$Database = new PDO('sqlite:'.$_CONFIG['SQLITE_DATABASE']);


if (!$id = intval($_GET['id'])) {
    $id = intval(isset($match[2]) ? $match[2] : 0);
}

switch($action) {
    case 'show':
        $sql = "SELECT id, data, _date FROM micro_blog ";
        if ($id) {
            $sql .= "WHERE id = {$id} ";
        }
        $stmt = $Database->prepare($sql.' ORDER BY _date DESC');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($_GET['ajax']) {
            echo json_encode($result);
        } else {
            include 'data/show.inc.html';
        }
        break;
    case 'delete':
        if ($id) {
            $result = $Database->exec("DELETE FROM micro_blog WHERE id = {$id}");
            echo json_encode($result);
        }
        break;
    case 'post':
        $data = trim($_POST['content']);
        if (!empty($data)) {
            $stmt = $Database->prepare("INSERT INTO micro_blog(data, _date) VALUES (?, ?)");
            $stmt->bindParam(1, $data);
            $stmt->bindParam(2, time());
            $stmt->execute();
            echo json_encode($Database->lastInsertId());
        }
        break;
    default:
        echo 'Request empty!';
}

$plugin = dir("./plugin");
while (false !== ($entry = $plugin->read())) {
    if (preg_match('/\.inc\.php$/i', $entry)) {
        @include_once './plugin/' . $entry;
    }
}
$plugin->close(); $Database = null;
