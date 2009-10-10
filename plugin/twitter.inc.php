<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8 nobomb:
/**
 * Ghoul - Simple MiniBlog
 *      同步到 Twitter 插件
 *
 * @author mingcheng<i.feelinglucky@gmail.com>
 * @date   2009-10-10
 * @link   http://www.gracecode.com/
 */

define('TWITTER_POSTURL',  'http://twitter.com/statuses/update.xml');

if ('post' == $action && $_CONFIG['TWITTER_USERNAME'] && $_CONFIG['TWITTER_PASSWORD']) {
    $message = trim($_POST['content']);
    if (strlen($message)) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, TWITTER_POSTURL);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_POST, 1);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, sprintf("status=%s", $message));
        curl_setopt($curl_handle, CURLOPT_USERPWD, sprintf("%s:%s", $_CONFIG['TWITTER_USERNAME'], $_CONFIG['TWITTER_PASSWORD']));
        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);
    }
}
