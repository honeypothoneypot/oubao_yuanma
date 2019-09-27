<?php
#include("app/serveradm/xhprof.php");
define('ROOT_DIR',realpath(dirname(__FILE__)));
require(ROOT_DIR.'/app/base/kernel.php');
require(ROOT_DIR.'/app/base/pinyin.php');
kernel::boot();

if(defined("STRESS_TESTING")){
    b2c_forStressTest::logSqlAmount();
}
function dump2file() {
    $args = func_get_args();
    $count = 1;
    foreach ($args as $key => $value) {
        if (strpos($value, '.txt')) {
            $filename = $value;
            continue;
        }
        $value = print_r($value,1);
        // $value = var_export($value,1);
        $canshu = '参数'.$count;
        $count ++;
        $import_data.= "↘↘↘↘↘↘↘↘".$canshu."↙↙↙↙↙↙↙↙\r\n".$value."\r\n\r\n";
        // dumptest($canshu);
    }
    if (!$filename) {
        $filename = 'debuglog.txt';
    }
    //时间标记-->
    $m_time = microtime();
    list($t1, $t2) = explode(' ', $m_time);
    $t1 = $t1*1000000;
    $t2 = date('Y-m-d H:i:s',$t2);
    $flag = $t2.':'.$t1;
    //<--
    $link =dirname(dirname(__DIR__)).'/debuglog/'.$filename;
    $import_data = print_r($import_data,1);
    $import_data = "================".$flag."================\r\n".$import_data."\r\n";
    file_put_contents($link, $import_data);
}
function dump2file2() {
    $args = func_get_args();
    $count = 1;
    foreach ($args as $key => $value) {
        if (strpos($value, '.txt')) {
            $filename = $value;
            continue;
        }
        $value = print_r($value,1);
        // $value = var_export($value,1);
        $canshu = '参数'.$count;
        $count ++;
        $import_data.= "↘↘↘↘↘↘↘↘".$canshu."↙↙↙↙↙↙↙↙\r\n".$value."\r\n\r\n";
        // dumptest($canshu);
    }
    if (!$filename) {
        $filename = 'debuglog.txt';
    }
    //时间标记-->
    $m_time = microtime();
    list($t1, $t2) = explode(' ', $m_time);
    $t1 = $t1*1000000;
    $t2 = date('Y-m-d H:i:s',$t2);
    $flag = $t2.':'.$t1;
    //<--
    $link =dirname(dirname(__DIR__)).'/debuglog/'.$filename;
    $import_data = print_r($import_data,1);
    $import_data = "================".$flag."================\r\n".$import_data."\r\n";
    file_put_contents($link, $import_data,FILE_APPEND);
}