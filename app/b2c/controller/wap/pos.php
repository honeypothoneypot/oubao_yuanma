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
        $cardLists = $mdlCard->getList('card_id,name,belong_to,card_no,memo,share_flag',array('is_enabled'=>'1'));
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
        $data = $_POST;
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
        $poslog = $this->app->model('poslog');
        try {
            $ret1 = $poslog->save($data);
        } catch (Exception $e) {
            echo  json_encode(array('success'=>false,'msg'=>$e->getMessage()));exit;
        }
        echo  json_encode(array('success'=>true,'msg'=>'提交成功'));exit;
    }

    //ajax获取日志列表
    public function ajax_get_poslog(){
        $page = $_POST['page'] ? $_POST['page'] : 1;
        $pageLimit = 15;//每页条数
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

    public function ajaxGetRemind(){
        //默认按可用额度排序
        // $sql = "SELECT group_concat(t.newid ) as newid FROM (
        //         SELECT group_concat(card_id ORDER BY usable_edu DESC) as newid,1 as flag  FROM sdb_b2c_poscard WHERE is_enabled='1' GROUP BY share_flag order by usable_edu desc ) as t GROUP BY t.flag";
        $sql = "SELECT card_id,share_flag FROM sdb_b2c_poscard WHERE is_enabled = '1' order by usable_edu DESC";
        $temp = app::get('b2c')->model('poscard')->db->select($sql);
        foreach ($temp as $key => $value) {
            $temp1[$value['share_flag']][]=$value['card_id'];
        }
        $str='';
        foreach ($temp1 as $key => $value) {
            if (count($value)>1) {
                foreach ($value as $ke => $val) {
                    $str.="'{$val}',";
                }
            }else{
                $str1 = implode(',', $value);
                $str.="'{$str1}',";
            }
        }
        $newCartId = rtrim($str,',');
        $sql = "SELECT * FROM sdb_b2c_poscard WHERE card_id in ($newCartId) order by field (card_id,$newCartId)";
        $rowsets = app::get('b2c')->model('poscard')->db->select($sql);
        //查询日志：
        //获取今天零点的时间戳：
        $start = strtotime(date('Y-m-d',time()));
        $sql = "SELECT card_id,count(id) as count FROM sdb_b2c_poslog where type='pos' and create_time>'{$start}' group by card_id ";
        $logs = app::get('b2c')->model('poscard')->db->select($sql);
        foreach ($logs as $key => $value) {
            $count[$value['card_id']]=$value['count'];
        }
        foreach ($rowsets as $key => &$value) {
            $value['all_edu'] = round($value['all_edu'],2);
            $value['usable_edu'] = round($value['usable_edu'],2);
            $value['linshi_edu'] = round($value['linshi_edu'],2);
            $value['count'] = $count[$value['card_id']];
            $new[$value['belong_to']][] = $value;
        }
        krsort($new);
        $this->pagedata['rowsets'] = $new;
        echo $this->fetch('wap/pos/remindajax.html');exit;
    }
    //获取账单
    public function getZhangdan(){
        $time = time();
        $arg = $arg2= -1;
        if ($_POST['flag']=='1') {
            $arg = $arg2= 0;
        }
        $arg2++;
        //本月：年-月
        $thisMonth = date("Y-m",strtotime("{$arg2} months"));
        $thisMonth2 = date("m",strtotime("{$arg2} months"));
        //上月：年-月
        $lastMonth = date("Y-m",strtotime("{$arg} months"));
        //前月：年-月
        $arg--;
        $prevMonth = date("Y-m",strtotime("{$arg} months"));
        //下月：年-月
        $arg2++;
        $nextMonth = date("Y-m",strtotime("{$arg2} months"));
        //前月1号作为开始时间
        $b_time = strtotime("{$prevMonth}-1");
        $poslog = $this->app->model('poslog');
        $data = $poslog->getZhangdan($thisMonth,$lastMonth,$prevMonth,$nextMonth,$thisMonth2,$b_time);
        $this->pagedata['data'] = $data;
        echo $this->fetch('wap/pos/getZhangdan.html');exit;
    }
    function getQiankuan(){
        $sql = "SELECT t.* FROM
        ( SELECT share_flag, max(all_edu) AS all_edu FROM sdb_b2c_poscard GROUP BY share_flag ) a
        LEFT JOIN sdb_b2c_poscard t ON t.share_flag = a.share_flag
        AND t.all_edu = a.all_edu
        AND t.is_enabled = '1' GROUP BY t.share_flag";
        $rowsets = app::get('b2c')->model('poscard')->db->select($sql);
        foreach ($rowsets as $key => $value) {
            $tmp1 = $value['all_edu']+$value['linshi_edu']-$value['usable_edu'];
            $tmp2+=$tmp1;
        }
        echo $tmp2;
    }
}
