<?php

if (!defined('IWEB')) {
    die("Error!");
}

// 确保包含 config.php 文件
require_once dir . '/system/data/config.php';

include dir . "/system/data/lang/".\system\data\config::$site_lang."/site.php";

// 使用 spl_autoload_register() 替代 __autoload()
spl_autoload_register(function ($name) {
    $path = dir . DIRECTORY_SEPARATOR . str_replace("\\", "/", $name) . ".php";

    if (file_exists($path))
        include $path;
    //else
    // \system\libs\system::debug("Error include file: $name");
});

if (file_exists(dir . "/system/data/config.php"))
    require_once dir . "/system/data/character/" . \system\data\config::$version . "/character.php";