<?php

namespace system\models;

use system\data\character\character;
use system\data\config;
use system\data\lang;
use system\libs\database;
use system\libs\func;
use system\libs\GRole;
use system\libs\socket;
use system\libs\stream;
use system\libs\struct\GRoleData;
use system\libs\struct\roleLevelUp;
use system\libs\system;

if (!defined('IWEB')) {
    die("Error!");
}

class editorModel
{
    static $visual;

    static function saveVisual($data)
    {
        //ini_set('memory_limit', '-1');
        ini_set('memory_limit', '-1');

        $role = GRole::readCharacter($data['id'], false);
        // system::debug($data['visual']);
        foreach ($data['visual'] as $key => $value) {

            $param = explode("-", $key);
            // system::debug($param);
            if (isset($param[1])) {
                if (isset($param[2])) {
                    if (isset($param[3]))
                        $role['role'][$param[0]][$param[1]][$param[2]][$param[3]]['value'] = $value;
                    else
                        $role['role'][$param[0]][$param[1]][$param[2]]['value'] = $value;
                } else {
                    if ($param[1] == "eqp") {
                        $items = json_decode($value, true);
                        for ($i = 0; $i < count($items); $i++) {
                            $items[$i]['data']['value'] = @pack("H*", $items[$i]['data']['value']);
                        }
                        $role['role']['equipment']['eqpcount']['value'] = count($items);
                        $role['role'][$param[0]][$param[1]] = $items;
                    } elseif ($param[1] == "inv") {
                        $items = json_decode($value, true);
                        for ($i = 0; $i < count($items); $i++) {
                            $items[$i]['data']['value'] = @pack("H*", $items[$i]['data']['value']);
                        }
                        $role['role']['pocket']['invcount']['value'] = count($items);
                        $role['role'][$param[0]][$param[1]] = $items;
                    } elseif ($param[1] == "store") {

                        $items = json_decode($value, true);
                        for ($i = 0; $i < count($items); $i++) {
                            $items[$i]['data']['value'] = @pack("H*", $items[$i]['data']['value']);
                        }
                        $role['role']['storehouse']['storecount']['value'] = count($items);
                        $role['role'][$param[0]][$param[1]] = $items;

                    } elseif ($param[1] == "card") {

                        $items = json_decode($value, true);
                        for ($i = 0; $i < count($items); $i++) {
                            $items[$i]['data']['value'] = @pack("H*", $items[$i]['data']['value']);
                        }
                        $role['role']['storehouse']['cardcount']['value'] = count($items);
                        $role['role'][$param[0]][$param[1]] = $items;

                    } else {
                        $role['role'][$param[0]][$param[1]]['value'] = $value;

                    }
                }
            } else {
                $role['role'][$param[0]] = $value;
            }
        }
        GRole::writeCharacter($data['id'], $role, false);
    }

    static function selectProp($arr, $value)
    {
        $result = "";
        foreach ($arr as $key => $val) {
            if ($value == $key) $active = " selected"; else $active = "";
            $result .= "<option value='{$key}' {$active}>$val</option>";
        }
        return $result;
    }

    //work
    static function levelUp($id, $level)
    {
        $role = GRole::readCharacter($id, false);
        if ($role) {
            if (is_numeric($id) && !empty($id)) {
                if ($level >= 1) {
                    $levelUP = new roleLevelUp();

                    $role['role']['status']['pp']['value'] = $level * 5;
                    $role['role']['status']['property']['vitality']['value'] = 5;
                    $role['role']['status']['property']['energy']['value'] = 5;
                    $role['role']['status']['property']['strength']['value'] = 5;
                    $role['role']['status']['property']['agility']['value'] = 5;

                    $levelUP->levelProperty($role['role']['base']['cls']['value'], $level, $role['role']['status']['property']);
                    $role['role']['status']['level']['value'] = $level;
                    $role['role']['status']['exp']['value'] = 0;
                    if ($role['role']['status']['hp']['value'] < $role['role']['status']['property']['max_hp']['value']) $role['role']['status']['property']['hp']['value'] = $role['role']['status']['property']['max_hp']['value'];
                    if ($role['role']['status']['mp']['value'] < $role['role']['status']['property']['max_mp']['value']) $role['role']['status']['property']['mp']['value'] = $role['role']['status']['property']['max_mp']['value'];

                    if (GRole::writeCharacter($id, $role, false))
                        system::log("角色级别已更改" . $id);
                } else
                    system::jms("info", "等级低于1或超过最高等级");
            } else
                system::jms("info", lang::$notValidCharID);
        } else
            system::jms("danger", "获取角色时出错");
    }

