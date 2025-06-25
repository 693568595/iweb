function notify(json) {
    var data = "";
    var title = "";
    var icon = "";
    try {
        data = JSON.parse(json);
    }
    catch (e) {
        json = JSON.stringify({type: "danger", message: "Error receive type data"});
        data = JSON.parse(json);
    }

    switch (data.type) {
        case "info":
            title = info;
            icon = "fa fa-info-circle";
            break;

        case "success":
            title = success;
            icon = "fa fa-check";
            break;

        case "danger":
            title = danger;
            icon = "fa fa-exclamation";
            break;
    }

    $.notify({
        icon: icon,
        title: " " + title ,
        message: data.message

    }, {
        // settings
        element: 'body',
        position: null,
        type: data.type,
        allow_dismiss: true,
        newest_on_top: true,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "right"
        },
        offset: 20,
        spacing: 10,
        z_index: 9999,
        delay: 5000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: 'pause',
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        icon_type: 'class',
        template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss"><span aria-hidden="true" style="color: #2e1510">&times;</span></button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title"><b>{1}</b></span><br>' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            '</div>'
    });

    setTimeout(function () {
        $.notifyClose();
    }, 3000);
}

$('button[name=authUser]').on('click', function () {
    var username = $('input[name=username]').val();
    var password = $('input[name=password]').val();

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=auth",
        data: {username: username, password: password},
        success: function (data) {
            var data1 = JSON.parse(data);
            if (data1.type === "reload") location.reload(); else notify(data);
        }
    });
});

$('button[name=sendXML]').on('click', function () {
    var id = $("input[name=id]").val();
    var xml = editor.getValue();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendxml",
        data: {id: id, xml: xml},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);
        }
    });
});

$('button[name=sendMail]').on('click', function () {
    var idChar = $('#idChar').val();
    var idItem = $('#idItem').val();
    var countItem = $('#countItem').val();
    var maxCountItem = $('#maxCountItem').val();
    var octetItem = $('#octetItem').val();
    var prototypeItem = $('#prototypeItem').val();
    var timeItem = $('#timeItem').val();
    var maskItem = $('#maskItem').val();
    var moneyItem = $('#moneyItem').val();
    var titleItem = $('#titleItem').val();
    var messageItem = $('#messageItem').val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmail",
        data: {
            idChar: idChar,
            idItem: idItem,
            countItem: countItem,
            maxCountItem: maxCountItem,
            octetItem: octetItem,
            prototypeItem: prototypeItem,
            timeItem: timeItem,
            maskItem: maskItem,
            moneyItem: moneyItem,
            titleItem: titleItem,
            messageItem: messageItem
        },
        success: function (data) {
            ////$("#systemMessage").html(data);;
            notify(data);
        }
    });
});

$('button[name=sendMailOnline]').on('click', function () {
    var idItem = $('#idItem').val();
    var countItem = $('#countItem').val();
    var maxCountItem = $('#maxCountItem').val();
    var octetItem = $('#octetItem').val();
    var prototypeItem = $('#prototypeItem').val();
    var timeItem = $('#timeItem').val();
    var maskItem = $('#maskItem').val();
    var moneyItem = $('#moneyItem').val();
    var titleItem = $('#titleItem').val();
    var messageItem = $('#messageItem').val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmailall",
        data: {
            idItem: idItem,
            countItem: countItem,
            maxCountItem: maxCountItem,
            octetItem: octetItem,
            prototypeItem: prototypeItem,
            timeItem: timeItem,
            maskItem: maskItem,
            moneyItem: moneyItem,
            titleItem: titleItem,
            messageItem: messageItem
        },
        success: function (data) {
            ////$("#systemMessage").html(data);;
            notify(data);
        }
    });
});

$('button[name=saveVisual]').on('click', function () {
    var save = {};
    var id = $("input[name=id]").val();
    $.each($('textarea,input[name^="visual\\["],select[name^="visual\\["]').serializeArray(), function () {
        var visual = this.name.replace('visual[', '');
        save[visual] = this.value;
    });
    $.ajax({
        url: adr + "/index.php?function=sendvisual",
        type: "POST",
        data: {
            id: id,
            visual: save
        },
        success: function (data) {
            $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=sendChatMsg]').on('click', function () {
    var msg = $("input[name=chatMsg]").val();
    var chanel = $("select[name=chatChanel]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmsg",
        data: {msg: msg, chanel: chanel},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=GMRestartServer]').on('click', function () {
    var restart_time = $("input[name=waitsecs]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=waitsecstime",
        data: {restart_time: restart_time},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=GMOpenGameAttri]').on('click', function () {
    var OpenGameAttri = $("select[name=OpenGameAttri]").val();
    var value = $("input[name=Value]").val();
    if (OpenGameAttri == 204){
        value = value*10;
    }
    else{
        value =1;
    }
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=OpenGameAttri",
        data: {OpenGameAttri: OpenGameAttri, value: value},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=GMCloseGameAttri]').on('click', function () {
    var CloseGameAttri = $("select[name=OpenGameAttri]").val();
    var value = 0;
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=CloseGameAttri",
        data: {CloseGameAttri: CloseGameAttri, value: value},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=startServer]').on('click', function () {
    $('#myModal').modal('show');
    var startCommandEach = JSON.parse(startCommand);
    $.each(startCommandEach, function (a, b) {
        setTimeout(function () {
            if (a !== 0) {
                $('.service_' + (a - 1)).html(" &raquo; 运行 ").css({"color": "green"});
            }
            $('.startCommand').append(b + " <span class='service_" + a + "' style='color: yellow'> &raquo; 启动...</span><br>");
            if (startCommandEach.length - 1 === a) return true;
        }, a * 300);
    });
    startCommandEach = "";
    $.ajax({
        type: "POST", url: adr + "/index.php?function=startServer", success: function (data) {
            //$("#systemMessage").html(data);
            $('#myModal').modal('hide');
            notify(data);
            $('.startCommand').html("");
        }
    });
    return true;
});

$('button[name=stopServer]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=stopServer", success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=clearServer]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=clearServer", success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=clearLog]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=clearLog", success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=restartServer]').on('click', function () {
    $('#myModal').modal('show');
    var startCommandEach = JSON.parse(startCommand);
    $('.startCommand').append("服务器: <span class='service_stop' style='color: yellow'>停止</span><br>");
    $.each(startCommandEach, function (a, b) {
        setTimeout(function () {
            if (a !== 0) {
                $('.service_' + (a - 1)).html(" &raquo; 运行 ").css({"color": "green"});
            } else {
                $(".service_stop").html("停止").css({"color": "red"});
            }
            $('.startCommand').append(b + " <span class='service_" + a + "' style='color: yellow'> &raquo; 启动...</span><br>");
            if (startCommandEach.length - 1 === a) return true;
        }, a * 900);
    });
    startCommandEach = "";
    $.ajax({
        type: "POST", url: adr + "/index.php?function=restartServer", success: function (data) {
            notify(data);
            $('#myModal').modal('hide');
            $('.startCommand').html("");
        }
    });
});

