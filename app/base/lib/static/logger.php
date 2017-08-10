<?php
/**
 * 日志记录类
 *
 * @link http://www.shopex.cn/
 * @copyright  Copyright (c) 2005-2013 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author bryant.yan@gmail.com
 * @package base
 */

/**
 * 类 logger 实现了一个简单的日志记录服务
*/

class logger{
    /**
     * @var array $_log_types 可用日志级别列表
     * @access static private
    */
    static private $__log_levels = array(
        LOG_SYS_EMERG => 'EMERG',
        LOG_SYS_ALERT => 'ALERT',
        LOG_SYS_CRIT  => 'CRIT',
        LOG_SYS_ERR   => 'ERR',
        LOG_SYS_WARNING => 'WARNING',
        LOG_SYS_NOTICE => 'NOTICE',
        LOG_SYS_INFO  => 'INFO',
        LOG_SYS_DEBUG => 'DEBUG'
    );

    /**
     * @var string $__log_level 当前日志级别
     * @access static private
    */
    static private $__log_level = null;

    /**
     * @var string $__default_log_level 默认日志级别
     * @access static private
    */
    static private $__default_log_level = LOG_SYS_INFO;

    /**
     * @var string $__logfile 日志文件
     * @access static private
    */
    static private $__logfile = '';

    /**
     * @var string $__accessId 当前请求日志ID
     * @access static private
    */
    static private $__accessId = '';

    /**
     * 初始化日志类
     *
     * @access static public
     * @return void
    */
    static public function __init(){
        if (self::$__log_level === null) {
            if (defined('LOG_LEVEL') && array_key_exists(LOG_LEVEL, self::$__log_levels)) {
                self::$__log_level = LOG_LEVEL;
            }else{
                self::$__log_level = self::$__default_log_level;
            }

            self::$__accessId = $_SERVER["REQUEST_TIME"] . uniqid();
            self::info('trigger log url:' . $_SERVER['REQUEST_URI']);
        }
    }

    /**
     * 读取当前日志级别
     *
     * @access static public
     * @return string
    */
    static public function get_log_level(){
        self::__init();
        self::$__log_level;
    }

    /**
     * 记录 LOG_SYS_EMERG 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function emerg($message) {
        logger::log($message, LOG_SYS_EMERG);
    }

    /**
     * 记录 LOG_CRIT 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function alert($message) {
        logger::log($message, LOG_SYS_ALERT);
    }

    /*
     * 记录 LOG_CRIT 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
     static public function crit($message) {
        logger::log($message, LOG_SYS_CRIT);
    }

    /*
     * 记录 LOG_ERR 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function error($message) {
        logger::log($message, LOG_SYS_ERR);
    }

    /*
     * 记录 LOG_WARNING 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function warning($message) {
        logger::log($message, LOG_SYS_WARNING);
    }

    /*
     * 记录 LOG_NOTICE 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function notice($message) {
        logger::log($message, LOG_SYS_NOTICE);
    }

    /*
     * 记录 LOG_INFO 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function info($message) {
        logger::log($message, LOG_SYS_INFO);
    }

    /*
     * 记录 LOG_DEBUG 类型的日志
     *
     * @param string $message 消息内容
     * @return void
    */
    static public function debug($message) {
        logger::log($message, LOG_SYS_DEBUG);
    }

    /*
     * 通用记录日志函数
     * @var string $message
     * @var int $log_level
     */
    static public function log($message, $log_level=LOG_SYS_INFO){
        self::__init();
        if(kernel::$console_output){
            if ($log_level < LOG_SYS_DEBUG) {
                echo $message = $message."\n";
                //echo "log_level < LOG_SYS_DEBUG";
            }
        }

        if ($log_level <= self::$__log_level) {
            $message = sprintf("%s\t%s\t%s\n", date("Y-m-d H:i:s") . "\t" . self::$__accessId, self::$__log_levels[$log_level], $message);
            switch(LOG_TYPE) {
            case 3:
                if('' == self::$__logfile){
                    self::$__logfile = str_replace('{date}', date("Ymd"), LOG_FILE);
                    $ip = ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
                    $ip = str_replace(array('.', ':'), array('_', '_'), $ip);
                    self::$__logfile = str_replace('{ip}', $ip, self::$__logfile);

                    if(!file_exists(self::$__logfile)){
                        if(!is_dir(dirname(self::$__logfile)))  utils::mkdir_p(dirname(self::$__logfile));
                        file_put_contents(self::$__logfile, (defined(LOG_HEAD_TEXT))?LOG_HEAD_TEXT:'<'.'?php exit()?'.">\n");
                    }
                }

                @error_log($message, 3, self::$__logfile);
                break;
            case 2:
                @error_log($message, 0);
            case 0:
            default:
                @syslog($log_level,  $message);
            }//End Switch
        }
    }

    static function appned(){

    }
}
