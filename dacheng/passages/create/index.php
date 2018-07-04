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
$title = empty($result_json->title) ? '' : $result_json->title;
$author = empty($result_json->author) ? '' : $result_json->author;
$content = empty($result_json->content) ? '' : $result_json->content;
if (isset($_COOKIE["uid"])) {
    $encode_uid = $_COOKIE["uid"];
    if (isset($_SESSION[$encode_uid]) && $_SESSION[$encode_uid]) {
        $connect = mysql_connect("47.106.208.112", "root", "123456");
        if (!$connect) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db("dacheng_db", $connect);
        mysql_query('set names utf8');
        $str1 = "(title, author, create_date, content)";
        $str2 = "'".$title."', '".$author."', '".date("Y/m/d h:i:s")."', '".$content."'";
        mysql_query("INSERT INTO passage_info ".$str1." VALUES (".$str2.")");
        $data['str'] = "INSERT INTO passage_info ".$str1." VALUES (".$str2.")";
        $data['session1'] = $_SESSION[$encode_uid];
        $data['session2'] = $_SESSION['hello'];
        var_json(200, 'ok', '成功', $data);
    } else {
        var_json(302, 'no_authority', '没有权限，需要重新登录', array());
    }
} else {
    var_json(302, 'no_authority', '没有权限，需要重新登录', array());
}
?>