function goTeleport() {
    var id = $("input[name=charIDFunc]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=teleport",
        data: {id: id},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);

        }
    });
}

function kickRole(id) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=kickrole",
        data: {id: id},
        success: function (data) {
            notify(data);
        }
    });
}

function goNullChar() {
    var id = $("input[name=charIDFunc]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=nullchar",
        data: {id: id},
        success: function (data) {
            $("#systemMessage").html(data);
            ;
            notify(data);
        }
    });
}

function goNullPasswd() {
    var id = $("input[name=charIDFunc]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=nullpasswd",
        data: {id: id},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);
        }
    });
}

function goDelChar(ida) {
    //console.log($("input[name=charIDFunc]").val());
    var id;
    if (ida === undefined)
        id = $("input[name=charIDFunc]").val();
    else
        id = ida;


    var result = confirm("请确认是否删除此角色 ID " + id);
    if (result) {
        $.ajax({
            type: "POST",
            url: adr + "/index.php?function=delrole",
            data: {id: id},
            success: function (data) {
                notify(data);
            }
        });
    }
}

function goRenameRole() {
    var id = $("input[name=renameIDRole]").val();
    var oldName = $("input[name=renameOldNameRole]").val();
    var newName = $("input[name=renameNewNameRole]").val();

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=renamerole",
        data: {id: id, oldname: oldName, newname: newName},
        success: function (data) {
            notify(data);
        }
    });

}

function goLevelUp() {
    var id = $("input[name=charIDLevelUp]").val();
    var level = $("input[name=charLevelUp]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=levelup",
        data: {id: id, level: level},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);

        }
    });
}

function goAddCash() {
    var id = $("input[name=charIDCash]").val();
    var gold = $("input[name=countCash]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addcash",
        data: {id: id, gold: gold*100},
        success: function (data) {
            notify(data);
        }
    });
}

function goEdit(type) {
    var id = $("input[name=charIDEdit]").val();
    if (type == "xml") location.href = adr + "/?controller=editor&page=xml&id=" + id; else location.href = adr + "/?controller=editor&id=" + id;
}

function tryint(int) {
    var objAr = [];
    while (int != 0) {
        if (int >= 262144) {
            int -= 131072;
            objAr.push(131072);
        }
        if (int >= 131072 && int < 262144) {
            int -= 131072;
            objAr.push(131072);
        }
        if (int >= 65536 && int < 131072) {
            int -= 65536;
            objAr.push(65536);
        }
        if (int >= 32768 && int < 65536) {
            int -= 32768;
            objAr.push(32768);
        }
        if (int >= 16384 && int < 32768) {
            int -= 16384;
            objAr.push(16384);
        }
        if (int >= 8192 && int < 16384) {
            int -= 8192;
            objAr.push(8192);
        }
        if (int >= 4096 && int < 8192) {
            int -= 4096;
            objAr.push(4096);
        }
        if (int >= 2048 && int < 4096) {
            int -= 2048;
            objAr.push(2048);
        }
        if (int >= 1024 && int < 2048) {
            int -= 1024;
            objAr.push(1024);
        }
        if (int >= 512 && int < 1024) {
            int -= 512;
            objAr.push(512);
        }
        if (int >= 256 && int < 512) {
            int -= 256;
            objAr.push(256);
        }
        if (int >= 128 && int < 256) {
            int -= 128;
            objAr.push(128);
        }
        if (int >= 64 && int < 128) {
            int -= 64;
            objAr.push(64);
        }
        if (int >= 32 && int < 64) {
            int -= 32;
            objAr.push(32);
        }
        if (int >= 16 && int < 32) {
            int -= 16;
            objAr.push(16);
        }
        if (int >= 8 && int < 16) {
            int -= 8;
            objAr.push(8);
        }
        if (int >= 4 && int < 8) {
            int -= 4;
            objAr.push(4);
        }
        if (int >= 2 && int < 4) {
            int -= 2;
            objAr.push(2);
        }
        if (int >= 1 && int < 2) {
            int -= 1;
            objAr.push(1);
        }
    }
    return objAr;
}

function modal_mask() {
    var int = $("#prototypeItem").val();
    var objMask = tryint(int);
    $.each(objMask, function (indexrr, value) {
        $("input[name=mask_" + value + "]").attr('checked', true);
    });
    $("#modalForm")[0].reset();
}

var result = 0;
$("#accept").on("click", function () {
    $("input[class='custom-checkbox']:checked").each(function () {
        result = result + parseInt($(this).val());
    });
    $("#prototypeItem").val(result);
    $("#windows").html("").css({'display': 'none'});
    result = 0;

});

function getChars(accountID) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=getrole",
        data: {id: accountID},
        success: function (data) {
            $("#charListModal").html(data);
        }
    });
}

var GM_accountID = 0;

function addCash(accountID) {
    GM_accountID = accountID;
    $("#modalForm")[0].reset();
}

$("#acceptGold").on("click", function () {
    var zoneid = $("input[name=zoneidSend]").val();
    var gold = $("input[name=goldCountSend]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addcash",
        data: {id: GM_accountID, zoneid: zoneid, gold: gold*100},
        success: function (data) {
            notify(data);
        }
    });
    GM_accountID = 0;
});

//设置元宝交易密码
var GM_accountID = 0;

function SetCashPassword(accountID) {
    GM_accountID = accountID;
    $("#modalForm")[0].reset();
}

$("#setcashpassword").on("click", function () {
    var Password = $("input[name=PasswordSend]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=cashpassword",
        data: {id: GM_accountID, Password: Password},
        success: function (data) {
            notify(data);
        }
    });
    GM_accountID = 0;
});
//结束

//安全锁
var GM_accountID = 0;
var locktime = 0;

function IWebAutolockSet(accountID) {
    GM_accountID = accountID;
    $("#modalForm")[0].reset();
}

$("#AutolockSet").on("click", function () {
    var locktime = $("input[name=locktime]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=autolock",
        data: {id: GM_accountID, locktime: locktime},
        success: function (data) {
            notify(data);
        }
    });
    GM_accountID = 0;
});
//结束

