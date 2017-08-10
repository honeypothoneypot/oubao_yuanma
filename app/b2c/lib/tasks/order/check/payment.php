<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class b2c_tasks_order_check_payment extends base_task_abstract implements base_interface_task{

    public function exec($params=null){

        $obj_payments = app::get('ectools')->model('payments');
        $obj_order_bills = app::get('ectools')->model('order_bills');
        $obj_orders = app::get('b2c')->model('orders');
        $obj_abnormal_orders = app::get('b2c')->model('order_abnormal');

        $ret['checked']= 0;
        $ret['status']= 'succ';
        $ret['t_payed|bthan']= time-3*3600;

        $result = $obj_payments->getList('payment_id',$ret);
        if($result){
            $payments_id=array();
            foreach($result as $val){
                $payments_id[] = $val['payment_id'];
            }
            $order_bills = $obj_order_bills->getList('*',array('bill_id'=>$payments_id,'pay_object'=>'order'));
            if($order_bills){
                foreach($order_bills as $val){
                    if($val['bill_type'] == 'payments' && $val['pay_object'] == 'order'){
                        $order = $obj_orders->getRow('pay_status',array('order_id'=>$val['rel_id']));
                        //暂定订单支付状态作为基础判断
                        if($order['pay_status']==0 || $order['pay_status']==1){
                            $abnormal_order['order_id']=$val['rel_id'];
                            $abnormal_order['abnormal_type']='已支付订单状态未更改';
                            $abnormal_order['updatetime']=time();
                            $obj_abnormal_orders->save($abnormal_order);
                        }
                    }else{
                        //$obj_payments->update(array('checked'=>'1'),array('payment_id'=>$val['bill_id']));
                    }
                    $obj_payments->update(array('checked'=>'1'),array('payment_id'=>$val['bill_id']));
                }
            }
        }
    }
}