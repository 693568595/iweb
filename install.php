<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$

session_start();
define('dir', dirname(__FILE__));
header("Content-Type: text/html; charset=utf-8");
$url = explode("/install.php", strtolower($_SERVER['PHP_SELF']));
$url = reset($url);
if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) $protocol = "https://";
else $protocol = "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $url;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>安装后台</title>
    <link rel='shortcut icon' type='image/x-icon' href='<?php
echo $url; ?>/system/template/images/favicon.ico'/>
    <link rel="stylesheet" href="<?php
echo $url; ?>/system/template/css/bootstrap.min.css">
    <style>
        body {
            background: #1c2326;
            /*background: url("padded.png");*/
            /*background-size: cover;*/
        }

        .content {
            background: #272f32;
            width: 600px;
            margin: 0 auto;
            margin-top: 30px;
            color: #c2c2c2;
            font-size: 12px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .next {
            position: relative;
            margin-bottom: 10px;
        }

        .container-fluid input[type=text] {
            padding-bottom: 5px;
            background: #1f2426;
            border: 1px solid #161a1c;
            color: white;
        }

        .container-fluid input[type=text]:focus {
            background: #192023;
            border: 1px solid #161a1c;
            color: white;
        }

        .container-fluid input[type=password] {
            padding-bottom: 5px;
            background: #1f2426;
            border: 1px solid #161a1c;
            color: white;
        }

        .container-fluid input[type=password]:focus {
            background: #192023;
            border: 1px solid #161a1c;
            color: white;
        }

        .container-fluid select {
            padding-bottom: 5px;
            background: #1f2426;
            border: 1px solid #161a1c;
            color: white;
        }

        .container-fluid select:focus {
            background: #192023;
            border: 1px solid #161a1c;
            color: white;
        }
    </style>
</head>
<body>
<div class="container-fluid">

    <div class="row">


        <?php
$get = (isset($_GET['step'])) ? $_GET['step'] : "";
switch ($get) {
    case 1:
        $sd = (is_writable(dir . "/system/data")) ? "<span style='color: green'>允许</span>" : "<span style='color: red'>禁止</span>";
?>
                <div class="content">
                    <h6 class="text-center pt-2">读写权限检查</h6>
                    <br>
                    <br>
                    <table class="table small table-dark">
                        <thead>
                        <tr>
                            <th scope="col" width="60%">文件夹或文件</th>
                            <th scope="col" width="20%">执行权限</th>
                            <th scope="col" width="20%">写入状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row">/system/data</th>
                            <td><?php
        echo substr(decoct(fileperms(dir . "/system/data")) , 2); ?> </td>
                            <td><?php
        echo $sd; ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    检查状态:<br>
                    安装脚本 <?php
        echo $sd; ?>
                    <br>
                    <?php
        if (is_writable(dir . "/system/data")) { ?>
                        <a href="<?php
            echo $url; ?>/install.php?step=2"
                           class="next btn btn-sm btn-success float-right mr-3">下一步</a>
                    <?php
        } else { ?>
                        设置权限 <b>777</b> 于文件夹 <b>/system/data</b><br>
                        <br>
                        <span style="color: red; font-weight: 700;">要继续安装，请更正错误！</span>
                    <?php
        } ?>
                </div>
                <?php
        break;

    case 2:
?>
                <div class="content">
                    <h6 class="text-center pt-2">初始设置</h6>
                    <br>
                    <br>
                    <form action="<?php
        echo $url; ?>/install.php?step=3" method="post">
                        语言： 
						<select class="form-control form-control-sm" 
							name="config[site_lang]">
							<option value="CN">中文</option>
                            <option value="RU">RU</option>
                        </select><br>
                        游戏服务器IP： 
						<input class="form-control form-control-sm" type="text"
							name="config[server_hostname]"
							value="127.0.0.1"><br>
                        服务器版本： 
						<select class="form-control form-control-sm" 
							name="config[version]">
							<option value="1.5.5_156">1.5.5 (156)</option>
                            <option value="1.5.1_101">1.5.1 (101)</option>
                            <option value="1.5.3_145">1.5.3 (145)</option>
                        </select><br>
                        进程名称： 
						<select class="form-control form-control-sm" type="text" 
							name="config[serverTypeAuth]">
							<option value="authd">authd</option>
                            <option value="auth">auth</option>
                            <option value="gauthd">gauthd</option>
                        </select><br>
                        Gamedbd 端口： 
						<input class="form-control form-control-sm" type="text"
							name="config[dbPort]"
                            value="29400"><br>
                        GProvider 端口： 
						<input class="form-control form-control-sm" type="text" 
							name="config[GProviderPort]"
                            value="29300"><br>
                        GDeliveryd 端口： 
						<input class="form-control form-control-sm" type="text"
							name="config[gdeliverydPort]"
							value="29100"><br>
                        Glink 端口： 
						<input class="form-control form-control-sm" type="text" 
							name="config[linkPort]"
                            value="29000"><br>

                        服务端目录路径： 
						<input class="form-control form-control-sm" type="text" 
							name="config[serverPath]"
                            value="/root/pwserver"><br>
                        日志目录路径： 
						<input class="form-control form-control-sm" type="text" 
							name="config[logsPath]"
                            value="/root/pwserver/logs"><br>
                        聊天插件： 
						<select class="form-control form-control-sm" type="text" 
							name="config[widgetChat]">
							<option value="off">关闭</option>
                            <option value="on">开启</option>
						</select><br>
                        聊天消息计数： 
						<input class="form-control form-control-sm" type="text" 
							name="config[countChatMsg]"
							value="50"><br>
                        检查更新： 
						<select class="form-control form-control-sm" type="text" 
							name="config[checkUpdate]">
							<option value="on">开启</option>
							<option value="off">关闭</option>
						</select><br>
                        <br>
                        <button type="submit" name="step2"
                                class="next btn btn-sm btn-success float-right mr-3">下一步
                        </button>
                    </form>
                </div>
                <?php
        break;

    case 3:
        $msgConfig = "";
        if (isset($_POST['step2'])) {
            function generateRandomString($length = 34) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString.= $characters[rand(0, $charactersLength - 1) ];
                }
                return $randomString;
            }
            $_SESSION['conf']['port'] = rand(65535, 65535);
            $_SESSION['conf']['key'] = generateRandomString();
            $configFile = '<?php
namespace system\data;
if (!defined(\'IWEB\')) {die("Error!");}
class config
{
    static $site_title = "IWEB";
    static $site_adr = "' . $url . '";
    static $site_lang = "' . $_POST['config']['site_lang'] . '";
    static $checkUpdate = "' . $_POST['config']['checkUpdate'] . '";
    static $widgetChat = "' . $_POST['config']['widgetChat'] . '";
    static $countChatMsg = "' . $_POST['config']['countChatMsg'] . '";
    static $logActions = "on";
    static $access = "off";
    static $accessIP = "";
    static $titleMail = "GM";
    static $messageMail = "Email";
    static $version = "' . $_POST['config']['version'] . '";
    static $dbPort = "' . $_POST['config']['dbPort'] . '";
    static $gdeliverydPort = "' . $_POST['config']['gdeliverydPort'] . '";
    static $GProviderPort = "' . $_POST['config']['GProviderPort'] . '";
    static $linkPort = "' . $_POST['config']['linkPort'] . '";
    static $serverPath = "' . $_POST['config']['serverPath'] . '";
    static $logsPath = "' . $_POST['config']['logsPath'] . '";
    static $serverTypeAuth = "' . $_POST['config']['serverTypeAuth'] . '";
    static $chatFile = "world2.chat";
    static $server_hostname = "' . $_POST['config']['server_hostname'] . '";
    static $server_port = "' . $_SESSION['conf']['port'] . '";
    static $server_key = "' . $_SESSION['conf']['key'] . '";
}';
            $fw = fopen(dir . "/system/data/config.php", "w");
            if (!fwrite($fw, $configFile)) {
                $msgConfig = "无法将设置写入文件！";
            }
            fclose($fw);
        }
?>
                <div class="content">
                    <h6 class="text-center pt-2">数据库设置</h6>
                    <br>
                    <br>
                    <?php
        if (empty($msgConfig)) { ?>
                        <form action="<?php
            echo $url; ?>/install.php?step=4" method="post">
                            数据库地址： <input class="form-control form-control-sm" type="text" name="dbconfig[host]"
                                               value="localhost"><br>
                            数据库帐号： <input class="form-control form-control-sm" type="text"
                                                     name="dbconfig[user]"
                                                     value="root"><br>
                            数据库密码： <input class="form-control form-control-sm" type="password"
                                           name="dbconfig[password]"
                                           value="123456"><br>
                            数据库名称： <input class="form-control form-control-sm" type="text"
                                                    name="dbconfig[table]"
                                                    value="iweb"><br>
                            <br>
                            <button type="submit" name="step3"
                                    class="next btn btn-sm btn-success float-right mr-3">继续安装
                            </button>
                        </form>
                    <?php
        } else {
            echo $msgConfig;
        }
?>
                    <br>


                </div>
                <?php
        break;

    case 4:
        if (isset($_POST['step3'])) {
            $configFile = '<?php
namespace system\data;
if (!defined(\'IWEB\')) {die("Error!");}
class dbconfig
{
    static $host = "' . $_POST['dbconfig']['host'] . '";
    static $user = "' . $_POST['dbconfig']['user'] . '";
    static $password = "' . $_POST['dbconfig']['password'] . '";
    static $table = "' . $_POST['dbconfig']['table'] . '";
    static $charset = "utf8";
}';
            $_SESSION['db']['host'] = $_POST['dbconfig']['host'];
            $_SESSION['db']['user'] = $_POST['dbconfig']['user'];
            $_SESSION['db']['password'] = $_POST['dbconfig']['password'];
            $_SESSION['db']['table'] = $_POST['dbconfig']['table'];
            $fw = fopen(dir . "/system/data/dbconfig.php", "w");
            if (!fwrite($fw, $configFile)) {
                $msgConfig = "无法将设置写入文件！";
            }
            fclose($fw);
            $msgDB = '';
            $db = @new mysqli($_SESSION['db']['host'], $_SESSION['db']['user'], $_SESSION['db']['password']);
            if ($db) {
                if (!$db->query("USE " . $_SESSION['db']['table'])) {
                    if ($db->query("CREATE DATABASE " . $_SESSION['db']['table'])) {
                        $db->query("USE " . $_SESSION['db']['table']);
                        $db->set_charset("utf8");
                        $db->query("CREATE TABLE `groups` (
                                                  `id_group` int(11) NOT NULL,
                                                  `title` varchar(255) DEFAULT NULL,
                                                  `xml_edit` int(1) DEFAULT NULL,
                                                  `visual_edit` int(1) DEFAULT NULL,
                                                  `gm_manager` int(1) DEFAULT NULL,
                                                  `kick_role` int(1) DEFAULT NULL,
                                                  `ban` int(1) DEFAULT NULL,
                                                  `add_gold` int(1) DEFAULT NULL,
                                                  `level_up` int(1) DEFAULT NULL,
                                                  `rename_role` int(1) DEFAULT NULL,
                                                  `teleport` int(1) DEFAULT NULL,
                                                  `null_exp_sp` int(1) DEFAULT NULL,
												  `null_passwd` int(1) DEFAULT NULL,
                                                  `del_role` int(1) DEFAULT NULL,
                                                  `server_manager` int(1) DEFAULT NULL,
                                                  `send_msg` int(1) DEFAULT NULL,
                                                  `send_mail` int(1) DEFAULT NULL,
                                                  `settings` int(1) DEFAULT NULL,
                                                  `logs` int(1) DEFAULT NULL
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                        $db->query("INSERT INTO `groups` (`id_group`, `title`, `xml_edit`, `visual_edit`, `gm_manager`, `kick_role`, `ban`, `add_gold`, `level_up`, `rename_role`, `teleport`, `null_exp_sp`, `null_passwd`, `del_role`, `server_manager`, `send_msg`, `send_mail`, `settings`, `logs`) VALUES
(1, 'administrator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);");
                        $db->query("CREATE TABLE `logs` (
                                                  `id` int(11) NOT NULL,
                                                  `ip` varchar(255) NOT NULL,
                                                  `date` int(11) NOT NULL,
                                                  `user` varchar(255) NOT NULL,
                                                  `action` text NOT NULL
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                        $db->query("CREATE TABLE `mail` (
                                                  `idMail` int(11) NOT NULL,
                                                  `titleItem` text NOT NULL,
                                                  `messageItem` text NOT NULL,
                                                  `idItem` int(11) NOT NULL,
                                                  `countItem` int(11) NOT NULL,
                                                  `maxCountItem` int(11) NOT NULL,
                                                  `octetItem` text NOT NULL,
                                                  `prototypeItem` int(11) NOT NULL,
                                                  `timeItem` int(11) NOT NULL,
                                                  `maskItem` int(11) NOT NULL,
                                                  `moneyItem` int(11) NOT NULL
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                        $db->query("CREATE TABLE `users` (
                                                  `id` int(11) NOT NULL,
                                                  `name` varchar(255) NOT NULL,
                                                  `password` varchar(255) NOT NULL,
                                                  `group_id` int(11) NOT NULL
                                                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                        $db->query("ALTER TABLE `groups` ADD PRIMARY KEY (`id_group`);");
                        $db->query("ALTER TABLE `logs` ADD PRIMARY KEY (`id`);");
                        $db->query("ALTER TABLE `mail` ADD PRIMARY KEY (`idMail`);");
                        $db->query("ALTER TABLE `users` ADD PRIMARY KEY (`id`);");
                        $db->query("ALTER TABLE `groups` MODIFY `id_group` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;");
                        $db->query("ALTER TABLE `logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;");
                        $db->query("ALTER TABLE `mail` MODIFY `idMail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;");
                        $db->query("ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;");
                        //$db->query("");
                        
                    } else {
                        $msgDB = "无法创建数据库: <b>{$_SESSION['db']['table']}</b>.";
                    }
                } else {
                    $db->close();
                }
            } else {
                $msgDB = "连接数据库失败！";
            }
            $db->close();
        }
?>
                <div class="content">
                    <h6 class="text-center pt-2">添加管理员</h6>
                    <br>
                    <br>
                    <?php
        if (empty($msgDB)) { ?>
                    <form action="<?php
            echo $url; ?>/install.php?step=5" method="post">
                        账号： <input class="form-control form-control-sm" type="text"
                                                 name="user"
                                                 value="admin"><br>
                        密码： <input class="form-control form-control-sm" type="password"
                                       name="password"
                                       value="admin"><br>
                        <br>
                        <button type="submit" name="step4"
                                class="next btn btn-sm btn-success float-right mr-3">下一步
                        </button>
                    </form>
                    <?php
        } else {
            echo $msgDB;
        } ?>
                </div>
                <?php
        break;

    case 5:
        if (isset($_POST['step4'])) {
            $msgDB = '';
            $db = @new mysqli($_SESSION['db']['host'], $_SESSION['db']['user'], $_SESSION['db']['password'], $_SESSION['db']['table']);
            if ($db) {
                if ($db->query("INSERT INTO `users` (`id`, `name`, `password`, `group_id`) VALUES
(NULL, '" . $_POST['user'] . "', '" . md5($_POST['password']) . "', 1);")) {
                } else {
                    $msgDB = "无法创建帐户！";
                }
            } else {
                $msgDB = "连接数据库失败！";
            }
        }
?>
                <div class="content">
                    <h6 class="text-center pt-2">安装完成</h6>
                    <br>
                    <br>
                    <?php
        if (empty($msgDB)) { ?>
                        安装成功！<br>
                        现在更改服务器设置并运行它。<br>
						请将以下信息配置到IWEB服务配置文件中<br>
                        端口： <b><?php
            echo $_SESSION['conf']['port']; ?></b><br>
                        密钥： <b><?php
            echo $_SESSION['conf']['key']; ?></b><br>
                        <a href="<?php
            echo $url; ?>"
                           class="next btn btn-sm btn-success float-right mr-3">完成</a>
                    <?php
        } else {
            echo $msgDB;
        } ?>

                </div>
                <?php
        break;

    default:
?>
                <div class="content">
                    <h6 class="text-center pt-2">IWEB快速安装脚本</h6>
                    <br>
                    <br>
                    欢迎使用IWEB安装脚本. <br>这个脚本在几分钟内会帮助你建立一个IWEB管理系统。<br>
                    但是，如果IWEB的工作或安装出现错误，我们强烈建议您与我联系。
                    <br><br>
                    在开始安装之前，请确保将所有IWEB文件上传到服务器。
                    <br><br>
                    <hr>
                    <a href="<?php
        echo $url; ?>/install.php?step=1"
                       class="next btn btn-sm btn-success float-right mr-3">开始安装</a>

                </div>
                <?php
        break;
    }
?>
    </div>

</div>
<script href="<?php
    echo $url; ?>/system/template/js/jquery-3.2.1.min.js"></script>
<script href="<?php
    echo $url; ?>/system/template/js/bootstrap.min.js"></script>
</body>
</html>