$("#addUserGo").on("click", function () {
    var username = $("input[name=addUsername]").val();
    var password = $("input[name=addPassword]").val();
    var email = $("input[name=addEmail]").val();
    var type_password = $("select[name=typePassword]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=adduserserver",
        data: {username: username, password: password, email: email, type_password: type_password},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
    $("#modalForm2")[0].reset();
});

$("#ChangePasswdGo").on("click", function () {
    var username2 = $("input[name=addUsername2]").val();
    var password2 = $("input[name=addPassword2]").val();
    var type_password2 = $("select[name=typePassword2]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=changepasswd",
        data: {username2: username2, password2: password2, type_password2: type_password2},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
    $("#modalForm2")[0].reset();
});

$("#addZoneidCashGo").on("click", function () {
    var Zoneid = $("input[name=Zoneid]").val();
    var Username = $("input[name=Username]").val();
    var Cashcount = $("input[name=Cashcount]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addzoneidcash",
        data: {Zoneid: Zoneid, Username: Username, Cashcount: Cashcount*100},
        success: function (data) {
            // $("#systemMessage").html(data);
            notify(data);
        }
    });
    $("#modalForm2")[0].reset();
});

function editGM(accountID) {
    $('input:checkbox').prop("checked", false);
    GM_accountID = accountID;
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=checkGM",
        data: {id: GM_accountID},
        success: function (data) {
            data = JSON.parse(data);
            $.each(data, function (key, value) {
                $("input[name=gm-" + value + "]").prop("checked", true);
            });

        }
    });
    //$("#modalForm1")[0].reset();
}

$("button[name=selectall]").on('click', function () {
    $('input:checkbox').prop("checked", true);
});

$("button[name=unselectall]").on('click', function () {
    $('input:checkbox').prop("checked", false);
});

$("#addGM").on("click", function () {
    var checkedValue = {};
    var inputElements = document.getElementsByClassName('custom-checkbox');
    for (var i = 0; inputElements[i]; ++i) {
        if (inputElements[i].checked) checkedValue[i] = inputElements[i].value;
    }
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=managerGM",
        data: {id: GM_accountID, params: checkedValue},
        success: function (data) {
            notify(data);
        }
    });

    GM_accountID = 0;
});

function pTypeItem() {
    var int = $('input[name="item[proctype]"]').val();
    var objMask = tryint(int);
    $.each(objMask, function (indexrr, value) {
        $("input[name=mask_" + value + "]").attr('checked', true);
    });

    var result = 0;
    $("#accept").on("click", function () {
        $("input[class='custom-checkbox']:checked").each(function () {
            result = result + parseInt($(this).val());
        });
        $('input[name="item[proctype]"]').val(result);
        result = 0;

    });

    $("#modalForm")[0].reset();
}

$("#startLoc").on("click", function () {
    var locations = $("#exampleSelect2").val();
    //console.log(locations);
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=start_location",
        data: {oneQuery: $("input[name=oneQuery]").prop("checked"), locations: locations},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);
        }
    });
});

var ch = false;
$("#select-all").change(function () {
    //alert($("#select-all").checked);
    if (ch) {
        $("#location").attr("checked", false);
        ch = false;
    } else {
        $("#location").each(function () {
            $("#location").attr("checked", true);
        });
        ch = true;

    }
});

$("#checkAll").change(function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});

function killLocs() {
    var yourArray = [];
    $("#location:checked").each(function () {
        yourArray.push($(this).attr("data-pid"));
    });
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=killselectloc",
        data: {locations: yourArray},
        success: function (data) {
            /// $("#systemMessage").html(data);
            //data = JSON.parse(data);
            $.each(yourArray, function (key, value) {
                // $("#" + key).val(value);
                //$("#systemMessage").html(data);;
                $("#loc-" + value).remove();
            })
        }
    });
    // console.log(yourArray);
}

// $("button[data-dismiss=modal]").on("click", function () {
//     $("#modalForm")[0].reset();
// });


var keyItem = 0;
var jsonItem = "";
var inputItem = "";
var typeItem = 0;

function getTime(time) {
    var timeDay = Math.floor(time / 86400); //Day
    var timeHour = Math.floor((time % 86400) / 3600); //HH
    var timeMin = Math.floor((time % 3600) / 60); //MM
    var timeSec = Math.floor(time % 60); //SS

    if (timeHour.toString().length === 1)
        timeHour = "0" + timeHour;

    if (timeMin.toString().length === 1)
        timeMin = "0" + timeMin;

    if (timeSec.toString().length === 1)
        timeSec = "0" + timeSec;


    var returnData = timeHour + ":" + timeMin + ":" + timeSec;

    if (timeDay > 0)
        returnData = timeDay + "д " + returnData;

    return returnData;
}

function getTimeExpire() {
    $(".timeEx").text(getTime($("input[name='item[expire_date]']").val()));
}

function editPocketItem(idKey, type, list) {
    typeItem = type;
    keyItem = idKey;


    if (type === 0)
        inputItem = $("input[data-name=packetItems]");
    else if (type === 1)
        inputItem = $("input[data-name=eqpItems]");
    else if (type === 2)
        inputItem = $("input[data-name=storeItems]");
    else if (type === 3)
        inputItem = $("input[data-name=dressItems]");
    else if (type === 4)
        inputItem = $("input[data-name=cardItems]");
    else if (type === 5)
        inputItem = $("input[data-name=materialItems]");


    var oct = $(".octetEdit");
    oct.html("");


    jsonItem = JSON.parse(inputItem.val());

    switch (list) {
        case 3:
            oct.html("<a class=\"badge badge-success m-2\" href='javascript:void(0)' data-toggle=\"modal\" onclick='editOctet(\"weapon\")' data-target=\"#editWeaponOctet\">编辑</a>")
            break;
        case 22:
            oct.html("<a class=\"badge badge-success m-2\" href='javascript:void(0)' data-toggle=\"modal\" onclick='editOctet(\"fly\")' data-target=\"#editFlyOctet\">编辑</a>")
            break;
        case 83:
            oct.html("<a class=\"badge badge-success m-2\" href='javascript:void(0)' data-toggle=\"modal\" onclick='editOctet(\"fashion\")' data-target=\"#editFashionOctet\">编辑</a>")
            break;
        case 184:
            oct.html("<a class=\"badge badge-success m-2\" href='javascript:void(0)' data-toggle=\"modal\" onclick='editOctet(\"card\")' data-target=\"#editCardOctet\">编辑</a>")
            break;
    }

    $.each(jsonItem[keyItem], function (a, b) {
        $('input[name="item[' + a + ']"]').val(b['value']);
    });

    $(".timeEx").text("");
    $(".timeEx").text(getTime($("input[name='item[expire_date]']").val()));
}

