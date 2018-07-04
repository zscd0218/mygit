<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Credentials');
header('Access-Control-Allow-Credentials: true');
$lifeTime = 24 * 3600;
session_set_cookie_params($lifeTime);
session_start();
function var_json($code, $enmsg, $cnmsg, $data) {
    $out['code'] = $code;
    $out['enmsg'] = $enmsg;
    $out['cnmsg'] = $cnmsg;
    $out['data'] = $data;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($out, JSON_HEX_TAG);
    exit(0);
}
$post_body = file_get_contents('php://input');
$result_json = json_decode($post_body);
$username = empty($result_json->username) ? '' : $result_json->username;
$encode = empty($result_json->password) ? '' : $result_json->password;
$salt1 = 'f3d2Aqwe%a3$';
$salt2 = 'bc@eD$&a1q2w';
$decode = base64_decode($encode);
$password = str_replace($salt1, "", $decode);
$password = str_replace($salt2, "", $password);
$connect = mysql_connect("47.106.208.112", "root", "123456");
if (!$connect) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("dacheng_db", $connect);
$result = mysql_query("SELECT * FROM user_info WHERE username=" . "'" . $username . "'");
$row = mysql_fetch_array($result);
if (!$row) {
    var_json(500, 'unregistered_username', '该用户名未注册', array());
} else {
    if (password_verify($password, $row['crypted_password'])) {
        $encode_uid = base64_encode($salt1 . $row['user_id'] . $salt2);
        setcookie("uid", $encode_uid, time()+3600*24, '/', null);
        $_SESSION[$encode_uid] = true;
        $_SESSION['hello'] = 'hello';
        //$data['cookie'] = $_COOKIE["uid"];
        //$data['session'] = $_SESSION[$encode_uid];
        $data['cookie'] = $_COOKIE['uid'];
        $data['session1'] = $_SESSION[$encode_uid];
        $data['session2'] = $_SESSION['hello'];
        var_json(200, 'ok', '成功', $data);
    } else {
        var_json(500, 'wrong_password', '密码错误', array());
    }
}
?>