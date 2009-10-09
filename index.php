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

// 根据 URL 解析控制器
preg_match('/'.str_replace('/', '\/', $_CONFIG['REQUEST_URI_BASE']).'(\w+)/i', $_SERVER["REQUEST_URI"], $match);
if (empty($match) || !$match[1]) {
    $match[1] = 'show';
}

if ($_CONFIG['AUTH_OBTRUSION']) {
    do_auth();
}

// 链接读取数据库
$Database = new PDO('sqlite:'.$_CONFIG['SQLITE_DATABASE']);

switch($action = $match[1]) {
    // 显示条目
    case 'show':
        $stmt = $Database->prepare("SELECT id, data, _date FROM micro_blog ORDER BY _date DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($_GET['ajax']) {
            echo json_encode($result);
        } else {
            include 'inc/show.inc.html';
        }
        break;
    case 'delete':
        do_auth();
        $id = intval($_GET['id']);
        if ($id) {
            $result = $Database->exec("DELETE FROM micro_blog WHERE id = {$id}");
            echo json_encode($result);
        }
        break;
    case 'post':
        do_auth();
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

$Database = null;