var oParam1 = "";
var oParam2 = "";
var OctetData = "";

var weaponBonusArr = [
    {"id": "0", "type": "0", "stat": "0", "name": "选择...", "desc": ""},
    {"id": "762", "type": "8192", "stat": "0", "name": "物理攻击 +", "desc": ""},
    {"id": "773", "type": "8192", "stat": "0", "name": "物理攻击上限 +", "desc": ""},
    {"id": "417", "type": "8192", "stat": "0", "name": "物理攻击 +%", "desc": ""},
    {"id": "820", "type": "8192", "stat": "0", "name": "法术攻击 +", "desc": ""},
    {"id": "829", "type": "8192", "stat": "0", "name": "法术攻击上限 +", "desc": ""},
    {"id": "438", "type": "8192", "stat": "0", "name": "法术攻击 +%", "desc": ""},
    {"id": "441", "type": "8192", "stat": "0", "name": "吟唱时间 -%", "desc": ""},
    {"id": "331", "type": "8192", "stat": "0", "name": "攻击间隔 -秒", "desc": ""},
    {"id": "473", "type": "8192", "stat": "0", "name": "致命一击率 +%", "desc": ""},
    {"id": "1272", "type": "8192", "stat": "0", "name": "物理免疫 +%", "desc": ""},
    {"id": "2503", "type": "8192", "stat": "0", "name": "五行免疫 +%", "desc": ""},
    {"id": "1311", "type": "8192", "stat": "0", "name": "命中 +", "desc": ""},
    {"id": "3236", "type": "8192", "stat": "0", "name": "气魄 +", "desc": ""},
    {"id": "497", "type": "8192", "stat": "0", "name": "生命值 +", "desc": ""},
    {"id": "502", "type": "8192", "stat": "0", "name": "真气值 +", "desc": ""},
    {"id": "2029", "type": "8192", "stat": "0", "name": "攻击等级 +", "desc": ""},
    {"id": "2085", "type": "8192", "stat": "0", "name": "防御等级 +", "desc": ""},
    {"id": "1104", "type": "8192", "stat": "0", "name": "力量 +", "desc": ""},
    {"id": "1109", "type": "8192", "stat": "0", "name": "敏捷 +", "desc": ""},
    {"id": "1119", "type": "8192", "stat": "0", "name": "体质 +", "desc": ""},
    {"id": "1114", "type": "8192", "stat": "0", "name": "灵力 +", "desc": ""}
];


function editOctet(type) {

    switch (typeItem) {
        case 0:
            oParam1 = "pocket";
            oParam2 = "inv";
            break;
        case 1:
            oParam1 = "equipment";
            oParam2 = "eqp";
            break;
        case 2:
            oParam1 = "storehouse";
            oParam2 = "store";
            break;
        case 3:
            oParam1 = "storehouse";
            oParam2 = "dress";
            break;
        case 4:
            oParam1 = "storehouse";
            oParam2 = "card";
            break;
        case 5:
            oParam1 = "storehouse";
            oParam2 = "material";
            break;

    }

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=readFly",
        data: {
            id: $("input[name='id']").val(),
            key: keyItem,
            param1: oParam1,
            param2: oParam2,
            type: type
        },
        success: function (data) {
            $("#systemMessage").html(data);
            OctetData = JSON.parse(data);
            var cellName = "";
            var bonusName = "";

            $.each(OctetData, function (key, value) {
                if (key === "cellInfo") {
                    $("#cellWeapon").html("");
                    // console.clear();
                    $.each(value, function (c, d) {
                        if (c === "cellStone") {
                            $.each(d, function (a, b) {
                                if (b['id']['value'] > 0)
                                    cellName = "魂石 " + b['id']['value'];
                                else
                                    cellName = "空";
                                $("#cellWeapon").append("<option value='" + a + "'>" + cellName + "</option>");
                            })
                        }
                    })
                } else if (key === "BonusInfo") {
                    $("#bonusWeapon").html("");
                    $.each(value, function (c, d) {
                        if (c === "bonus") {
                            $.each(d, function (a, b) {
                                if (b['type']['value'] === 40960) {
                                    bonusName = "魂石 " + b['id']['value']
                                } else if (b['type']['value'] === 16384) {
                                    bonusName = "技能 " + b['id']['value']
                                } else {
                                    bonusName = b['id']['value'];
                                }
                                $("#bonusWeapon").append("<option value='" + a + "'>" + bonusName + "</option>");
                            })
                        }
                    })
                } else {
                    if (key === "WeaponClass") {
                        console.log(value['value']);
                        $("select[name='" + type + "[" + key + "]'] [value='" + +value['value'] + "']").prop("selected", true)
                    } else
                        $("input[name='" + type + "[" + key + "]']").val(value['value']);

                }
            })
        }
    });
}

function writeOctet(type) {
    $.each($('[name^="' + type + '\\["]').serializeArray(), function () {
        var visual = this.name.replace(type + "[", "");
        visual = visual.replace("]", "");
        OctetData[visual]['value'] = this.value;
    });
    // console.log(OctetData);
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=writeFly",
        data: {
            data: JSON.stringify(OctetData)
            // id: $("input[name='id']").val(),
            // key: keyItem,
            // param1: oParam1,
            // param2: oParam2
        },
        success: function (data) {
            // $("#systemMessage").html(data);
            jsonItem[keyItem]['value'] = data;
            $('input[name="item[data]"]').val(data);
            //inputItem.val(jsonItem);
        }
    });
}

function addCell() {
    if (OctetData["cellInfo"]["cellStone"] === undefined) {
        //OctetData["cellInfo"] = {"cellFlag": {"value":0, "type":"int16sm"}};
        OctetData["cellInfo"]["cellStone"] = [];
        console.log(OctetData);
    }
    if (OctetData["cellInfo"]["cellStone"].length < 4) {
        var inputCell = $("#cellWeapon");
        OctetData["cellInfo"]["cellStone"].push({"id": {"value": "0", "type": "int32sm"}});
        console.log(OctetData);
        //var last = OctetData["cellInfo"]["cellStone"].last();

        inputCell.append("<option value='" + OctetData["cellInfo"]["cellStone"].length + "'>空</option>");
    } else {
        alert("msx 4 cell");
    }

}

