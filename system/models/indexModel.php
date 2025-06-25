<?php

namespace system\models;

use system\data\character\character;
use system\data\config;
use system\data\lang;
use system\libs\database;
use system\libs\GRole;
use system\libs\socket;
use system\libs\stream;
use system\libs\struct\GMRoleData;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class indexModel
{

    //work
    static function statusServer()
    {
        return (@fsockopen(config::$server_hostname, config::$linkPort)) ? true : false;
    }

    //work
    static function login($username, $password)
    {
        if ((boolean)preg_match("#^[aA-zZ0-9_]+$#", $username)) {
            if ((boolean)preg_match("#^[aA-zZ0-9_]+$#", $password)) {
                database::query("SELECT * FROM users WHERE name='" . database::safesql($username) . "' AND password='" . database::safesql(md5($password)) . "'");
                if (database::num() > 0) {
                    $user = database::assoc();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['user'] = $user['name'];
                    system::jms('reload', "");
                } else {
                    system::jms('danger', "输入错误的,没有正确的账号或密码!");
                }
            } else
                system::jms('danger', "用户名包含不正确的字符!");
        } else
            system::jms('danger', "密码不包含正确的符号！");
        database::clear();
    }

    //work
    static function onlineList()
    {
        $online = array();
        $onlineList['count'] = 0;
        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeOctets("");
        stream::pack(character::$pack['GMListOnlineUser']);
        stream::$readData = socket::sendPacket(5, socket::packInt(config::$gdeliverydPort) . stream::$writeData, 2048*2048);
        if (stream::$readData != "server:0") {
            stream::readCUint32();
            stream::$length = stream::readCUint32();
            $online = GRole::readData(character::$functions['GMListOnlineUser']);
            $onlineList['count'] = $online['count']['value'];
        }
        $onlineList['data'] = "";
        if ($onlineList['count'] > 0) {
            foreach ($online['users'] as $user) {
                $onlineList['data'] .= "    <tr>
        <td>{$user['userid']['value']}</td>
        <td>{$user['roleid']['value']}</td>
        <td>{$user['name']['value']}</td>
        <td><a class=\"badge badge-primary\" href='" . config::$site_adr . "/?controller=editor&page=xml&id={$user['roleid']['value']}'>XML</a>
            <a class=\"badge badge-success\" href='" . config::$site_adr . "/?controller=editor&id={$user['roleid']['value']}'>可视化</a>
            <a class=\"badge badge-warning\" href='" . config::$site_adr . "/?controller=server&page=mail&id={$user['roleid']['value']}'>发送邮件</a>
            <a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user['roleid']['value'] . ", 2)'>禁止发言</a>
            <a class=\"badge badge-light\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $user['roleid']['value'] . ", 1)'>禁止登录</a>
            <a class=\"badge badge-danger\" onclick='kickRole(" . $user['roleid']['value'] . ")' href='javascript:void(0)'>强制下线</a>
        </td>
    </tr>";
            }
        }
        return $onlineList;
    }

    //work
    static function accountList()
    {
        $account['count'] = 0;
        $account['data'] = "";
        $users = socket::sendPacket(59, "", 4096 * 4096, MSG_WAITALL);       
        $users = preg_replace("/passwd\":\"[\w\W\n\r]*?\",\"group/i","passwd\":\"\",\"group",$users);
        $users = explode("\n", trim($users));
        $usersList = json_decode($users[1], true);
        if ($users != "mysql:1") {
            $account['count'] = $users[0];
            if ($account['count'] > 0) {
                foreach ($usersList as $user) {
                    if ($user['group'] == "gm")
                        $group = "<span class='badge badge-danger'>" . lang::$user_group[$user['group']] . "</span>";
                    else
                        $group = "<span class='badge badge-success'>" . lang::$user_group[$user['group']] . "</span>";

                    $account['data'] .= "<tr>
                                    <td><small>{$user['id']}</small></td>
                                    <td><small>{$user['name']}</small></td>
                                    <td><small>{$user['email']}</small></td>
                                    <td><small>{$group}</small></td>
                                    <td><small>{$user['creatime']}</small></td>
                                    <td><small><a class=\"badge badge-success\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#getChar\" onclick='getChars(" . $user['id'] . ")'>角色列表</a>
                                     <a class=\"badge badge-primary\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#addCash\" onclick='addCash(" . $user['id'] . ")'>账号充值</a>
                                     <a class=\"badge badge-warning\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#editGM\" onclick='editGM(" . $user['id'] . ")'>权限管理</a>
                                     <a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#SetCashPassword\" onclick='SetCashPassword(" . $user['id'] . ")'>元宝交易密码</a>
                                     <a class=\"badge badge-danger\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#IWebAutolockSet\" onclick='IWebAutolockSet(" . $user['id'] . ")'>安全锁</a>
                               </small> </tr>";
                }
            }
        }
        return $account;
    }

    static function gm($data, $function = "add")
    {
        $error = false;
        if ($function == "add") {
            if (socket::sendPacket(60, socket::packString("DELETE FROM auth WHERE userid='{$data['id']}'")) == "mysql:7") {
                if (isset($data['params']) && count($data['params']) > 0) {
                    foreach ($data['params'] as $value) {
                        if (socket::sendPacket(60, socket::packString("INSERT INTO auth (userid, zoneid, rid) VALUES ('" . $data['id'] . "', '1', '" . $value . "')")) != "mysql:7") {
                            $error = true;
                        }
                    }
                    if (!$error) {
                        system::jms("success", "权限设置成功ID " . $data['id']);
                        system::log("修改GM账户权限ID " . $data['id']);
                    } else
                        system::jms("danger", "修改权限出错");
                } else
                    system::jms("success", "GM权限从帐户中删除ID " . $data['id']);
            } else
                system::jms("danger", "修改权限出错");

        } else if ($function == "check") {
            $result = socket::sendPacket(61, socket::packInt($data));
            if ($result != "mysql:0") {
                $permission = explode("\n", $result);
                $count = $permission[0];
                $permission = array_slice($permission, 1);
                if ($count > 0) {
                    echo json_encode($permission);
                } else
                    echo 0;
            }
        }
    }

    static function kickUser($role, $time = "1", $reason = "GM", $gm = 32)
    {
        stream::writeInt32($gm);
        stream::writeInt32(1);
        stream::writeInt32($role);
        stream::writeInt32($time);
        stream::writeString($reason);
        stream::pack(character::$pack['GMKickoutRole']);
        if (socket::sendPacket(4, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
            system::jms('success', "角色 $role 与服务器断开连接！");
            system::log("角色 $role 与服务器断开连接！");
        } else
            system::jms('error', "连接服务器时出错！");

    }

    static function addUser($data)
    {
        $username = strtolower(trim($data['username']));
        $password = strtolower(trim($data['password']));
        $email = strtolower(trim($data['email']));
        if (!empty($username)) {
            if (!empty($username)) {
                if ($data['type_password'] == "base64") {
                    $hashPassword = base64_encode(md5($username . $password, true));
                } elseif ($data['type_password'] == "md5") {
					$hashPassword = "0x".md5($username.$password);
                } else {
                    $hashPassword = $password;
                }

                if (socket::sendPacket(60, socket::packString("call adduser('$username', $hashPassword, '0', '0', '0', '0', '$email', '0', '0', '0', '0', '0', '0', '0', '', '', '$hashPassword')")) != "mysql:7")
					{
                    system::jms("danger", "无法创建账户！");
                } else {
                    system::jms("success", "帐户已创建");
                }
            } else {
                system::jms("danger", "请输入密码！");
            }
        } else {
            system::jms("danger", "请输入用户名！");
        }

//        for ($i = 1500; $i < 6000; $i++){
//            socket::sendPacket(60, socket::packString("call adduser('test_$i', 'test_$i', '0', '0', '0', '0', 'test_$i', '0', '0', '0', '0', '0', '0', '0', '', '', 'test_$i')"));
//        }
    }

    static function ChangePasswd($data)
    {
        $username2 = strtolower(trim($data['username2']));
        $password2 = strtolower(trim($data['password2']));
        if (!empty($username2)) {
            if (!empty($password2)) {
                if ($data['type_password2'] == "base64") {
                    $hashPassword2 = base64_encode(md5($username2 . $password2, true));
                } elseif ($data['type_password2'] == "md5") {
					$hashPassword2 = "0x".md5($username2.$password2);
                } else {
                    $hashPassword2 = $password2;
                }

                if (socket::sendPacket(60, socket::packString("call changepasswd ('$username2', $hashPassword2)")) != "mysql:7")
					{
                    system::jms("danger", "无法修改密码！");
                } else {
                    socket::sendPacket(60, socket::packString("update users set passwd2='$password2' where name='$username2'"));
                    system::jms("success", "修改成功");
                }
            } else {
                system::jms("danger", "请输入密码！");
            }
        } else {
            system::jms("danger", "请输入账号");
        }
    }

    static function addZoneidCash($data)
    {
        $zoneid = strtolower(trim($data['Zoneid']));
        $name = strtolower(trim($data['Username']));
        $count = strtolower(trim($data['Cashcount']));
        $date = date("Y-m-d H:i:s");

        // 查询用户 ID
        $users = socket::sendPacket(59, "", 4096 * 4096, MSG_WAITALL);
        $users = preg_replace("/passwd\":\"[\w\W\n\r]*?\",\"group/i","passwd\":\"\",\"group",$users);
        $users = explode("\n", trim($users));
        $usersList = json_decode($users[1], true);
        if ($users!= "mysql:1") {
            $userid = null;
            foreach ($usersList as $user) {
                if ($user['name'] === $name) {
                    $userid = $user['id'];
                    break;
                }
            }
        }
        if (!empty($userid)) {
            if (!empty($count)) {
                if (socket::sendPacket(60, socket::packString("insert into usecashnow (userid, zoneid, sn, aid, point, cash, status, creatime) values ('$userid', '$zoneid', '0', '$zoneid', '0', '$count', '1', '$date')")) != "mysql:7") {
                    system::jms("danger", "无法充值 $user_id");
                } else {
                    system::jms("success", "充值成功");
                }
            } else {
                system::jms("danger", "请输金额！");
            }
        } else {
            system::jms("danger", "没有此账号");
        }
    }

}