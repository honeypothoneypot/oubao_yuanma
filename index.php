<?php
#include("app/serveradm/xhprof.php");
define('ROOT_DIR',realpath(dirname(__FILE__)));
require(ROOT_DIR.'/app/base/kernel.php');
kernel::boot();

if(defined("STRESS_TESTING")){
    b2c_forStressTest::logSqlAmount();
}
function dump2file($content,$filename = 'debuglog') {
    define('DEBUG_PATH',dirname(ROOT_DIR));
    $filename = $filename?$filename:'debuglog';
    $import_data = print_r($content,1);
    $import_data = "================".date('Y-m-d H:i:s')."---".md5(time().mt_rand(1,1000000))."================\r\n".$import_data."\r\n";
    file_put_contents(DEBUG_PATH.'/debuglog/'.$filename.'.txt', $import_data,FILE_APPEND);
}