function removeCell() {
    var inputCell = $("#cellWeapon");
    //delete OctetData["cellInfo"]["cellStone"]];
    if (OctetData["cellInfo"]["cellStone"].length > 0) {
        OctetData["cellInfo"]["cellStone"].splice(inputCell.val(), 1);
    } else {
        OctetData["cellInfo"].splice("cellStone", 1);
    }
    var val = inputCell.val();
    $("#cellWeapon [value='" + val + "']").remove();
    $("#bonusWeapon option[value='" + (val[0] - 1) + "']").prop("selected", true)

    //console.log(inputCell.val());
    //console.log(OctetData["cellInfo"]);
}

function addBonus() {
    var inputBonus = $("#bonusWeapon");
    if (OctetData["BonusInfo"]["bonus"] === undefined) {
        OctetData["BonusInfo"]["bonus"] = [];
    }

    //var test = {"id":{"type":"int32sm","value":1421},"type":{"type":0,"value":8192},"stat":{"type":"int32sm","value":4}};
    //var test = {"id":{"type":"int32sm","value":331},"type":{"type":0,"value":8192},"stat":{"type":"int32sm","value":2}};
    var id = $("input[name='bonus[id]']").val();
    var type = $("input[name='bonus[type]']").val();
    var stat = $("input[name='bonus[stat]']").val();
    var data = {
        "id": {"type": "int32sm", "value": id},
        "type": {"type": 0, "value": type},
        "stat": {"type": "int32sm", "value": stat}
    };
    OctetData['BonusInfo']['bonus'].push(data);
    inputBonus.append("<option value='" + OctetData["BonusInfo"]["bonus"].length + "'>" + id + "</option>");
}

function loadListBonus(type) {
    $("[name='addBonusList']").html("");
    if (type === "weapon") {
        $.each(weaponBonusArr, function (key, val) {
            $("[name='addBonusList']").append("<option value='" + key + "'>" + val.name + "</option>");
        });
    }
}

function applyBonus(type, who) {
    var bonus;
    if (type === "weapon") {
        var listbomus = $("[name='addBonusList']").val();
        bonus = weaponBonusArr[listbomus];
    }
    //console.log(listbomus);
    $("input[name='" + who + "[id]']").val(bonus.id);
    $("input[name='" + who + "[type]']").val(bonus.type);
    $("input[name='" + who + "[stat]']").val(bonus.stat);
}

var editKeyBonus = -1;

function editBonus() {
    loadListBonus('weapon');
    $('#editBonus').modal('show', {focus: true});
    var inputBonus = $("#bonusWeapon");
    editKeyBonus = inputBonus.val();
    $.each(OctetData["BonusInfo"]["bonus"][editKeyBonus], function (key, value) {
        $("input[name='editBonus[" + key + "]']").val(value['value']);
    })
}

function editBonusSave() {
    //console.log(editKeyBonus);

    var id = $("input[name='editBonus[id]']").val();
    var type = $("input[name='editBonus[type]']").val();
    var stat = $("input[name='editBonus[stat]']").val();
    OctetData["BonusInfo"]["bonus"][editKeyBonus]["id"]["value"] = id;
    OctetData["BonusInfo"]["bonus"][editKeyBonus]["type"]["value"] = type;
    OctetData["BonusInfo"]["bonus"][editKeyBonus]["stat"]["value"] = stat;
    $("#bonusWeapon option[value='" + editKeyBonus + "']").text("xer" + id);
    editKeyBonus = -1;
}

function removeBonus() {
    var inputCell = $("#bonusWeapon");
    if (OctetData["BonusInfo"]["bonus"].length > 0) {
        OctetData["BonusInfo"]["bonus"].splice(inputCell.val(), 1);
    } else {
        // OctetData["BonusInfo"].splice("bonus", 1);
    }
    //console.log(inputCell.val()[0]-1);
    var val = inputCell.val();
    $("#bonusWeapon [value='" + val + "']").remove();
    $("#bonusWeapon option[value='" + (val[0] - 1) + "']").prop("selected", true)


}

var resultCls = 0;
var clsInput = "";

function charClass(input) {
    resultCls = 0;
    $(".clsChar").prop('checked', false);
    clsInput = input;
    var int = $("input[name='" + clsInput + "']").val();
    var objMask = tryint(int);
    $.each(objMask, function (indexrr, value) {
        $("input[name=mask_" + value + "]").prop('checked', true);
    });
}

$("#setClsChar").on("click", function () {
    $(".clsChar:checked").each(function (a, b) {
        resultCls = resultCls + parseInt($(this).val());
    });

    $("input[name='" + clsInput + "']").val(resultCls);
    clsInput = "";
});

$("#acceptDel").on('click', function () {
    if (typeItem === 0)
        $("[data-key=" + keyItem + "]").remove();
    if (typeItem === 1)
        $("[data-key-eqp=" + keyItem + "]").remove();
    if (typeItem === 2)
        $("[data-key-store=" + keyItem + "]").remove();
    if (typeItem === 3)
        $("[data-key-card=" + keyItem + "]").remove();

    jsonItem.splice(keyItem, 1);
    console.log("delete item: " + keyItem);
    console.log(jsonItem);
    //$('body>.tooltip').remove();
    inputItem.val(JSON.stringify(jsonItem));
    $("#modalForm")[0].reset();
    jsonItem = "";
    inputItem = "";
    typeItem = "";
    keyItem = 0;
});

// $("input[name='item[expire_date]']").on("keyup",function () {
//     var getTime = $("input[name='item[expire_date]']").val();
//     var time = moment().startOf('day')
//         .seconds(getTime)
//         .format('D[д] H:mm:ss');
//     $(".timeEx").html("<i style='color: lime'>"+time+"</i>");
// });

$("#acceptSave").on('click', function () {
    //input.val(JSON.stringify(json));
    $.each($('input[name^="item\\["]').serializeArray(), function () {
        var visual = this.name.match(/item\[(.*)\]/)[1];
        jsonItem[keyItem][visual]['value'] = this.value;
        console.log(visual + " - " + keyItem);
    });
    inputItem.val(JSON.stringify(jsonItem));
    $("#modalForm")[0].reset();
    typeItem = "";
    jsonItem = "";
    inputItem = "";
    keyItem = 0;
});

function selectItemMail() {
    var id = $("select[name=lastItems]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=getmailitems",
        data: {id: id},
        success: function (data) {
            ////$("#systemMessage").html(data);
            data = JSON.parse(data);
            $.each(data, function (key, value) {
                $("#" + key).val(value);
            })
        }
    });
}

function goRead() {
    var loader = $(".loaderEl");

    loader.html("<b>正在加载物品...</b>");
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=goreadelement",
        success: function (data) {
            $("#systemMessage").html(data);
            loader.html("");
            notify(data);
        }
    });
}

