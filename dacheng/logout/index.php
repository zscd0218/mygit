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
if (isset($_COOKIE['uid'])) {
    $encode_uid = $_COOKIE['uid'];
    if (isset($_SESSION[$encode_uid])) {
        $data['cookie'] = $_COOKIE['uid'];
        $data['session1'] = $_SESSION[$encode_uid];
        $data['session2'] = $_SESSION['hello'];
        unset($_SESSION[$encode_uid]);
        unset($_SESSION['hello']);
    } else {
        $data['message'] = "no sessions";
    }
    setcookie("uid", $encode_uid, time()-3600, '/', null);
} else {
    $data['message'] = "no cookies";
}
$data['cookie'] = $_COOKIE['uid'];
$data['result'] = isset($_SESSION[$encode_uid]) && $_SESSION[$encode_uid];
var_json(200, 'ok', '成功', $data);
?>