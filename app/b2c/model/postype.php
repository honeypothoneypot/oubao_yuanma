<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * brand 模板
 */
class b2c_mdl_postype extends dbeav_model {
    public $defaultOrder = array('posbrand_id', ' ASC');
    public function getPostype() {
        //刷卡方式
        $postype = array(
            'da_e'       => '大额',
            'hui_ka'     => '挥卡',
            'weixin'     => '微信扫码',
            'zhifubao'   => '支付宝扫码',
            'mobile_pay' => '手机pay',
            'jd'         => '京东扫码',
            'bank_app'   => '银行app扫码',
            'yun_pay'    => '银联云闪付',
            'hongshan'   => '红闪封顶',
            'long_pay'   => '龙支付',
            'qianbao'    => '钱宝app',
            'heika1'     => '常州·餐饮',
            'heika2'     => '常州·百货',
            'heika3'     => '无锡·加油站',
            'heika4'     => '无锡·酒吧',
            'heika5'     => '苏州·旅行社',

            'heika6'     => '南京·酒店',
            'heika7'     => '南京·旅行社',
            'heika8'     => '常州·酒吧',
        );
        return $postype;
    }

    public function getList($cols = '*', $filter = array(), $offset = 0, $limit = -1, $orderType = null) {
        $datas   = parent::getList($cols, $filter, $offset, $limit, $orderType);
        $postype = $this->getPostype();
        foreach ($datas as $key => &$value) {
            $value['shuaka_typeOld'] = $value['shuaka_type'];
            $value['shuaka_type']    = $postype[$value['shuaka_type']];
        }
        return $datas;
    }
}