function uploadIconItems() {
    var loader = $(".loaderIc");
    loader.html("<b>正在加载图标...</b>");
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=gouploadicon",
        success: function (data) {
            $("#systemMessage").html(data);
            loader.html("");
            notify(data);
        }
    });
}

var statusChatBlock = true;

function chengeChl(id, type) {
    if (type === undefined) {
        if (statusChatBlock === true) {
            $(".chatW").show();
            $(".chatWindow").removeClass("hideChatWindow");
            $(".chatHead").removeClass("hideChatWindow");
            statusChatBlock = false;
        }
    }
    var chat = $("[data-target=chatMessage]");
    chat.attr('data-chl', id);
    startChat();
    chat.scrollTop(chat.prop('scrollHeight'));
}

function startChat() {
    var chat = $("[data-target=chatMessage]");
    $.ajax({
        url: adr + '/index.php?function=getchat&chl=' + chat.attr('data-chl'),
        type: 'GET',
        success: function (data) {
            chat.html(data);
        }
    });

}

function showHideChat() {
    if (statusChatBlock === false) {
        $(".chatW").hide();
        $(".chatWindow").addClass("hideChatWindow");
        $(".chatHead").addClass("hideChatWindow");
        statusChatBlock = true;
    } else {
        $(".chatW").show();
        $(".chatWindow").removeClass("hideChatWindow");
        $(".chatHead").removeClass("hideChatWindow");
        statusChatBlock = false;
    }
}

//ban acc/chat/char
var ban_id = 0;
var ban_type = 0;

function ban(id, type) {
    ban_id = id;
    ban_type = type;
    $("#modalForm")[0].reset();
}

$("#acceptBan").on("click", function () {
    var time = $('input[name="banTime"]').val();
    var reason = $('input[name="banReason"]').val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=ban",
        data: {id: ban_id, type: ban_type, time: time, reason: reason},
        success: function (data) {
            //$("#systemMessage").html(data);;
            notify(data);
        }
    });
    ban_id = 0;
    ban_type = 0;
});

//end ban acc/chat/char

function killLocation(pid) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=killpid",
        data: {pid: pid},
        success: function (data) {
            $("#loc-" + pid).remove();
            //$("#systemMessage").html(data);;
            //notify(data);
        }
    });
}

function saveSettings() {
    var save = {};
    $.each($('textarea,input[name^="config\\["],select[name^="config\\["]').serializeArray(), function () {
        var visual = this.name.replace("config[", '');
        //console.log(visual);
        save[visual] = this.value;
    });

    $.ajax({
        url: adr + "/index.php?function=savesettings",
        type: "POST",
        data: {
            config: save
        },
        success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
}

$("#addUserIweb").on("click", function () {
    var username = $("input[name=iwebUsername]").val();
    var password = $("input[name=iwebPassword]").val();
    var group = $("select[name=iwebGroup]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addUserIweb",
        data: {username: username, password: password, group: group},
        success: function (data) {
            //$("#systemMessage").html(data);
            console.log(data);
            var parsdata = JSON.parse(data);
            if (parsdata.type === "success") {
                $("#usersIweb").append("<tr id='user_" + parsdata.id + "'>" +
                    "<td>" + parsdata.id + "</td>" +
                    "<td>" + parsdata.username + "</td>" +
                    "<td>" + parsdata.group + "</td>" +
                    "<td class=\"text-right\">\n" +
                    "            <button onclick='updateUser(" + parsdata.id + ")' class='btn btn-sm btn-success' href='javascript:void(0)' data-toggle=\"modal\" data-target=\"#updateUser\">编辑</button>\n" +
                    "            <button onclick='removeUser(" + parsdata.id + ")' class='btn btn-sm btn-danger'>删除</button>\n" +
                    "        </td></tr>");
            }
            notify(data);
        }
    });
    $("#modalForm2")[0].reset();
});

var idUser;

function updateUser(id) {
    idUser = id;
    $.ajax({
        type: "POST", url: adr + "/index.php?function=getUserIweb", data: {id: id}, success: function (data) {
            data = JSON.parse(data);
            $("input[name=updateUsername]").val(data.name);
            $("select[name=updateGroup] [value='" + data.group_id + "']").attr("selected", "selected");
        }
    });
}

function removeUser(id) {
    var result = confirm("请确认是否删除此管理员?");
    if (result) {
        $.ajax({
            type: "POST", url: adr + "/index.php?function=delUserIweb", data: {id: id}, success: function (data) {
                var parsdata = JSON.parse(data);
                if (parsdata.type === "success") {
                    $("#user_" + id).remove();
                }
                notify(data);
            }
        });
    }
}

$("#updateUserIweb").on("click", function () {
    var username = $("input[name=updateUsername]").val();
    var password = $("input[name=updatePassword]").val();
    var group = $("select[name=updateGroup]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=updateUserIweb",
        data: {id: idUser, username: username, password: password, group: group},
        success: function (data) {
            notify(data);
        }
    });
    idUser = 0;
    $("#modalForm2")[0].reset();
});

$("#addGroupIweb").on("click", function () {
    var iwebTitle = $("input[name=iwebTitle]").val();
    var xml_edit = $("input[name=xml_edit]").prop("checked") ? 1 : 0;
    var visual_edit = $("input[name=visual_edit]").prop("checked") ? 1 : 0;
    var gm_manager = $("input[name=gm_manager]").prop("checked") ? 1 : 0;
    var kick_role = $("input[name=kick_role]").prop("checked") ? 1 : 0;
    var ban = $("input[name=ban]").prop("checked") ? 1 : 0;
    var add_gold = $("input[name=add_gold]").prop("checked") ? 1 : 0;
    var level_up = $("input[name=level_up]").prop("checked") ? 1 : 0;
    var rename_role = $("input[name=rename_role]").prop("checked") ? 1 : 0;
    var teleport = $("input[name=teleport]").prop("checked") ? 1 : 0;
    var null_exp_sp = $("input[name=null_exp_sp]").prop("checked") ? 1 : 0;
	var null_passwd = $("input[name=null_passwd]").prop("checked") ? 1 : 0;
    var del_role = $("input[name=del_role]").prop("checked") ? 1 : 0;
    var server_manager = $("input[name=server_manager]").prop("checked") ? 1 : 0;
    var send_msg = $("input[name=send_msg]").prop("checked") ? 1 : 0;
    var send_mail = $("input[name=send_mail]").prop("checked") ? 1 : 0;
    var settings = $("input[name=settings]").prop("checked") ? 1 : 0;
    var logs = $("input[name=logs]").prop("checked") ? 1 : 0;

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addGroupIweb",
        data: {
            iwebTitle: iwebTitle,
            xml_edit: xml_edit,
            visual_edit: visual_edit,
            gm_manager: gm_manager,
            kick_role: kick_role,
            ban: ban,
            add_gold: add_gold,
            level_up: level_up,
            rename_role: rename_role,
            teleport: teleport,
            null_exp_sp: null_exp_sp,
			null_passwd: null_passwd,
            del_role: del_role,
            server_manager: server_manager,
            send_msg: send_msg,
            send_mail: send_mail,
            settings: settings,
            logs: logs
        },
        success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
    $("#modalForm2")[0].reset();
});

