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
$filename = empty($result_json->filename) ? '' : $result_json->filename;
$filetype = empty($result_json->filetype) ? '' : $result_json->filetype;
$fileurl = empty($result_json->fileurl) ? '' : $result_json->fileurl;
if (isset($_COOKIE["uid"])) {
    $encode_uid = $_COOKIE["uid"];
    if (isset($_SESSION[$encode_uid]) && $_SESSION[$encode_uid]) {
        $salt1 = 'f3d2Aqwe%a3$';
        $salt2 = 'bc@eD$&a1q2w';
        $decode = base64_decode($encode_uid);
        $uid = str_replace($salt1, "", $decode);
        $uid = str_replace($salt2, "", $uid);
        $connect = mysql_connect("47.106.208.112", "root", "123456");
        if (!$connect) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db("dacheng_db", $connect);
        mysql_query('set names utf8');
        $str1 = "(filename, filetype, fileurl, upload_user, upload_date)";
        $str2 = "'".$filename."', '".$filetype."', '".$fileurl."', '".$uid."', '".date("Y/m/d h:i:s")."'";
        mysql_query("INSERT INTO file_info ".$str1." VALUES (".$str2.")");
        var_json(200, 'ok', '成功', array());
    } else {
        var_json(302, 'no_authority', '没有权限，需要重新登录', array());
    }
} else {
    var_json(302, 'no_authority', '没有权限，需要重新登录', array());
}
?>