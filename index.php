<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mb_internal_encoding("UTF-8");

session_start();

define("dir", dirname(__FILE__));
define('IWEB', true);

if (file_exists(dir . "/system/data/config.php") or file_exists(dir . "/system/data/dbconfig.php")) {
    require_once dir . "/system/init.php";
    if (!file_exists(dir . "/install.php")) {
        \system\libs\system::run();
    } else {
        \system\libs\system::debug("删除文件 /install.php");
    }

} else {
    header("Location: install.php");
}