var idGroup;

function updateGroup(id) {
    idGroup = id;
    $.ajax({
        type: "POST", url: adr + "/index.php?function=getGroupIweb", data: {id: idGroup}, success: function (data) {
            data = JSON.parse(data);
            console.log(data);


            $("input[name=upd_iwebTitle]").val(data.title);

            var xml_edit = $("input[name=upd_xml_edit]");
            var visual_edit = $("input[name=upd_visual_edit]");
            var gm_manager = $("input[name=upd_gm_manager]");
            var kick_role = $("input[name=upd_kick_role]");
            var ban = $("input[name=upd_ban]");
            var add_gold = $("input[name=upd_add_gold]");
            var level_up = $("input[name=upd_level_up]");
            var rename_role = $("input[name=upd_rename_role]");
            var teleport = $("input[name=upd_teleport]");
            var null_exp_sp = $("input[name=upd_null_exp_sp]");
			var null_passwd = $("input[name=upd_null_passwd]");
            var del_role = $("input[name=upd_del_role]");
            var server_manager = $("input[name=upd_server_manager]");
            var send_msg = $("input[name=upd_send_msg]");
            var send_mail = $("input[name=upd_send_mail]");
            var settings = $("input[name=upd_settings]");
            var logs = $("input[name=upd_logs]");

            (data.xml_edit === "1") ? xml_edit.prop("checked", true) : xml_edit.prop("checked", false);
            (data.visual_edit === "1") ? visual_edit.prop("checked", true) : visual_edit.prop("checked", false);
            (data.gm_manager === "1") ? gm_manager.prop("checked", true) : gm_manager.prop("checked", false);
            (data.kick_role === "1") ? kick_role.prop("checked", true) : kick_role.prop("checked", false);
            (data.ban === "1") ? ban.prop("checked", true) : ban.prop("checked", false);
            (data.add_gold === "1") ? add_gold.prop("checked", true) : add_gold.prop("checked", false);
            (data.level_up === "1") ? level_up.prop("checked", true) : level_up.prop("checked", false);
            (data.rename_role === "1") ? rename_role.prop("checked", true) : rename_role.prop("checked", false);
            (data.teleport === "1") ? teleport.prop("checked", true) : teleport.prop("checked", false);
            (data.null_exp_sp === "1") ? null_exp_sp.prop("checked", true) : null_exp_sp.prop("checked", false);
			(data.null_passwd === "1") ? null_passwd.prop("checked", true) : null_passwd.prop("checked", false);
            (data.del_role === "1") ? del_role.prop("checked", true) : del_role.prop("checked", false);
            (data.server_manager === "1") ? server_manager.prop("checked", true) : server_manager.prop("checked", false);
            (data.send_msg === "1") ? send_msg.prop("checked", true) : send_msg.prop("checked", false);
            (data.send_mail === "1") ? send_mail.prop("checked", true) : send_mail.prop("checked", false);
            (data.settings === "1") ? settings.prop("checked", true) : settings.prop("checked", false);
            (data.logs === "1") ? logs.prop("checked", true) : logs.prop("checked", false);
        }
    });
    //$("#modalForm4")[0].reset();
}

function removeGroup(id) {
    var result = confirm("请确认是否删除此用户组?");
    if (result) {
        $.ajax({
            type: "POST", url: adr + "/index.php?function=delGroupIweb", data: {id: id}, success: function (data) {
                var parsdata = JSON.parse(data);
                if (parsdata.type === "success") {
                    $("#group_" + id).remove();
                }
                notify(data);
            }
        });
    }
}

$("#updateGroupIweb").on("click", function () {
    var iwebTitle = $("input[name=upd_iwebTitle]").val();
    var xml_edit = $("input[name=upd_xml_edit]").prop("checked") ? 1 : 0;
    var visual_edit = $("input[name=upd_visual_edit]").prop("checked") ? 1 : 0;
    var gm_manager = $("input[name=upd_gm_manager]").prop("checked") ? 1 : 0;
    var kick_role = $("input[name=upd_kick_role]").prop("checked") ? 1 : 0;
    var ban = $("input[name=upd_ban]").prop("checked") ? 1 : 0;
    var add_gold = $("input[name=upd_add_gold]").prop("checked") ? 1 : 0;
    var level_up = $("input[name=upd_level_up]").prop("checked") ? 1 : 0;
    var rename_role = $("input[name=upd_rename_role]").prop("checked") ? 1 : 0;
    var teleport = $("input[name=upd_teleport]").prop("checked") ? 1 : 0;
    var null_exp_sp = $("input[name=upd_null_exp_sp]").prop("checked") ? 1 : 0;
	var null_passwd = $("input[name=upd_null_passwd]").prop("checked") ? 1 : 0;
    var del_role = $("input[name=upd_del_role]").prop("checked") ? 1 : 0;
    var server_manager = $("input[name=upd_server_manager]").prop("checked") ? 1 : 0;
    var send_msg = $("input[name=upd_send_msg]").prop("checked") ? 1 : 0;
    var send_mail = $("input[name=upd_send_mail]").prop("checked") ? 1 : 0;
    var settings = $("input[name=upd_settings]").prop("checked") ? 1 : 0;
    var logs = $("input[name=upd_logs]").prop("checked") ? 1 : 0;

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=updateGroupIweb",
        data: {
            id: idGroup,
            title: iwebTitle,
            xml_edit: xml_edit,
            visual_edit: visual_edit,
            gm_manager: gm_manager,
            kick_role: kick_role,
            ban: ban,
            add_gold: add_gold,
            level_up: level_up,
            rename_role: rename_role,
            teleport: teleport,
            null_exp_sp: null_exp_sp,
			null_passwd: null_passwd,
            del_role: del_role,
            server_manager: server_manager,
            send_msg: send_msg,
            send_mail: send_mail,
            settings: settings,
            logs: logs
        },
        success: function (data) {
            $("#systemMessage").html(data);
            notify(data);
        }
    });
    idGroup = 0;
    $("#modalForm4")[0].reset();
});

