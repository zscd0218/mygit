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
$page = empty($result_json->page) ? '' : $result_json->page;
$filename = empty($result_json->filename) ? '' : $result_json->filename;
$filetype = empty($result_json->filetype) ? '' : "filetype='".$result_json->filetype."' AND ";
$upload_user = empty($result_json->upload_user) ? '' : $result_json->upload_user;
$date_start = empty($result_json->date_start) ? '' : "upload_date>='".$result_json->date_start."' AND ";
$date_end = empty($result_json->date_end) ? '' : "upload_date<='".$result_json->date_end."' AND ";
if (isset($_COOKIE["uid"])) {
    $encode_uid = $_COOKIE["uid"];
    if (isset($_SESSION[$encode_uid]) && $_SESSION[$encode_uid]) {
        $connect = mysql_connect("47.106.208.112", "root", "123456");
        if (!$connect) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db("dacheng_db", $connect);
        mysql_query('set names utf8');
        $result = mysql_query("SELECT * FROM user_info WHERE username='".$upload_user."'");
        $user = mysql_fetch_array($result);
        if ($user == false && $upload_user != "") {
            $data['file_sum'] = 0;
            $data['file'] = null;
        } else {
            if ($upload_user != "") {
                $uid = $user['user_id'];
                $user_id = "upload_user=".$uid." AND ";
            } else {
                $user_id = "";
            }
            $str = $filetype.$user_id.$date_start.$date_end."1=1 ORDER BY file_id DESC";
            $res = mysql_query("SELECT * FROM file_info WHERE ".$str);
            if ($res != null) {
                $file_sum = 0;
                $min = ($page-1) * 10;
                $max = $page * 10;
                $i = 0;
                while ($row = mysql_fetch_array($res)) {
                    if (strpos($row['filename'], $filename) !== false || $filename == "") {
                        if ($file_sum >= $min && $file_sum < $max) {
                            $file[$i]['filename'] = $row['filename'];
                            $file[$i]['filetype'] = $row['filetype'];
                            $file[$i]['fileurl'] = $row['fileurl'];
                            $file[$i]['upload_date'] = $row['upload_date'];
                            if ($upload_user != "") {
                                $file[$i]['upload_user'] = $upload_user;
                            } else {
                                $user_result = mysql_query("SELECT * FROM user_info WHERE user_id=".$row['upload_user']);
                                $upload = mysql_fetch_array($user_result);
                                $file[$i]['upload_user'] = $upload['username'];
                            }
                            $i++;
                        }
                        $file_sum++;
                    }
                }
                $data['file_sum'] = $file_sum;
                $data['file'] = $file;
            }
        }
        var_json(200, 'ok', '成功', $data);
    } else {
        var_json(302, 'no_authority', '没有权限，需要重新登录', array());
    }
} else {
    var_json(302, 'no_authority', '没有权限，需要重新登录', array());
}
?>