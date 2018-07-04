<?php
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
$country_id = empty($result_json->country_id) ? '' : $result_json->country_id;
$city_id = empty($result_json->city_id) ? '' : $result_json->city_id;
$page = empty($result_json->page) ? '' : $result_json->page;
$feature1 = empty($result_json->feature1) ? '' : " AND experience_feature1='".$result_json->feature1."'";
$feature2 = empty($result_json->feature2) ? '' : " AND experience_feature2='".$result_json->feature2."'";
$feature3 = empty($result_json->feature3) ? '' : " AND experience_feature3='".$result_json->feature3."'";
$connect = mysql_connect("39.108.15.127", "root", "Jupiter");
if (!$connect) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("Jupiter_db", $connect);
mysql_query('set names utf8');
$data = null;
$items = null;
if ($country_id != "") {
    $res = mysql_query("SELECT * FROM city_info_temp WHERE country_id=".$country_id);
    if ($res != null) {
        $item_sum = 0;
        $min = ($page-1) * 6;
        $max = $page * 6;
        $i = 0;
        while ($row = mysql_fetch_array($res)) {
            $city_id = $row['city_id'];
            $result = mysql_query("SELECT * FROM experience_info_temp WHERE city_id=".$city_id.$feature1.$feature2.$feature3);
            if ($result != null) {
                while ($experience = mysql_fetch_array($result)) {
                    if ($item_sum >= $min && $item_sum < $max) {
                        $items[$i]['experience_id'] = $experience['experience_id'];
                        $items[$i]['experience_feature3'] = $experience['experience_feature3'];
                        $items[$i]['cover_img'] = $experience['cover_img'];
                        $items[$i]['experience_title'] = $experience['experience_title'];
                        $items[$i]['experience_brief_description'] = $experience['experience_brief_description'];
                        $i++;
                    }
                    $item_sum++;
                }
            }
        }
        $data['item_sum'] = $item_sum;
        $data['items'] = $items;
    }
} else {
    $res = mysql_query("SELECT * FROM experience_info_temp WHERE city_id=".$city_id.$feature1.$feature2.$feature3);
    if ($res != null) {
        $item_sum = 0;
        $min = ($page-1) * 6;
        $max = $page * 6;
        $i = 0;
        while ($row = mysql_fetch_array($res)) {
            if ($item_sum >= $min && $item_sum < $max) {
                $items[$i]['experience_id'] = $row['experience_id'];
                $items[$i]['experience_feature3'] = $row['experience_feature3'];
                $items[$i]['cover_img'] = $row['cover_img'];
                $items[$i]['experience_title'] = $row['experience_title'];
                $items[$i]['experience_brief_description'] = $row['experience_brief_description'];
                $i++;
            }
            $item_sum++;
        }
        $data['item_sum'] = $item_sum;
        $data['items'] = $items;
    }
}
var_json(200, 'ok', '成功', $data);
?>