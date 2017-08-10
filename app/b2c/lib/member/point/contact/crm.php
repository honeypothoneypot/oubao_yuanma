<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
class b2c_member_point_contact_crm
{
    /**
     * 公开构造方法
     * @params app object
     * @return null
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->rpc_obj = kernel::single("b2c_apiv_exchanges_request_member_point");
    }

    /**
     * 查询积分
     * @param string member id
     * @param real_point 会员积分
     * @param type 获取积分方法的类型
     */
    public function getPoint($member_id=0,$type=1)
    {
        $real_point = 0;

        $members = app::get('b2c')->model('members');

        if( $member_id ){
            $member_data = $members->getRow('point,member_lv_id',array('member_id'=>$member_id));
            $current_point = $member_data['point'];
            $current_member_lv = $member_data['member_lv_id'];

            if( isset($_SESSION['point']['addtime']) && ($_SESSION['point']['addtime'] + 60*5) > time() && $type != 2){
                $real_point = $current_point;
            }else{
                if( $this->rpc_obj ){
                    $point_data = $this->rpc_obj->getActive($member_id);
                    $real_point = $point_data['total'];
                    $_SESSION['point']['addtime'] = time();

                    if($real_point != $current_point){
                        $members->update(array('point'=>$real_point),array('member_id'=>$member_id));

                        $obj_member_point = app::get('b2c')->model('member_point');
                        $member_lv_id = $obj_member_point->member_lv_chk($member_id,$current_member_lv,$real_point);

                        if( $member_lv_id != $current_member_lv){
                            $members->update(array('member_lv_id'=>$member_lv_id),array('member_id'=>$member_id));
                        }
                    }
                }
            }
        }

        return $real_point;
    }

    /**
     * 查询积分日志
     * @param arr data 接口请求参数 array('member_id'=>'','page'=>1,'page_size'=10)
     * @param arr pointlog 积分日志
     */
    public function getPointLog($data)
    {
        $pointlog = array();

        if( $this->rpc_obj ){
            $pointlog = $this->rpc_obj->getlogActive($data);
        }

        return $pointlog;
    }

    /**
     * 同步积分日志
     * @param point_id 积分日志的id
     *
     */

    public function pointChange($point_id){
        if($point_id){
            if($this->rpc_obj){
                $this->rpc_obj->changeActive($point_id);
            }
        }
    }
}
