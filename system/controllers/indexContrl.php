<?php

namespace system\controllers;

use system\data\config;
use system\libs\system;
use system\models\indexModel;

if (!defined('IWEB')) { die("Error!"); }

class indexContrl
{

    static function index()
    {
        system::$site_title = config::$site_title;
        $online = indexModel::onlineList();
        $account = indexModel::accountList();
        $versions = array('client' => 0);
        if (config::$checkUpdate == "on") {
            $getVersion = @file_get_contents("http://pwserver.cn/versions.data");
            if (!empty($getVersion)) {
                $getVersion = explode("\n", $getVersion);
                foreach ($getVersion as $key => $val) {
                    $newVer = explode(":", $val);
                    $versions[$newVer[0]] = $newVer[1];
                }
            }
        }

        system::load("index");
        system::set("{status}", (indexModel::statusServer()) ? "开启": "关闭");
        system::set("{status_color}", (indexModel::statusServer()) ? "bg-success": "bg-danger");
        system::set("{online}", ($online['count']) ? $online['count'] : 0);
		//system::debug($online);
        system::set("{online_users}", $online['data']);
        system::set("{accounts}", ($account['count']) ? $account['count'] : 0);
        system::set("{accounts_list}", $account['data']);
        system::set("{iversion}", system::$version);
        system::set("{newVersion}", (floatval(system::$version) >= floatval($versions['client'])) ? "已更新到最新版" : "<span style='color: lime;'>发现了一个新版本: <b>".$versions['client']."</b></span>");
        system::show('content');
        system::clear();
    }

    static function login()
    {
        system::$site_title = "登陆";
        system::load("login");
        system::show('content');
        system::clear();
    }

    static function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['id']);
        header("Location: ".config::$site_adr);
    }

}