function restartService(serviceName) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=restartService",
        data: {service: serviceName},
        success: function (data) {
            notify(data);
        }
    });
}

function stopService(serviceName) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=stopService",
        data: {service: serviceName},
        success: function (data) {
            notify(data);
            //$("#systemMessage").html(data);

        }
    });
}

function startService(serviceName) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=startService",
        data: {service: serviceName},
        success: function (data) {
            notify(data);
        }
    });
}

function defPosition(event) {
    var x = y = 0;
    if (document.attachEvent != null) {
        x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
        y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    } else if (!document.attachEvent && document.addEventListener) {
        x = event.clientX + window.scrollX;
        y = event.clientY + window.scrollY;
    } else {
    }
    return {x: x, y: y};
}

function menu(type, evt) {
    //console.log(evt.path[1]);
    evt = evt || window.event;
    evt.cancelBubble = true;
    var menu = $("#contextMenuId");
    var html = "";

    if ($("." + type + "_count").text() === "0") {
        html += "<a class=\"dropdown-item\" onclick='startService(\"" + type + "\")' href=\"javascript:void(0)\"><i class='fa fa-play' style='color: green'></i> 启动</a>";
    }else {
        if (type !== "gs") {
            html += "<a class=\"dropdown-item\" onclick='restartService(\"" + type + "\")' href=\"javascript:void(0)\"><i class='fa fa-spinner' style='color: gold'></i> 重新启动</a>";
        }
        html += "<a class=\"dropdown-item\" onclick='stopService(\"" + type + "\")' href=\"javascript:void(0)\"><i class='fa fa-power-off' style='color: red'></i> 停止</a>";
    }

    if (html) {
        menu.html(html);
        menu.css("top", defPosition(evt).y + "px");
        menu.css("left", defPosition(evt).x + "px");
        menu.css("display", "block");
    }
    return false;
}

function addHandler(object, event, handler, useCapture) {
    if (object.addEventListener) {
        object.addEventListener(event, handler, useCapture ? useCapture : false);
    } else if (object.attachEvent) {
        object.attachEvent('on' + event, handler);
    } else alert("Add handler is not supported");
}

addHandler(document, "contextmenu", function () {
    document.getElementById("contextMenuId").style.display = "none";
});
addHandler(document, "click", function () {
    document.getElementById("contextMenuId").style.display = "none";
});

$(function () {

    $("body").tooltip({
        selector: '[data-tip="tooltip"]',
        container: 'body',
        placement: 'auto',
        html: true
    });

    $('[data-toggle="dropdown"]').dropdown();

    var getContrl = window.location.href.match(/controller=([^&]+)/);
    var getPage = window.location.href.match(/page=([^&]+)/);
    if (getPage !== null) {
        $("[data-active='" + getPage[1] + "']").addClass('active');
    } else {
        if (getContrl !== null)
            $("[data-active='" + getContrl[1] + "']").addClass('active');
        else
            $("[data-active='']").addClass('active');
    }

    if (getContrl[1] === "server" && getPage === null) {
        //alert("test");


        setInterval(function () {
            $.ajax({
                type: "POST",
                url: adr + "/index.php?function=get_process",
                success: function (data) {
                    data = JSON.parse(data);
                    $.each(data, function (a, b) {
                        $("." + a + "_count").html(b['count']);
                        $("." + a).html(b['status']);
                        if (b.process !== undefined) {
                            $("." + a + "_mem").html(b.process[0][3]);
                            $("." + a + "_cpu").html(b.process[0][2]);
                        }
                    });
                    //alert(data.gs.process[1][11]);
                    $(".listloc").removeAttr("disabled");
                    $.each(data.gs.process, function (d, c) {
                        // alert($("#gs-"+c[11]).val());
                        $("#gs-" + c[11]).attr("disabled", "disabled");
                        //console.log(c[11]);
                    });

                }
            });

            $.ajax({
                type: "POST",
                url: adr + "/index.php?function=get_status",
                success: function (data) {
                    data = JSON.parse(data);
                    var use = data.MemTotal - data.MemFree;
                    var swap_use = data.SwapTotal - data.SwapFree;
                    var use_proc = Math.round(use / data.MemTotal * 100);
                    var swap_proc = Math.round(swap_use / data.SwapTotal * 100);
                    $(".all").html(data.MemTotal);
                    $(".free").html(data.MemFree);
                    $(".use").html(use);
                    $(".use_proc").html(use_proc);
                    $(".swap_all").html(data.SwapTotal);
                    $(".swap_free").html(data.SwapFree);
                    $(".swap_use").html(swap_use);
                    $(".swap_use_proc").html(swap_proc);
                    $("#use_swap").attr("aria-valuenow", swap_proc);
                    $("#use_swap").attr("style", "width:" + swap_proc + "%;");
                    $("#use_mem").attr("aria-valuenow", use_proc);
                    $("#use_mem").attr("style", "width:" + use_proc + "%;");
                }
            });

            $.ajax({
                type: "POST",
                url: adr + "/index.php?function=get_locations",
                success: function (data) {
                    $(".listStarted").html(data);
                    //id="checkAll"
                    //alert($("#checkAll").prop('checked'));
                    if ($("#checkAll").prop('checked')) {
                        $(".location").attr("checked", "checked");
                    } else {
                        $(".location").removeAttr("checked");
                    }
                    //$("input:checkbox").prop('checked', $(this).prop("checked"));
                }
            });
        }, 3000);
    }

    if (getPage[1] === "chat") {
        startChat();
        setInterval(function () {
            startChat();
            if ($("input[name=autoscroll]").prop('checked')) {
                var div = $(".boxChat");
                div.scrollTop(div.prop('scrollHeight'));
            }
        }, 3000);
    } else {
        if (widgetChat === "on") {
            startChat();
            setInterval(function () {
                startChat();
                if ($("input[name=autoscroll]").prop('checked')) {
                    var div = $("[data-target=chatMessage]");
                    div.scrollTop(div.prop('scrollHeight'));
                }
            }, 3000);
        }
    }

});

var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    mode: "xml",
    lineNumbers: true,
    styleActiveLine: true,
    theme: "material",
    //lineWrapping: true,
    extraKeys: {
        "Ctrl-Q": function (cm) {
            cm.foldCode(cm.getCursor());
        }
    },
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
});
editor.setSize(null, 500);

