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
$page = empty($result_json->page) ? '' : $result_json->page;
$connect = mysql_connect("47.106.208.112", "root", "123456");
if (!$connect) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("dacheng_db", $connect);
mysql_query('set names utf8');
$res = mysql_query("SELECT * FROM passage_info");
$item_count = 0;
while ($items = mysql_fetch_array($res)) {
   $item_count++;
}
$data['item_sum'] = $item_count;
$result = mysql_query("SELECT * FROM passage_info ORDER BY passage_id DESC limit " . ($page-1)*10 . ", 10");
if ($result != null) {
    $i = 0;
    while ($passage_list = mysql_fetch_array($result)) {
        $passages[$i]['passage_id'] = $passage_list['passage_id'];
        $passages[$i]['title'] = $passage_list['title'];
        $passages[$i]['author'] = $passage_list['author'];
        $passages[$i]['create_date'] = $passage_list['create_date'];
        $i++;
    }
    $data['passages'] = $passages;
} else {
    $data['passages'] = null;
}
var_json(200, 'ok', '成功', $data);
?>