<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix filetype=html fenc=utf8 nobomb:
header('Content-type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="icon" type="image/x-icon" href="http://www.gracecode.com/favicon.ico" />
        <title>Ghoul - <?php echo $_CONFIG['SITE_TITLE'];?></title>
        <link href="?format=rss" type="application/rss+xml" rel="alternate" title="Sitewide RSS Feed" />
        <link rel="stylesheet" href="http://a.tbcdn.cn/tbra/2.0/reset-grids.css" type="text/css" media="screen" />
        <style type="text/css"><?php include 'data/micro.css'; ?></style>
    </head>
    <body>
        <div id="page">
        <h1><?php echo $_CONFIG['SITE_TITLE'];?></h1>
        <ul id="show" current:page="<?php echo $page; ?>">
        <?php 
            foreach ($result as $item) {
                printf('<li id="micro_%d"><span
                    class="date">%s</span>%s</li>',
                    $item['id'],
                    $item['date'],
                    $item['data']
                );
            }
        ?>
        </ul>
        <div id="ver">$Id$</div>
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>
        <?php if (IS_LOGIN) { ?>
            <script type="text/javascript"><?php include 'data/micro.js'; ?></script>
        <?php } ?>
    </body>
</html>
