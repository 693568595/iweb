<?php

namespace system\controllers;

use system\data\config;
use system\data\lang;
use system\libs\database;
use system\libs\func;
use system\libs\socket;
use system\libs\system;
use system\models\settingsModel;

if (!defined('IWEB')) {
    die("Error!");
}

class settingsContrl
{

    //work
    static function index()
    {
        system::$site_title = "系统设置";
        if (system::$user['settings']) {

            // $items = database::query("SELECT * FROM items");

            if (file_exists(dir . "/system/data/elements.data"))
                $statusElement = "<span style='color: green'>已找到</span>";
            else $statusElement = "<span style='color: red'>未找到</span>";

            if (file_exists(dir . "/system/data/iconlist_ivtrm.png"))
                $statusPngIcons = "<span style='color: green'>已找到</span>";
            else $statusPngIcons = "<span style='color: red'>未找到</span>";

            if (file_exists(dir . "/system/data/iconlist_ivtrm.txt"))
                $statusTxtIcons = "<span style='color: green'>已找到</span>";
            else $statusTxtIcons = "<span style='color: red'>未找到</span>";
            $fileElement = @file_get_contents(dir . "/system/data/items.json");
			$fileIcon = @file_get_contents(dir . "/system/data/icons.json");
            $itemArray = @json_decode(base64_decode(gzinflate($fileElement)), true);
            $iconArray= @json_decode(base64_decode(gzinflate($fileIcon)),true);



            system::load("settings");
            system::set("{items_count}", count($itemArray));
            system::set("{icons_count}", count($iconArray));
            system::set("{status_element}", $statusElement);
            system::set("{status_png_icons}", $statusPngIcons);
            system::set("{status_txt_icons}", $statusTxtIcons);

            //settings params

            //main
            system::set("{main_site_title}", config::$site_title);
            system::set("{main_site_adr}", config::$site_adr);
            system::set("{main_site_lang}", settingsModel::selectSettings("site_lang", config::$site_lang, array("CN" => "中文", "EN" => "英文", "RU" => "俄文")));
            system::set("{main_countChatMsg}", config::$countChatMsg);
            system::set("{main_checkUpdate}", settingsModel::selectSettings("checkUpdate", config::$checkUpdate, array("on" => "开启", "off" => "关闭")));
            system::set("{main_widgetChat}", settingsModel::selectSettings("widgetChat", config::$widgetChat, array("on" => "开启", "off" => "关闭")));
            system::set("{main_logActions}", settingsModel::selectSettings("logActions", config::$logActions, array("on" => "开启", "off" => "关闭")));
            system::set("{main_access}", settingsModel::selectSettings("access", config::$access, array("on" => "开启", "off" => "关闭")));
            system::set("{main_accessIP}", config::$accessIP);

            //pw
            system::set("{pw_titleMail}", config::$titleMail);
            system::set("{pw_messageMail}", config::$messageMail);
            system::set("{pw_version}", config::$version);
            system::set("{pw_version}", settingsModel::selectSettings("version", config::$version, settingsModel::listDir(dir . "/system/data/character")));
            system::set("{pw_dbPort}", config::$dbPort);
            system::set("{pw_gdeliverydPort}", config::$gdeliverydPort);
            system::set("{pw_GProviderPort}", config::$GProviderPort);
            system::set("{pw_linkPort}", config::$linkPort);
            system::set("{pw_serverPath}", config::$serverPath);
            system::set("{pw_serverTypeAuth}",settingsModel::selectSettings("serverTypeAuth", config::$serverTypeAuth, array("authd" => "authd", "auth" => "auth", "gauthd" => "gauthd")));
			system::set("{pw_logsPath}", config::$logsPath);
            system::set("{pw_chatFile}", config::$chatFile);

            //iweb
            system::set("{iweb_server_hostname}", config::$server_hostname);
            system::set("{iweb_server_port}", config::$server_port);
            system::set("{iweb_server_key}", config::$server_key);


            system::show("content");
            system::clear();
        } else system::info("没有权限,请登录");
    }

    //work
    static function logs()
    {
        system::$site_title = "操作日志";

        if (system::$user['logs'] == true) {
            $logs = "";
            if ($getLogs = settingsModel::getLogs()) {
                foreach ($getLogs as $key => $log) {
                    $logs .= "<tr>";
                    $logs .= "<td>" . date("d.m.y / H:i", $log['date']) . "</td>";
                    $logs .= "<td>{$log['ip']}</td>";
                    $logs .= "<td>{$log['user']}</td>";
                    $logs .= "<td>{$log['action']}</td>";
                    $logs .= "</tr>";
                }
            }

            system::load('logs');
            system::set("{logs}", $logs);
            system::show('content');
            system::clear();
        } else system::info("无法访问", "你没有这个权限");

    }

    static function users()
    {

        //  $configGS = func::readConf("/home/gamed/ptemplate.conf");
//        system::debug($configPTemplate);
//        $configGS['GENERAL']['exp_bonus'] = 10000;
//        $configGS['GENERAL']['sp_bonus'] = 10000;
//        $configGS['GENERAL']['drop_bonus'] = 10000;
//        $configGS['GENERAL']['money_bonus'] = 10000;
        //  func::writeConf("/home/gamed/ptemplate.conf",$configGS);


        system::$site_title = "管理员";
        if (system::$user['settings']) {
            $userList = "";
            $users = database::query("SELECT users.*, groups.title FROM users, groups WHERE groups.id_group = users.group_id");
            $groups = database::query("SELECT * FROM groups");
            system::load("users");
            system::foreachTpl("users", $users, "user_");
            system::foreachTpl("groupsList", $groups, "users_");
            system::set("{userlist}", $userList);
            system::show("content");
            system::clear();
        } else system::info("无法访问", "你没有这个权限");

    }

    static function groups()
    {
        system::$site_title = "用户组";
        if (system::$user['settings']) {

            system::load("groups");

            foreach (lang::$template['groupsRoles'] as $key => $value) {
                system::set("{lang_$key}", $value);
            }

            foreach (lang::$template['groups'] as $key => $value) {
                system::set("{lang_$key}", $value);
            }

            $groups = database::query("SELECT * FROM groups");
            $replace = array(0 => "<span style='color: lightcoral'>{%%}</span>", 1 => "<span style='color: lightgreen'>{%%}</span>");
            system::foreachTpl("groups", $groups, "group_", array(0 => lang::$template['forbidden'], 1 => lang::$template['allowed']), $replace);
            system::show("content");
            system::clear();
        } else system::info("无法访问", "你没有这个权限");

    }

}