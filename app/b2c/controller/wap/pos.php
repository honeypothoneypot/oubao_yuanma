<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class b2c_ctl_wap_pos extends wap_frontpage{
    function __construct(&$app){
        $this->set_tmpl('pos');
        parent::__construct($app);
        $this->verify_member();
    }

    /*
     * 如果是登录状态则直接跳转到会员中心
     * */
    public function check_login($mini){
        if( $this->userObject->is_login() )
        {
            $url = $this->gen_url(array('app'=>'b2c','ctl'=>'wap_member','act'=>'index'));
            if($_GET['mini_passport']==1 || $mini){
                kernel::single('wap_router')->http_status(302);return;
            }else{
                //您已经是登录状态，不需要重新登录
                $this->redirect($url);
            }
        }
        return false;
    }

    public function index(){
        //base_component_request
        //$request_params = $this->_request->get_params();
        //获取信用卡列表
        $mdlCard = $this->app->model('poscard');
        $cardLists = $mdlCard->getList('card_id,name,belong_to,card_no,memo',array('is_enabled'=>'1'));
        $cardLists = utils::array_group_by($cardLists,'belong_to');
        $this->pagedata['cardLists'] = $cardLists;

        //获取pos品牌
        $mdlBrand = $this->app->model('posbrand');
        $posBrandAndTypeLists = $mdlBrand->getBrandAndType();
        $posBrandAndTypeLists = utils::array_group_by($posBrandAndTypeLists,'posbrand_id');
        $this->pagedata['posBrandLists'] = $posBrandAndTypeLists;
        $this->page('wap/pos/index.html');
    }
    public function save(){
        $data['card_id'] = $_POST['card_id'];
        $data['postype_id'] = $_POST['postype_id'];
        $data['money'] = $_POST['money'];
        $data['feilv'] = $_POST['feilv'];
        $data['fengding'] = $_POST['fengding'];
        //计算结算金额
        if ($data['fengding']>0) {
            $data['jiesuan_money'] = $data['money']-$data['fengding'];
        }else{
            $jiesuan = $data['money']*(1-$data['feilv']/100);
            $data['jiesuan_money'] = substr(sprintf("%.3f",$jiesuan),0,-1);
        }
        $data['memo'] = $_POST['memo'];
        $data['mcc'] = $_POST['mcc'];
        $data['create_time'] = time();
        // $url = $this->gen_url(array('app'=>'b2c','ctl'=>'wap_pos','act'=>'index','arg0'=>$_POST['name'],'arg1'=>$_POST['pos_type'],'arg2'=>$_POST['bank'],'arg3'=>$_POST['mcc']));
        $poslog = $this->app->model('poslog');
        try {
            $poslog->save($data);
        } catch (Exception $e) {
            echo  json_encode(array('success'=>false,'msg'=>'提交失败'));exit;
        }
        echo  json_encode(array('success'=>true,'msg'=>'提交成功'));exit;
    }

    //ajax获取日志列表
    public function ajax_get_poslog(){
        $page = $_POST['page'] ? $_POST['page'] : 1;
        $pageLimit = 50;//每页条数
        $poslog = $this->app->model('poslog');
        //$belong_to,$card_id,$postype_id,$from_time,$to_time
        $filter['belong_to'] = $_POST['belong_to'];
        $filter['card_id'] = $_POST['card_id'];
        $filter['postype_id'] = $_POST['postype_id'];
        $filter['from_time'] = strtotime($_POST['from_time']);
        $filter['to_time'] = $_POST['to_time']?strtotime($_POST['to_time'])+86400:'';
        $filter['posbrand_id'] = $_POST['posbrand_id'];
        $filter['mcc'] = $_POST['mcc'];

        $ret = $poslog->getLog($filter,$pageLimit*($page-1),$pageLimit);
        //刷卡方式
        $mdlType = $this->app->model('postype');
        $postype = $mdlType->getPostype();
        foreach ($ret['lists'] as $key => &$value) {
            $value['shuaka_type'] = $postype[$value['shuaka_type']];
            if ($value['belong_to']=='lsc') {
                $value['belong_to'] = '蔺苏川';
            }else{
                $value['belong_to'] = '刘艳';
            }
        }
        $counts = $poslog->getCount($filter);//统计的总数-条数、金额、结算金额、利息、次数等
        $total = $counts['countsSum'];//总数
        $pagetotal= $total ? ceil($total/$pageLimit) : 1;//总页数
        $this->pagedata['page'] = $page;
        //分页
        $this->pagedata['pager'] = array(
            'current'=>$page,
            'total'=>$pagetotal,
            'link' =>$this->gen_url(array('app'=>'b2c', 'ctl'=>'wap_pos','act'=>'ajax_get_poslog')),
        );
        $this->pagedata['poslogs'] = $ret;
        $this->pagedata['total'] = $total;
        $this->pagedata['counts'] = $counts;
        $view = 'wap/pos/poslog.html';
        echo $this->fetch($view);
    }
    //获取pos品牌
    public function ajaxGetPosBrand(){
        $mdlBrand = $this->app->model('posbrand');
        $lists = $mdlBrand->getList('posbrand_id,name',array('display'=>'true'));
        $this->pagedata['lists'] = $lists;
        $view = 'wap/pos/posbrand.html';
        echo $this->fetch($view);
    }

    //获取pos刷卡方式
    public function ajaxGetPosType(){
        $posbrand_id = $_POST['posbrand_id'];
        $mdlType = $this->app->model('postype');
        $lists = $mdlType->getList('postype_id,sub_name,shuaka_type,feilv,fengding',array('posbrand_id'=>$posbrand_id,'is_enable'=>1));
        $this->pagedata['lists'] = $lists;
        $view = 'wap/pos/postype.html';
        echo $this->fetch($view);
    }

    //获取信用卡列表
    public function ajaxGetPosCard(){
        $belong_to = $_POST['belong_to'];
        $mdlCard = $this->app->model('poscard');
        $lists = $mdlCard->getList('card_id,name,belong_to,card_no,memo',array('belong_to'=>$belong_to));
        $this->pagedata['lists'] = $lists;
        $view = 'wap/pos/poscard.html';
        echo $this->fetch($view);
    }
}
