<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

        $prov = array(
            array(),
            array(11,12,13,14,15,21,22,23,31,32,33,34,35,36,37,41,42,43,44,45,46,50,51,52,53,54,61,62,63,64,65),
            array('北京市','天津市','河北省','山西省','内蒙古自治区','辽宁省','吉林省','黑龙江省',
                '上海市','江苏省','浙江省','安徽省','福建省','江西省','山东省','河南省',
                '湖北省','湖南省','广东省','广西壮族自治区','海南省','重庆市','四川省','贵州省',
                '云南省','西藏自治区','陕西省','甘肃省','青海省','宁夏回族自治区','新疆维吾尔自治区'
            )
        );


        $matches = $prov;

        header("Content-type: text/html; charset=utf8");

        $url = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2017/';



        for ($i = 0,$e = count($matches[1]); $i < $e; $i++) {
            $index = file_get_contents($url . $matches[1][$i] . '.html');
//            echo $matches[2][$i];
            M('Area')->add(array('area_id'=>$matches[1][$i],'name'=>$matches[2][$i],'parent_id'=>0));
            preg_match_all('/<a href=\'\d{2}\/(.{1,30}).html\'>(.{1,30})<\/a><\/td><\/tr>/', $index, $matche);
            for ($a = 0, $b = count($matche[1]); $a < $b; $a++) {
                $index = file_get_contents($url . $matches[1][$i] . '/' . $matche[1][$a] . '.html');
                preg_match_all('/<a href=\'\d{2}\/(.{1,30}).html\'>(.{1,30})<\/a><\/td><\/tr>/', $index, $match);
//                echo iconv("gbk", "utf-8//ignore", $matche[2][$a]);
                M('Area')->add(array('area_id'=>$matche[1][$a],'name'=>iconv("gbk", "utf-8//ignore", $matche[2][$a]),'parent_id'=>$matches[1][$i]));
                for ($c = 0, $d = count($match[1]); $c < $d; $c++) {
                    $aru = substr($matche[1][$a], 2, 2);
                    $index = file_get_contents($url . $matches[1][$i] . '/' . $aru . '/' . $match[1][$c] . '.html');
                    preg_match_all('/<a href=\'\d{2}\/(.{1,30}).html\'>(.{1,30})<\/a><\/td><\/tr>/', $index, $matc);

                    //部分省市的html和大部分的不一样，重写规则
                    if (!$matc[0]) preg_match_all('/<td>(.{1,30})<\/td><td>\d{1,10}<\/td><td>(.{1,30})<\/td><\/tr>/', $index, $matc);

//                    echo iconv("gbk", "utf-8//ignore", $match[2][$c]);
                    M('Area')->add(array('area_id'=>$match[1][$a],'name'=>iconv("gbk", "utf-8//ignore", $match[2][$c]),'parent_id'=>$matche[1][$a]));

//                    $sql = 'REPLACE INTO position (province_id,province_name,city_id,city_name,county_id,county_name,town_id,town_name) VALUES ';
//                    for ($v = 0, $n = count($matc[1]); $v < $n; $v++) {
//                        $jil = iconv("utf-8", "gbk//ignore", $matches[2][$i]);
//                        $sql .= "({$matches[1][$i]},'{$jil}',{$matche[1][$a]},'{$matche[2][$a]}',{$match[1][$c]},'{$match[2][$c]}',{$matc[1][$v]},'{$matc[2][$v]}'),";
//                    }
//                    $sql = iconv("gbk", "utf-8//ignore", $sql);
//                    $res = $db->query(rtrim($sql, ","));
//                    echo $sql . '</br> ';

                }
            }
        }
        echo 'success';

    }
}