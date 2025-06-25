<?php
namespace system\data;
if (!defined('IWEB')) {die("Error!");}
class config
{
    static $site_title = "IWEB";
    static $site_adr = "http://localhost/iweb";
    static $site_lang = "CN";
    static $checkUpdate = "on";
    static $widgetChat = "off";
    static $countChatMsg = "50";
    static $logActions = "on";
    static $access = "off";
    static $accessIP = "";
    static $titleMail = "GM";
    static $messageMail = "Email";
    static $version = "1.5.5_156";
    static $dbPort = "29400";
    static $gdeliverydPort = "29100";
    static $GProviderPort = "29300";
    static $linkPort = "29000";
    static $serverPath = "/root/pwserver";
    static $logsPath = "/root/pwserver/logs";
    static $serverTypeAuth = "gauthd";
    static $chatFile = "world2.chat";
    static $server_hostname = "127.0.0.1";
    static $server_port = "65535";
    static $server_key = "kTtOdZRGJzrl3oIxrwDdiDOfolnmsIuNCX";
}