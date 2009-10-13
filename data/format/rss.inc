<?php // vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix filetype=xml fenc=utf8 nobomb:
header('Content-type: application/rss+xml;charset=utf-8');
?>
<rss version="2.0">
  <channel>
    <title>Ghoul - <?php echo $_CONFIG['SITE_TITLE'];?></title>
    <generator>$Id$</generator>
        <?php 
            foreach ($result as $item) {
                echo sprintf("<item>
                    <title>%s</title>
                    <description><![CDATA[%s]]></description>
                    <pubDate>%s</pubDate></item>",
                    mb_substr(strip_tags($item['data']), 0, 10, 'utf-8') . '...',
                    $item['data'],
                    date('r', $item['_date'])
                );
            }
        ?>
  </channel>
</rss>
