<?php

namespace system\models;

use system\data\character\character;
use system\data\config;
use system\data\server;
use system\libs\database;
use system\libs\func;
use system\libs\GRole;
use system\libs\socket;
use system\libs\stream;
use system\libs\struct\GMRoleData;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class serverModel
{

    public static $auth;
    public static $gacd;
    public static $gamedb;
    public static $gdeliveryd;
    public static $gfactiond;
    public static $glinkd1;
    public static $logservice;
    public static $uniquenamed;
    public static $gs;

    static function sendChatMessage($msg, $chanel)
    {
        if (!empty($msg)) {
            stream::writeByte($chanel);
            stream::writeByte(0);
            stream::writeInt32(0);
            stream::writeString($msg);
            stream::writeOctets("");
            stream::pack(character::$pack['ChatBroadCast']);

            if (socket::sendPacket(3, socket::packInt(29300) . stream::$writeData) != "server:0") {
                system::jms("success", "消息已发送");
                system::log("发送消息");
            } else
                system::jms("danger", "发送消息时出错");
        } else
            system::jms("info", "请输入消息内容");
    }

    static function GMRestartServer($restart_time)
    {
        if (!empty($restart_time) && is_numeric($restart_time)) {
            stream::writeInt32(-1);
            stream::writeInt32(0);
            stream::writeInt32(0);
            stream::writeInt32($restart_time);
            stream::pack(character::$pack['GMRestartServer']);
            if (socket::sendPacket(3, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                system::jms("success", "倒计时 $restart_time 秒关闭服务器");
                system::log("倒计时 $restart_time 秒关闭服务器");
            } else
                system::jms("danger", "倒计时关闭服务器出错");
        } else
            system::jms("info", "请输入时间");
    }

    //增加服务器禁用和多倍功能
    static function GMOpenGameAttri($OpenGameAttri, $value)
    {
        // 关联数组映射选项值和文字描述
        $attributeDescriptions = array(
            204 => "双倍经验",
            213 => "双倍元神",
            211 => "双倍金币",
            212 => "双倍掉落",
            207 => "禁止交易操作",
            208 => "禁止拍卖操作",
            209 => "禁止邮件操作",
            210 => "禁止帮派操作",
            214 => "禁止点卡操作"
        );
        $value = pack("C*", $value);
        if (!empty($OpenGameAttri)) {
            stream::writeInt32(-1);
            stream::writeInt32(-1);
            stream::writeInt32(-1);
            stream::writeByte($OpenGameAttri);
            stream::writeOctets($value);
            stream::pack(character::$pack['GMSetGameAttri']);

            // 获取选项值对应的文字描述
            if (isset($attributeDescriptions[$OpenGameAttri])) {
                $attributeDescription = $attributeDescriptions[$OpenGameAttri];
            } else {
                $attributeDescription = "未知属性";
            }

            if (socket::sendPacket(3, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                system::jms("success", "开启 $attributeDescription 成功");
                system::log("开启 $attributeDescription");
            } else
                system::jms("danger", "服务器设置出错");
        } else
            system::jms("info", "请选择设置参数");
    }

    static function GMCloseGameAttri($OpenGameAttri, $value)
    {
        // 关联数组映射选项值和文字描述
        $attributeDescriptions = array(
            204 => "双倍经验",
            213 => "双倍元神",
            211 => "双倍金币",
            212 => "双倍掉落",
            207 => "禁止交易操作",
            208 => "禁止拍卖操作",
            209 => "禁止邮件操作",
            210 => "禁止帮派操作",
            214 => "禁止点卡操作"
        );
        $value = pack("C*", $value);
        if (!empty($OpenGameAttri)) {
            stream::writeInt32(-1);
            stream::writeInt32(-1);
            stream::writeInt32(-1);
            stream::writeByte($OpenGameAttri);
            stream::writeOctets($value);
            stream::pack(character::$pack['GMSetGameAttri']);

            // 获取选项值对应的文字描述
            if (isset($attributeDescriptions[$OpenGameAttri])) {
                $attributeDescription = $attributeDescriptions[$OpenGameAttri];
            } else {
                $attributeDescription = "未知属性";
            }

            if (socket::sendPacket(3, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                system::jms("success", "关闭 $attributeDescription 成功");
                system::log("关闭 $attributeDescription");
            } else
                system::jms("danger", "服务器设置出错");
        } else
            system::jms("info", "请选择设置参数");
    }

    static function statusServer()
    {
        $datas = socket::sendPacket(57, socket::packString("/proc/meminfo"));

        $dataMemFile = explode("\n", $datas);
        $data = array();
        foreach ($dataMemFile as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", str_replace(" kB", "", $line));
                $data[$key] = intval((int)trim($val) / 1024);
            }
        }
        return $data;
    }

    static function mail($data, $online = false)
    {
        if (!empty($data['idChar']) && is_numeric($data['idChar'])) {
            stream::writeInt32(344);
            stream::writeInt32(32);
            stream::writeByte(3);
            stream::writeInt32($data['idChar']);
            stream::writeString($data['titleItem']);
            stream::writeString($data['messageItem']);
            stream::writeInt32($data['idItem']);
            stream::writeInt32(0);
            stream::writeInt32($data['countItem']);
            stream::writeInt32($data['maxCountItem']);
            stream::writeOctets($data['octetItem'], true);
            stream::writeInt32($data['prototypeItem']);
            stream::writeInt32($data['timeItem']);
            stream::writeInt32(0);
            stream::writeInt32(0);
            stream::writeInt32($data['maskItem']);
            stream::writeInt32($data['moneyItem']);
            stream::pack(character::$pack['SysSendMail']);
            if (socket::sendPacket(3, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                if (!$online) {
                    system::jms("success", "发送邮件给角色 " . $data['idChar']);
                    system::log("发送邮件到 " . $data['idChar'] . ", 物品: " . $data['idItem'] . " 数量: " . $data['countItem']);
                }// else return true;
                if (database::query("SELECT * FROM mail WHERE idItem='" . database::safesql($data['idItem']) . "'")) {
                    if (database::num() == 0) {
                        $insertKey = "";
                        $insertValue = "";
                        foreach ($data as $key => $value) {
                            if ($key != "idChar") {
                                $insertKey .= "$key,";
                                $insertValue .= "'" . database::safesql($value) . "',";
                            }
                        }
                        database::query("INSERT INTO mail (" . rtrim($insertKey, ",") . ") VALUES (" . rtrim($insertValue, ",") . ")");
                    } else {
                        $update = "";
                        foreach ($data as $key => $value) {
                            if ($key != "idChar") {
                                $update .= $key . "='" . database::safesql($value) . "',";
                            }
                        }
                        database::query("UPDATE mail SET " . rtrim($update, ",") . " WHERE idItem='" . database::safesql($data['idItem']) . "'");
                    }
                } else {
                    system::jms("info", "邮件已发送,但将日志写入数据库时出现问题");
                }
            } else {
                system::jms("danger", "发送邮件时出错");
            }
        } else {
            system::jms("info", "请输入角色ID发送邮件");
        }
    }

    static function sendMailAllOnline($data)
    {
        $online = array();
        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeInt32(0);
        stream::writeOctets("");
        stream::pack(character::$pack['GMListOnlineUser']);
        stream::$readData = socket::sendPacket(5, socket::packInt(config::$gdeliverydPort) . stream::$writeData);
        if (stream::$readData != "server:0") {
            stream::readCUint32();
            stream::$length = stream::readCUint32();
            $online = GRole::readData(character::$functions['GMListOnlineUser']);
        }
        if ($online['count']['value'] > 0) {
            foreach ($online['users'] as $user) {
                $data['idChar'] = $user['roleid']['value'];
                stream::$writeData = "";
                self::mail($data, true);
            }
            system::log("向所有人发送邮件");
            system::jms("success", "已经向所有玩家发送 " . $online['count']['value'] . "封邮件 ");
        }

    }

    static function getItemsMail($id = "")
    {
        $items = "";
        $fileElement = json_decode(base64_decode(gzinflate(file_get_contents(dir . "/system/data/items.json"))),true);
        $fileIcon = json_decode(base64_decode(gzinflate(file_get_contents(dir . "/system/data/icons.json"))),true);

        if (isset($id) && is_numeric($id)) {
            database::query("SELECT * FROM mail WHERE idItem='{$id}'");
            $items = json_encode(database::assoc());
        } else {
            $history = database::query("SELECT * FROM mail");
            echo "<input type='hidden'>";

            for ($i = 0; $i < database::num($history); $i++) {
                $item = database::assoc($history);
                $elItem = editorModel::getItemFromElement($item['idItem'], $fileElement);
                $icon = (isset($fileIcon[$elItem['icon']])) ?$fileIcon[$elItem['icon']] : $fileIcon["unknown.dds"];
                $items .= "<option value='" . $item['idItem'] . "' data-content='<img src=\"data:image/png;base64,$icon\"> {$item['idItem']} {$elItem['name']}'></option>";
            }

        }
        return $items;
    }

    static function checkStatusServer()
    {
        $proc = array();
        foreach (server::$server as $key => $server) {
            $program = (!isset($server['pid_name'])) ? $server['program'] : $server['pid_name'][config::$serverTypeAuth];

            $getRecv = socket::sendPacket(2, socket::packString($program), 1024 * 100);

            if ($getRecv != "off") {
                $getProcess = explode("\n", trim($getRecv));
                //$getProcess = array_diff($getProcess, array(''));
                foreach ($getProcess as $value) {
                    $proc[$key]['process'][] = str_getcsv(preg_replace("/\s{2,}/", ' ', $value), " ", "", "\n");
                }
            }
            if ($getRecv != "off") {
                $proc[$key]['count'] = count($proc[$key]['process']);
                $proc[$key]['status'] = "<span style='color: green'>在线</span>";
            } else {
                $proc[$key]['count'] = 0;
                $proc[$key]['status'] = "<span style='color:red;'>离线</span>";
            }
            //break;
        }
        return $proc;
    }

    static function getStartedLocation()
    {
        $Started = "";
        $getRecv = socket::sendPacket(2, socket::packString("gs"), 2048 * 10);
        if ($getRecv != "off") {
            $getProcess = explode("\n", trim($getRecv));
            $arr = func::listLocations(true);

            foreach ($getProcess as $process) {
                $get = str_getcsv(preg_replace("/\s{2,}/", ' ', $process), " ", "", "\n");
                $Started .= "<tr id='loc-{$get[1]}'>
    <td><label style='width: 100%' class='custom-control custom-checkbox'>
  <input type='checkbox' name='checkbox[{$get[1]}]' data-pid='{$get[1]}' id='location' class='location custom-control-input' value='{$get[11]}'>
  <span class='custom-control-indicator'></span>
  <span class='custom-control-description'>" . $arr[$get[11]] . "</span>
</label></td>
    <td>" . $get[2] . "%</td>
    <td>" . $get[3] . "%</td>
     <td><button onclick='killLocation({$get[1]})' class='btn btn-danger btn-sm'><i class='fas fa-power-off'></i></button></td></tr>";
            }
        }
        return $Started;

    }

    static function startLocation($data)
    {
        $server = server::$server['gs'];
        $error = false;
        if ($data['oneQuery'] == "true") {
            $firstLocation = $data['locations'][0];
            $count = count($data['locations']);
            $locations = array_slice($data['locations'], 1);
            $goStart = "";
            foreach ($locations as $location) {
                $goStart .= "$location ";
            }
            $startServerOneQuery = "cd {dir}; ./{program} $firstLocation gs.conf gmserver.conf gsalias.conf $goStart> {log_dir}/Locations_{$count}_iweb.log &";
            $startServerOneQuery = preg_replace("{{dir}}", config::$serverPath . "/" . $server['dir'], $startServerOneQuery);
            $startServerOneQuery = preg_replace("{{program}}", $server['program'], $startServerOneQuery);
            $startServerOneQuery = preg_replace("{{log_dir}}", config::$serverPath . "/logs", $startServerOneQuery);
            if (socket::sendPacket(0, socket::packString($server['program'] . " " . $firstLocation) . socket::packString($startServerOneQuery)) == "off") {
                $error = true;
            }

        } else {
            foreach ($data['locations'] as $item) {
                $startServer = "cd {dir}; ./{program} {config} > {log_dir}/{$item}_iweb.log &";
                $startServer = preg_replace("{{config}}", $item, $startServer);
                $startServer = preg_replace("{{dir}}", config::$serverPath . "/" . $server['dir'], $startServer);
                $startServer = preg_replace("{{program}}", $server['program'], $startServer);
                $startServer = preg_replace("{{log_dir}}", config::$serverPath . "/logs", $startServer);
                if (socket::sendPacket(0, socket::packString($server['program'] . " " . $item) . socket::packString($startServer)) == "off") {
                    $error = true;
                }
            }
        }
        if (!$error) {
            system::jms("success", "启动地图成功");
            system::log("启动地图");
        } else {
            system::jms("danger", "无法启动地图");
        }
    }

    static function killPid($pid)
    {
        if (is_array($pid)) {
            foreach ($pid as $item) {
                socket::sendPacket(7, socket::packInt($item));
            }
        } else
            socket::sendPacket(7, socket::packInt($pid));
    }

    static function startServer()
    {
        $error = true;
        $error1 = false;
        foreach (server::$server as $key => $server) {
            if ($key == "auth")
                $serverPIDName = $server['pid_name'][config::$serverTypeAuth];
            else
                $serverPIDName = $server['program'] . " " . $server['config'];

            $startServer = "cd {dir}; ./{program} {config} > {log_dir}/{program}_iweb.log &";

            $startServer = preg_replace("{{dir}}", config::$serverPath . "/" . $server['dir'], $startServer);
            $startServer = preg_replace("{{program}}", $server['program'], $startServer);
            $startServer = preg_replace("{{config}}", $server['config'], $startServer);
            $startServer = preg_replace("{{log_dir}}", config::$logsPath, $startServer);
            $result = socket::sendPacket(0, socket::packString($serverPIDName) . socket::packString($startServer));
           // system::debug($key . " " . $result);
            if ($result == "off") {
                $error = false;
            } elseif ($result == "0") {
                $error1 = $server['program'];
            }
        }
        if ($error) {
            system::jms("success", "启动服务器");
            system::log("启动服务器");
        } else if ($error1) {
            system::jms("danger", "无法启动 " . $error1);
        } else
            system::jms("danger", "无法启动一个或多个服务");

    }

    static function stopServer($restart = false)
    {
        $error = true;
        foreach (server::$serverStop as $stop) {
            if (socket::sendPacket(1, socket::packString($stop)) == "off") {
                $error = false;
            }
        }
        socket::sendPacket(9);
        if (!$restart) {
            if (!$error) {
                system::jms("success", "关闭服务器");
                system::log("关闭服务器");
            } else
                system::jms("danger", "无法关闭一个或多个服务");
        }
    }

    static function startService($service){

        if ($service == "auth")
            $serverPIDName = server::$server[$service]['pid_name'][config::$serverTypeAuth];
        else
            $serverPIDName = server::$server[$service]['program'] . " " . server::$server[$service]['config'];

        $startServer = "cd {dir}; ./{program} {config} > {log_dir}/{program}_iweb.log &";

        $startServer = preg_replace("{{dir}}", config::$serverPath . "/" . server::$server[$service]['dir'], $startServer);
        $startServer = preg_replace("{{program}}", server::$server[$service]['program'], $startServer);
        $startServer = preg_replace("{{config}}", server::$server[$service]['config'], $startServer);
        $startServer = preg_replace("{{log_dir}}", config::$logsPath, $startServer);
        $result = socket::sendPacket(0, socket::packString($serverPIDName) . socket::packString($startServer));
        system::jms("success", "发送命令以启用该服务 $service");
    }

    static function stopService($service, $restart = false)
    {
            $program = (!isset(server::$server[$service]['pid_name'])) ? server::$server[$service]['program'] : server::$server[$service]['pid_name'][config::$serverTypeAuth];
            $getRecv = socket::sendPacket(2, socket::packString($program), 1024 * 100);
            if ($getRecv != "off") {
                $getProcess = explode("\n", trim($getRecv));
                $getInfo = str_getcsv(preg_replace("/\s{2,}/", ' ', $getProcess[0]), " ", "", "\n");
                self::killPid($getInfo[1]);
                if (!$restart) system::jms("success", "发送命令关闭服务 $service");
            }else{
                if (!$restart) system::jms("danger", "$service 无法再次关闭");
            }
    }

    static function restartService($service)
    {
        self::stopService($service, true);
        self::startService($service);
    }

    static function clearServer(){
        if (socket::sendPacket(9) == "1") {
            system::jms("success", "服务器缓存已清除");
            system::log("清除服务器缓存");
        }
    }

    static function restartServer()
    {
        self::stopServer(true);
        self::startServer();
    }

}