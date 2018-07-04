<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Credentials');
header('Access-Control-Allow-Credentials: true');
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
$passage_id = empty($result_json->passage_id) ? '' : $result_json->passage_id;
$connect = mysql_connect("47.106.208.112", "root", "123456");
if (!$connect) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("dacheng_db", $connect);
mysql_query('set names utf8');
$result = mysql_query("SELECT * FROM passage_info WHERE passage_id=" . $passage_id);
$row = mysql_fetch_array($result);
if ($row != null) {
    $data['title'] = $row['title'];
    $data['author'] = $row['author'];
    $data['create_date'] = $row['create_date'];
    $data['content'] = $row['content'];
} else {
    $data = array();
}
var_json(200, 'ok', '成功', $data);
?>