    //work
    static function teleportGD($roleID)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            if ($role = GRole::readCharacter($roleID, true)) {
                $role['role']['status']['posx']['value'] = 1270.651;
                $role['role']['status']['posy']['value'] = 219.756;
                $role['role']['status']['posz']['value'] = 1033.289;
                $role['role']['status']['worldtag']['value'] = 1;
                if (GRole::writeCharacter($roleID, $role, true))
                    system::log("已成功将角色 $roleID 移动到城西");
            } else
                system::jms("danger", "获取角色时出错");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function nullSpEp($roleID)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            if ($role = GRole::readCharacter($roleID, true)) {
                $role['role']['status']['sp']['value'] = 0;
                $role['role']['status']['exp']['value'] = 0;
                if (GRole::writeCharacter($roleID, $role, true))
                    system::log("重置元神和经验" . $roleID);
            } else
                system::jms("danger", "获取角色时出错");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function nullrolePasswd($roleID)
    {
        if (!empty($roleID) && is_numeric($roleID)) {
            if ($role = GRole::readCharacter($roleID, true)) {
				$role['role']['status']['storehousepasswd']['value'] = "";
                if (GRole::writeCharacter($roleID, $role, true))
                    system::log("清空仓库密码" . $roleID);
            } else
                system::jms("danger", "获取角色时出错");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function addGold($userid, $zoneid, $count)
    {
        // if (!empty($roleID) && is_numeric($roleID)) {
        //     stream::writeInt32($roleID);
        //     stream::writeInt32($count);
        //     stream::pack(character::$pack['DebugAddCash']);
        //     if (socket::sendPacket(6, socket::packInt(config::$dbPort) . stream::$writeData) != "server:0") {
        //         system::jms("success", "元宝发送成功");
        //         system::log("已将 $count 元宝发送至账户" . $roleID);
        //     } else {
        //         system::jms("danger", "元宝发送失败,可能是服务器已关闭");
        //     }
        // } else
        //     system::jms("danger", lang::$notValidCharID);
        $date = date("Y-m-d H:i:s");
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

    static function CashPasswordSet($userid, $password)
    {
        $userid = 1024;
        if (!empty($userid) && is_numeric($userid)) {
            stream::writeInt32($userid);
            stream::writeOctets($password);
            stream::writeInt32(16);
            stream::pack(character::$pack['CashPasswordSet']);
            if (socket::sendPacket(67, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                system::jms("success", "元宝交易密码修改成功");
                system::log("已将 $userid 元宝交易密码修改" . $password);
            } else {
                system::jms("danger", "元宝交易密码修改失败");
            }
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function IWebAutolockSet($userid, $locktime)
    {
        if (!empty($userid) && is_numeric($userid)) {
            stream::writeInt32(0);
            stream::writeInt32($userid);
            stream::writeInt32($locktime);
            stream::pack(character::$pack['IWebAutolockSet']);
            if (socket::sendPacket(3, socket::packInt(config::$gdeliverydPort) . stream::$writeData) != "server:0") {
                system::jms("success", "用户 $userid 设置安全锁 $locktime 成功");
                system::log("已将 $userid 设置安全锁" . $locktime);
            } else {
                system::jms("danger", "设置安全锁失败");
            }
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function deleteRole($id)
    {
        if (!empty($id) && is_numeric($id)) {
            stream::writeInt32($id, true, -1);
            stream::writeByte(0);
            stream::pack(character::$pack['DeleteRole']);
            if (socket::sendPacket(6, socket::packInt(config::$dbPort) . stream::$writeData) != "server:0") {
                system::jms("success", "角色已删除");
                system::log("删除角色 " . $id);
            } else
                system::jms("danger", "无法删除角色");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function renameRole($id, $oldName, $newName)
    {
        if (!empty($id) && is_numeric($id)) {
            stream::writeInt32($id, true, -1);
            stream::writeString($oldName);
            stream::writeString($newName);
            stream::pack(character::$pack['RenameRole']);;
            if (socket::sendPacket(6, socket::packInt(config::$dbPort) . stream::$writeData) != "server:0") {
                system::jms("success", "角色重新命名");
                system::log("角色改名ID $id 旧名字 $oldName 新名字 $newName");
            } else
                system::jms("danger", "角色改名失败");
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    //work
    static function charsList($id)
    {
        if (!empty($id) && is_numeric($id)) {
            $list = GRoleData::getListRoles($id);
            $roleList = "<table>";
            if ($list['count']['value'] > 0) {
                foreach ($list['roles'] as $role) {
                    $roleList .= "<tr>  <td>" . $role['id']['value'] . "&nbsp;&blacktriangleright;&nbsp;</td> <td><b> " . $role['name']['value'] . "&nbsp;&nbsp;&nbsp; </b> </td>
<td><a class=\"badge badge-primary\" href='" . config::$site_adr . "/?controller=editor&page=xml&id=" . $role['id']['value'] . "'>XML</a> 
<a class=\"badge badge-success\" href='" . config::$site_adr . "/?controller=editor&id=" . $role['id']['value'] . "'>可视化</a> 
<a class=\"badge badge-warning\" href='" . config::$site_adr . "/?controller=server&page=mail&id=" . $role['id']['value'] . "'>发送邮件</a>
<a class=\"badge badge-info\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $role['id']['value'] . ", 2)'>禁止发言</a>
<a class=\"badge badge-light\" href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#ban\" onclick='ban(" . $role['id']['value'] . ", 1)'>禁止登录</a>
<a class=\"badge badge-danger\" href='javascript:void(0)' onclick='goDelChar(" . $role['id']['value'] . ")'>删除角色</a></td></tr>";
                }
                $roleList .= "</table>";
                echo $roleList;
            } else
                echo "<p class=\"alert alert-info\">没有找到角色,或者无法从服务器上获取数据</p>";
        } else
            system::jms("danger", lang::$notValidCharID);
    }

    static function getItemFromElement($id, $itemArray)
    {
        // if (isset($itemArray[$id])) {
        if (isset($itemArray[(string)$id])) {
            $iconNameArr = explode("/", $itemArray[$id]['icon']);
            $item['name'] = $itemArray[$id]['name'];
            $item['list'] = $itemArray[$id]['list'];
            $item['icon'] = end($iconNameArr);
            if (isset($itemArray[$id]['color']))
                $item['color'] = $itemArray[$id]['color'];
            else
                $item['color'] = 0;

        } else {
            $item['name'] = "未知物品";
            $item['list'] = 999;
            $item['icon'] = "unknown.dds";
            $item['color'] = 0;
        }
        return $item;
    }

    static function ban($id, $time, $type, $reason)
    {

        // $list = GRoleData::getListRoles($id);
        // // 检查列表是否不为空并且至少有一个角色
        // if ($list['count']['value'] > 0) {
        //     // 获取第一个角色的ID
        //     $firstRoleId = $list['roles'][0]['id']['value'];
        // }
        // $idtype = ($type == 1 || $type == 2) ? $firstRoleId : $id;
        stream::writeInt32(-1);
        stream::writeInt32(0);
        stream::writeInt32($id);
        stream::writeInt32($time);
        stream::writeString($reason);
        switch ($type) {
            case 1:
                $msg = "账号封禁：$id  时间：$time";
                stream::pack(character::$pack['GMKickoutUser']); // 账号封禁
                break;
            case 2:
                $msg = "账号禁言：$id  时间：$time";
                stream::pack(character::$pack['GMShutup']); // 账号禁言
                break;
            case 3:
                $msg = "角色禁言：$id  时间：$time";
                stream::pack(character::$pack['GMShutupRole']); // 角色禁言
                break;
            case 4:
                $msg = "角色封禁：$id  时间：$time";
                stream::pack(character::$pack['GMKickoutRole']); // 角色封禁
                break;
            default:
                $msg = "封禁类型无效";
                break;
        }
        if (socket::sendPacket(4, socket::packInt(config::$gdeliverydPort) . stream::$writeData)) {
            system::log($msg);
            system::jms("success", $msg);
        } else
            system::jms("danger", "角色/帐户禁封失败");
    }

    static function getBonus()
    {
        $fp = fopen(dir . '/system/data/bonus.txt', "r");
        if (!$fp) die('Error opening file bonus.txt');
        $bonus = array();
        $cnt = 0;
        while (!feof($fp)) {
            $line = fgets($fp);
            $line = substr($line, 0, strlen($line) - 1);
            $bonus['names'][$cnt] = $line;
            $line = fgets($fp);
            $line = explode(',', substr($line, 0, strlen($line) - 1));
            $bonus['ids'][$cnt] = $line;
            $cnt++;
        }
        fclose($fp);
        return $bonus;
    }

    static function getSharpening()
    {
        $sharpening = array();
        $fp = fopen(dir . '/system/data/sharpening.txt', "r");
        if (!$fp) die('Error opening file sharpening.txt');
        $cnt = 0;
        while (!feof($fp)) {
            $line = fgets($fp);
            $line = substr($line, 0, strlen($line) - 1);
            $sharpening['names'][$cnt] = $line;
            $line = fgets($fp);
            $line = explode(',', substr($line, 0, strlen($line) - 1));
            $sharpening['ids'][$cnt] = $line;
            $cnt++;
        }
        fclose($fp);
        return $sharpening;
    }

    static function checkSharpening($id, $sharpening)
    {
        foreach ($sharpening['ids'] as $i => $val) {
            if (in_array($id, $val)) return true;
        }
        return false;
    }

    static function getWeapon($item, $sharpening, $bonusData, $fileElement)
    {
        $dot = 0;
        $cell = 0;
        $addAtkSpeed = 0;
        $stone = "";
        $bonusItem = "";

        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['weapon']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);

        if (isset($itemRes['BonusInfo']['bonus'])) {
            $specialIds = array(331, 337, 338, 339);    //未知错误，增加420,421后不显示
            
            foreach ($itemRes['BonusInfo']['bonus'] as $key => &$bonus) {
                if (in_array($bonus['id']['value'], $specialIds)) {
                    $addAtkSpeed += $bonus['stat']['value'];
                }
                elseif (($bonus['type']['value'] == 16384) && self::checkSharpening($bonus['id']['value'], $sharpening)) {
                    $dot = $bonus['dopStat1']['value'];
                    $addBonus = func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']);
                    $bonusItem .= "<br><span style=\"color: lightcoral;\">精炼: " . $addBonus . "</span>";
                    $addBonus = explode(" +", $addBonus);
                    ($itemRes['MinPhysAtk']['value']) ? $itemRes['MinPhysAtk']['value'] += $addBonus[1] : $itemRes['MinPhysAtk']['value'];
                    ($itemRes['MaxPhysAtk']['value']) ? $itemRes['MaxPhysAtk']['value'] += $addBonus[1] : $itemRes['MaxPhysAtk']['value'];
                    ($itemRes['MinMagAtk']['value']) ? $itemRes['MinMagAtk']['value'] += $addBonus[1] : $itemRes['MinMagAtk']['value'];
                    ($itemRes['MaxMagAtk']['value']) ? $itemRes['MaxMagAtk']['value'] += $addBonus[1] : $itemRes['MaxMagAtk']['value'];
                } elseif ($bonus['type']['value'] == 40960) {
                    $stoneEl = editorModel::getItemFromElement($itemRes['cellInfo']['cellStone'][$cell]['id']['value'], $fileElement);
                    $stone .= "<br><span style=\"color: #a1e2f1\">" . $stoneEl['name'] . " " . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
                    $cell++;
                } else
                    $bonusItem .= "<br><span style=\"color: #835bff\">" . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
            }
        }
        $dot = ($dot != 0) ? "+$dot" : "";
        $cellDesk = ($itemRes['cellInfo']['cellCount']['value'] > 0) ? "(" . $itemRes['cellInfo']['cellCount']['value'] . "孔)" : "";
        $description = " $cellDesk $dot <br>品阶 " . $itemRes['Rang']['value'];
        $description .= "<br>攻击频率 " . round(20 / ($itemRes['AtkSpeed']['value'] - $addAtkSpeed), 2);
        $description .= "<br>攻击距离 " . $itemRes['Distance']['value'];
        $description .= ($itemRes['MinPhysAtk']['value'] != 0) ? "<br>物理攻击 " . $itemRes['MinPhysAtk']['value'] . "-" . $itemRes['MaxPhysAtk']['value'] : "";
        $description .= ($itemRes['MinMagAtk']['value'] != 0) ? "<br>法术攻击 " . $itemRes['MinMagAtk']['value'] . "-" . $itemRes['MaxMagAtk']['value'] : "";
        $description .= "<br>耐久上限 " . ($itemRes['strength']['value'] / 100) . "/" . ($itemRes['maxStrength']['value'] / 100);
        $description .= ($itemRes['class']['value'] != func::allCharSum()) ? "<br>职业限制 " . func::getCharClass($itemRes['class']['value']) : "";
        $description .= "<br>等级要求 " . $itemRes['level']['value'];
        $description .= ($itemRes['strong']['value'] != 0) ? "<br>力量要求 " . $itemRes['strong']['value'] : "";
        $description .= ($itemRes['endurance']['value'] != 0) ? "<br>体质要求 " . $itemRes['endurance']['value'] : "";
        $description .= ($itemRes['agility']['value'] != 0) ? "<br>敏捷要求 " . $itemRes['agility']['value'] : "";
        $description .= ($itemRes['intellect']['value'] != 0) ? "<br>灵力要求 " . $itemRes['intellect']['value'] : "";
        $description .= (!empty($bonusItem)) ? $bonusItem : "";
        $description .= (!empty($stone)) ? $stone : "";
        $description .= (!empty($itemRes['creator']['value'])) ? "<br><span style=\"color: lawngreen\">制造者: " . func::reColorDesc($itemRes['creator']['value']) . "</span>" : "";

        return $description;
    }

    static function getFly($item)
    {
        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['fly']);
        //system::debug($itemRes);
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        $description = "<br>品阶 " . $itemRes['element_level']['value'] . "<br>";
        $description .= "普通飞行 +" . round($itemRes['speed_increase1']['value'], 2) . " 米\秒<br>";
        $description .= "加速飞行 +" . round($itemRes['speed_increase2']['value'], 2) . " 米\秒<br>";
        $description .= "有效时间 " . $itemRes['max_time']['value'] . "\\" . $itemRes['cur_time']['value'] . " (秒)<br>";
        $description .= "职业限制 " . func::getCharClass($itemRes['require_class']['value']) . "<br>";
        $description .= "等级要求 " . $itemRes['require_level']['value'];
        $description .= (!empty($itemRes['creator']['value'])) ? "<br><span style=\"color: lawngreen\">制造者: " . $itemRes['creator']['value'] . "</span>" : "";
        return $description;
    }

    static function getFashion($item)
    {
        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['fashion']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        $description = '<br>等级要求 <b>' . $itemRes['require_level']['value'] . '</b>';
        $description .= '<br>性别 <b>' . lang::$gender[$itemRes['gender']['value']] . '</b>';
        return $description;
    }

    static function getArmor($item, $sharpening, $bonusData, $fileElement)
    {
        $dot = 0;
        $cell = 0;
        $stone = "";
        $bonusItem = "";

        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['armor']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);

        if (isset($itemRes['BonusInfo']['bonus'])) {
            $specialValues = array(420, 421, 331, 337, 338, 339, 2012, 2013, 2014, 2957, 3508, 3525, 3542, 3680, 3681, 3444, 3466, 3488);
            
            foreach ($itemRes['BonusInfo']['bonus'] as $key => &$bonus) { // 注意这里的 & 符号，表示传递引用
                if (in_array($bonus['id']['value'], $specialValues)) {
                    $bonus['stat']['value'] /= 20; // 将间隔属性更新
                }
                if (($bonus['type']['value'] == 16384) && self::checkSharpening($bonus['id']['value'], $sharpening)) {
                    $dot = $bonus['dopStat1']['value'];
                    $addBonus = func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']);
                    $bonusItem .= "<br><span style=\"color: lightcoral;\">精炼: " . $addBonus . "</span>";
                    $addBonus = explode(" +", $addBonus);
                    $itemRes['HP']['value'] += $addBonus[1];
                } elseif ($bonus['type']['value'] == 40960) {
                    $stoneEl = editorModel::getItemFromElement($itemRes['cellInfo']['cellStone'][$cell]['id']['value'], $fileElement);
                    $stone .= "<br><span style=\"color: #a1e2f1\">" . $stoneEl['name'] . " " . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
                    $cell++;
                } else
                    $bonusItem .= "<br><span style=\"color: #835bff\">" . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
            }
        }
        $dot = ($dot != 0) ? "+$dot" : "";
        $cellDesk = ($itemRes['cellInfo']['cellCount']['value'] > 0) ? "(" . $itemRes['cellInfo']['cellCount']['value'] . "孔)" : "";
        $description = " $cellDesk $dot";
        $description .= ($itemRes['PhysDef']['value']) ? "<br>物防 <b>+{$itemRes['PhysDef']['value']}</b>" : "";
        $description .= ($itemRes['Dodge']['value']) ? "<br>躲闪度 <b>+{$itemRes['Dodge']['value']}</b>" : "";
        $description .= ($itemRes['Mana']['value']) ? "<br>真气值 <b>+{$itemRes['Mana']['value']}</b>" : "";
        $description .= ($itemRes['HP']['value']) ? "<br>生命值 <b>+{$itemRes['HP']['value']}</b>" : "";
        $description .= ($itemRes['MetalDef']['value']) ? "<br>金防 <b>+{$itemRes['MetalDef']['value']}</b>" : "";
        $description .= ($itemRes['WoodDef']['value']) ? "<br>木防 <b>+{$itemRes['WoodDef']['value']}</b>" : "";
        $description .= ($itemRes['WaterDef']['value']) ? "<br>水防 <b>+{$itemRes['WaterDef']['value']}</b>" : "";
        $description .= ($itemRes['FireDef']['value']) ? "<br>火防 <b>+{$itemRes['FireDef']['value']}</b>" : "";
        $description .= ($itemRes['EarthDef']['value']) ? "<br>土防 <b>+{$itemRes['EarthDef']['value']}</b>" : "";
        $description .= "<br>耐久上限: <b>" . ($itemRes['CurDurab']['value'] / 100) . '/' . ($itemRes['MaxDurab']['value'] / 100) . "</b>";
        $description .= ($itemRes['ClassReq']['value'] != func::allCharSum()) ? "<br>职业限制 " . func::getCharClass($itemRes['ClassReq']['value']) : "";
        $description .= ($itemRes['LvlReq']['value']) ? "<br>要求等级 <b>{$itemRes['LvlReq']['value']}</b>" : "";
        $description .= ($itemRes['StrReq']['value']) ? "<br>要求力量 <b>{$itemRes['StrReq']['value']}</b>" : "";
        $description .= ($itemRes['ConReq']['value']) ? "<br>要求体质 <b>{$itemRes['ConReq']['value']}</b>" : "";
        $description .= ($itemRes['DexReq']['value']) ? "<br>要求敏捷 <b>{$itemRes['DexReq']['value']}</b>" : "";
        $description .= ($itemRes['IntReq']['value']) ? "<br>要求灵力 <b>{$itemRes['IntReq']['value']}</b>" : "";
        $description .= (!empty($bonusItem)) ? $bonusItem : "";
        $description .= (!empty($stone)) ? $stone : "";
        $description .= (!empty($itemRes['creator']['value'])) ? "<br><span style=\"color: lawngreen\">制造者: " . $itemRes['creator']['value'] . "</span>" : "";

        return $description;
    }

    static function getAmulet($item, $sharpening, $bonusData, $fileElement)
    {
        $dot = 0;
        $cell = 0;
        $stone = "";
        $bonusItem = "";

        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['amulet']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);

        if (isset($itemRes['BonusInfo']['bonus'])) {
            $specialValues = array(420, 421, 331, 337, 338, 339, 2012, 2013, 2014, 2957, 3508, 3525, 3542, 3680, 3681, 3444, 3466, 3488);
            
            foreach ($itemRes['BonusInfo']['bonus'] as $key => &$bonus) { // 注意这里的 & 符号，表示传递引用
                if (in_array($bonus['id']['value'], $specialValues)) {
                    $bonus['stat']['value'] /= 20; // 将间隔属性更新
                }
                if (($bonus['type']['value'] == 16384) && self::checkSharpening($bonus['id']['value'], $sharpening)) {
                    $dot = $bonus['dopStat1']['value'];
                    $addBonus = func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']);
                    $bonusItem .= "<br><span style=\"color: lightcoral;\">精炼: " . $addBonus . "</span>";
                    $addBonus = explode(" +", $addBonus);
                    $itemRes['PhysDef']['value'] += $addBonus[1];
                } elseif ($bonus['type']['value'] == 40960) {
                    $stoneEl = editorModel::getItemFromElement($itemRes['cellInfo']['cellStone'][$cell]['id']['value'], $fileElement);
                    $stone .= "<br><span style=\"color: #a1e2f1\">" . $stoneEl['name'] . " " . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
                    $cell++;
                } else
                    $bonusItem .= "<br><span style=\"color: #835bff\">" . func::bonus($bonus['id']['value'], $bonusData, $bonus['stat']['value']) . "</span>";
            }
        }
        $dot = ($dot != 0) ? "+$dot" : "";
        $cellDesk = ($itemRes['cellInfo']['cellCount']['value'] > 0) ? "(" . $itemRes['cellInfo']['cellCount']['value'] . "孔)" : "" ;
        $description = " $cellDesk $dot";
        $description .= ($itemRes['PhysAtk']['value']) ? "<br>物理攻击 <b>+{$itemRes['PhysAtk']['value']}</b>" : "";
        $description .= ($itemRes['MagAtk']['value']) ? "<br>法术攻击 <b>+{$itemRes['MagAtk']['value']}</b>" : "";
        $description .= ($itemRes['PhysDef']['value']) ? "<br>物理防御 <b>+{$itemRes['PhysDef']['value']}</b>" : "";
        $description .= ($itemRes['Dodge']['value']) ? "<br>躲闪度 <b>+{$itemRes['Dodge']['value']}</b>" : "";
        $description .= ($itemRes['MetalDef']['value']) ? "<br>金防 <b>+{$itemRes['MetalDef']['value']}</b>" : "";
        $description .= ($itemRes['WoodDef']['value']) ? "<br>木防 <b>+{$itemRes['WoodDef']['value']}</b>" : "";
        $description .= ($itemRes['WaterDef']['value']) ? "<br>水防 <b>+{$itemRes['WaterDef']['value']}</b>" : "";
        $description .= ($itemRes['FireDef']['value']) ? "<br>火防 <b>+{$itemRes['FireDef']['value']}</b>" : "";
        $description .= ($itemRes['EarthDef']['value']) ? "<br>土防 <b>+{$itemRes['EarthDef']['value']}</b>" : "";
        $description .= "<br>耐久上限: <b>" . ($itemRes['CurDurab']['value'] / 100) . '/' . ($itemRes['MaxDurab']['value'] / 100) . "</b>";
        $description .= ($itemRes['ClassReq']['value'] != func::allCharSum()) ? "<br>职业限制 " . func::getCharClass($itemRes['ClassReq']['value']) : "";
        $description .= ($itemRes['LvlReq']['value']) ? "<br>要求等级 <b>{$itemRes['LvlReq']['value']}</b>" : "";
        $description .= ($itemRes['StrReq']['value']) ? "<br>要求力量 <b>{$itemRes['StrReq']['value']}</b>" : "";
        $description .= ($itemRes['ConReq']['value']) ? "<br>要求体质 <b>{$itemRes['ConReq']['value']}</b>" : "";
        $description .= ($itemRes['DexReq']['value']) ? "<br>要求敏捷 <b>{$itemRes['DexReq']['value']}</b>" : "";
        $description .= ($itemRes['IntReq']['value']) ? "<br>要求灵力 <b>{$itemRes['IntReq']['value']}</b>" : "";
        $description .= (!empty($bonusItem)) ? $bonusItem : "";
        $description .= (!empty($stone)) ? $stone : "";
        $description .= (!empty($itemRes['creator']['value'])) ? "<br><span style=\"color: lawngreen\">制造者: " . $itemRes['creator']['value'] . "</span>" : "";

        return $description;
    }

    static function getElf($item, $fileElement)
    {

        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['elf']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        //system::debug($itemRes);
        $description = ($itemRes['sharpening']['value']) ? " +" . $itemRes['sharpening']['value'] : $description = "";
        $description .= "<br>元素精灵等级 " . $itemRes['Lvl']['value'];
        $description .= "<br>力量 " . $itemRes['Strength']['value'];
        $description .= "<br>敏捷 " . $itemRes['Agility']['value'];
        $description .= "<br>体质 " . $itemRes['Endurance']['value'];
        $description .= "<br>灵力 " . $itemRes['Intelligence']['value'];
        $description .= "<br>当前体力值: " . $itemRes['CEnergy']['value'] . "/999999";
        $description .= "<br>能量 ".$itemRes['CEnergy']['value'];

        $description .= "<br>天赋等级 ".$itemRes['GeniusPoints']['value'];
        $description .= "<br>金 ".$itemRes['Metal']['value'];
        $description .= "<br>木 ".$itemRes['Wood']['value'];
        $description .= "<br>水 ".$itemRes['Water']['value'];
        $description .= "<br>火 ".$itemRes['Fire']['value'];
        $description .= "<br>土 ".$itemRes['Earth']['value'];

        return $description;

    }

    static function getCard($item, $fileElement)
    {
        stream::putRead($item['data']['value'], 0);
        $itemRes = GRole::readItemData(character::$items['card']);
        stream::putRead(stream::$readData_copy, stream::$p_copy);
        //system::debug($itemRes);
        switch ($itemRes['CardGrade']['value']) {
            case 0:
                $CardGrade = "C";
                break;
            case 1:
                $CardGrade = "B";
                break;
            case 2:
                $CardGrade = "A";
                break;
            case 3:
                $CardGrade = "S";
                break;
            case 4:
                $CardGrade = "S+";
                break;
            default:
                $CardGrade = $itemRes['CardGrade']['value'];
                break;

        }

        switch ($itemRes['CardType']['value']) {
            case 0:
                $CardType = "破军";
                break;
            case 1:
                $CardType = "破阵";
                break;
            case 2:
                $CardType = "长生";
                break;
            case 3:
                $CardType = "完璧";
                break;
            case 4:
                $CardType = "玄魂";
                break;
            case 5:
                $CardType = "玄命";
                break;
            default:
                $CardType = $itemRes['CardType']['value'];
                break;
        }

        $description = "<br>品阶 " . $CardGrade;
        $description .= "<br>类型 " . $CardType;
        $description .= "<br>等级要求 " . $itemRes['RequiredXp']['value'];
        $description .= "<br>统御力要求 " . $itemRes['RequiredPoints']['value'];
        $description .= "<br>等级 " . $itemRes['LvL']['value'] . "/" . $itemRes['MaxLvL']['value'];
        $description .= "<br>经验 " . $itemRes['CurrentExp']['value'];
        $description .= "<br>转生次数 " . $itemRes['Reborn']['value'];
        return $description;
    }

    static function getDisk($item, $fileElement){

//        stream::putRead($item['data']['value'], 0);
//        system::debug(stream::readInt16(false));
//        system::debug(stream::readByte());
//        system::debug(stream::readByte());
//        system::debug(stream::readByte());
//        system::debug(stream::readByte());
//        system::debug(stream::readInt32());
//        system::debug(stream::readInt16(false));
//        system::debug(stream::readInt16(false));
//        //system::debug(stream::readSingle());
//        system::debug(stream::readInt16(false));
//        system::debug(stream::readInt16(false));
//        system::debug(stream::readInt16(false));
//        stream::putRead(stream::$readData_copy, stream::$p_copy);


        return 0;
    }

    static function itemData($elementItem, $item, $sharpening, $bonusData, $desk, $key, $fileElement, $fileIcon, $type = "inv")
    {
        if ($elementItem['list'] == 3) {
            $description = self::getWeapon($item, $sharpening, $bonusData, $fileElement);
        } elseif ($elementItem['list'] == 6) {
            $description = self::getArmor($item, $sharpening, $bonusData, $fileElement);
        } elseif ($elementItem['list'] == 9) {
            $description = self::getAmulet($item, $sharpening, $bonusData, $fileElement);
        } elseif ($elementItem['list'] == 22) {
            $description = self::getFly($item);
        } elseif ($elementItem['list'] == 83) {
            $description = self::getFashion($item);
        } elseif ($elementItem['list'] == 119) {
            $description = self::getElf($item, $fileElement);
        } elseif ($elementItem['list'] == 184) {
            $description = self::getCard($item, $fileElement);
        } elseif ($elementItem['list'] == 197) {
            $description = self::getDisk($item, $fileElement);
        } else {
            $description = "";
        }

        $itemDesk = (isset($desk[$item['id']['value']])) ? func::reColorDesc($desk[$item['id']['value']]) : "";
        switch ($type) {
            case"inv":
                $class = "item_inv";
                $typeKey = "";
                $typeJS = 0;
                break;
            case"eqp":
                $class = "player__equipment-item cell-{$item['pos']['value']}";
                $typeKey = "-eqp";
                $typeJS = 1;
                break;
            case"store":
                $class = "item_inv";
                $typeKey = "-store";
                $typeJS = 2;
                break;
            case"dress":
                $class = "item_inv";
                $typeKey = "-dress";
                $typeJS = 3;
                break;
            case"card":
                $class = "item_inv";
                $typeKey = "-card";
                $typeJS = 4;
                break;
            case"material":
                $class = "item_inv";
                $typeKey = "-material";
                $typeJS = 5;
                break;
        }

        $elementItem['icon'] = strtolower($elementItem['icon']);
        $icon = (isset($fileIcon[$elementItem['icon']])) ? $fileIcon[$elementItem['icon']] : $fileIcon["unknown.dds"];

        $elementItem['name'] = str_replace("'", "`", $elementItem['name']);
        switch ($elementItem['color']) {
            case 0://+
                $elementItem['name'] = "<span style=\"color: #ffffff;\">{$elementItem['name']}</span>";
                break;
            case 1://+
                $elementItem['name'] = "<span style=\"color: #8080ff;\">{$elementItem['name']}</span>";
                break;
            case 2://+
                $elementItem['name'] = "<span style=\"color: #ffdc50;\">{$elementItem['name']}</span>";
                break;
            case 3://+
                $elementItem['name'] = "<span style=\"color: #aa32ff;\">{$elementItem['name']}</span>";
                break;
            case 4://+
                $elementItem['name'] = "<span style=\"color: #ff6000;\">{$elementItem['name']}</span>";
                break;
            case 5:
                $elementItem['name'] = "<span style=\"color: #ffffff;\">{$elementItem['name']}</span>";
                break;
            case 6:
                $elementItem['name'] = "<span style=\"color: #b0b0b0;\"'>{$elementItem['name']}</span>";
                break;
            case 7://+
                $elementItem['name'] = "<span style=\"color: #00ffae;\">{$elementItem['name']}</span>";
                break;
            case 8://+
                $elementItem['name'] = "<span style=\"color: #ff0000;\">{$elementItem['name']}</span>";
                break;
            case 9://+
                $elementItem['name'] = "<span style=\"color: #ff0000;\">{$elementItem['name']}</span>";
                break;
            case 10://+
                $elementItem['name'] = "<span style=\"color: #80ffff;\">{$elementItem['name']}</span>";
                break;
        }

        $pocketItems = "<div class=\"$class\" onclick='editPocketItem($key,$typeJS, {$elementItem['list']})' data-html=\"true\" data-tip=\"tooltip\" data-key$typeKey='$key' data-toggle=\"modal\" data-target=\"#pocketItem\" data-original-title='<b>{$elementItem['name']}</b>{$description}<br>{$itemDesk}'><img src='data:image/png;base64,$icon'><span class='item_count'>{$item['count']['value']}</span></div>";

        return $pocketItems;
    }

}