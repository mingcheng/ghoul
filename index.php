<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Ghoul - Simple MiniBlog
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-10-09
 * @link   http://www.gracecode.com/
 */

define('DIR_DATA',   realpath('./data'));
define('DIR_PLUGIN', realpath('./plugin'));

// 配置项 - 请修改 data/config.ini 文件
$_CONFIG = parse_ini_file('data/config.ini');

define('IS_LOGIN', 
    $_CONFIG['AUTH_PASSWORD'] === $_SERVER['PHP_AUTH_PW'] && 
    $_CONFIG['AUTH_USERNAME'] === $_SERVER['PHP_AUTH_USER']);

preg_match('/'.str_replace('/', '\/', 
    $_CONFIG['REQUEST_URI_BASE']).'(\w+)\/*(\d*)\/*/i', $_SERVER["REQUEST_URI"], $match);
if (empty($match) || !$match[1]) {
    $match[1] = 'show';
}
$action = $match[1];

if ($action == 'delete' || $action == 'post' || ($_CONFIG['AUTH_OBTRUSION'] && $action == 'show')) {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="'.$_CONFIG['SITE_TITLE'].'"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    } else {
        if ($_CONFIG['AUTH_PASSWORD'] !== $_SERVER['PHP_AUTH_PW'] || 
            $_CONFIG['AUTH_USERNAME'] !== $_SERVER['PHP_AUTH_USER']) {
            exit;
        }
    }
}

if (!is_writeable($_CONFIG['SQLITE_DATABASE'])) {
    die('Database is not writeable.');
}
$Database = new PDO('sqlite:'.$_CONFIG['SQLITE_DATABASE']);


if (isset($_GET['id']) && !$extra = intval($_GET['id'])) {
    $extra = intval(isset($match[2]) ? $match[2] : 0);
}

switch($action) {
    case 'show':
        preg_match('/^(\w+)$/', $_GET['format'], $format);
        if (!strlen($format = $format[1])) {
            $format = $_CONFIG['DEFAULT_FORMAT'];
        }

        preg_match('/^(\d+)$/', $_GET['page'], $page);
        if (!$page = $page[1]) { $page = 1; }

        $sql = "SELECT id, data, _date FROM micro_blog ";
        if (isset($extra) && $extra) {
            $sql .= "WHERE id = {$extra} ";
        }
        $sql .= ' ORDER BY _date DESC';
        $sql .= ' LIMIT ' . intval($_CONFIG['PAGE_SIZE']) . ' OFFSET ' . intval(($page - 1) * $_CONFIG['PAGE_SIZE']);

        $stmt = $Database->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Creole_Wiki
        @include_once 'plugin/Creole_Wiki/Creole_Wiki.php';
        if (class_exists('Creole_Wiki')) {
            $Creole = new Creole_Wiki;
        }

        foreach ($result as $k => $item) {
            $result[$k]['data'] = isset($Creole) ? 
                $Creole->transform(trim($item['data'])) : '<p>'.nl2br(htmlspecialchars($item['data'])).'</p>';
            $result[$k]['date'] = date($_CONFIG['SITE_TIME_FORMAT'], $item['_date']);
        }
        include DIR_DATA.'/format/'.$format.'.inc';
        break;
    case 'delete':
        if (isset($extra) && $extra) {
            $result = $Database->exec("DELETE FROM micro_blog WHERE id = {$extra}");
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
