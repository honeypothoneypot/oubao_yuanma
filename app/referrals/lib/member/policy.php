<?php
class referrals_member_policy
{
    public function __construct($app)
    {
        $this->app = $app;
        //$this->bind_crm_status= $this->is_bind_crm();
        //$this->register_rule = $this->app->model('register_rule');
        $this->register_record = $this->app->model('register_record');
        $this->b2c_members_model = app::get('b2c')->model('members');
    }



    public function is_bind_crm()
    {
        $nodes_obj = app::get('b2c')->model('shop');
        $nodes = $nodes_obj->count( array('node_type'=>'ecos.taocrm','status'=>'bind'));
        if($nodes > 0){
        	return true;
        }
        else{
        	return false;
        }
    }



    public function referrals_member($referrals_code,$member_id)
    {
        $referrals_setting = app::get('referrals')->getConf('register_rule');
        if( is_array($referrals_setting) && $referrals_setting['status'] == 1 ){
            $this->referrals_Process($referrals_code,$member_id,$referrals_setting['register_point']);
        }
        else{
            return false;
        }

    }

    public function referrals_Process($referrals_code,$member_id,$register_point)
    {
        $result = $this->b2c_members_model->getRow('member_id',array('referrals_code'=>$referrals_code));
        if(!empty($result['member_id'])){
            $referrals_member_id=$result['member_id'];
            $this->point_change($referrals_member_id,$register_point);
            $save_data=array(
                'reference_id' => $referrals_member_id,
                'register_id' => $member_id,
                'regtime'     => time(),
                'register_point' => $register_point
                );
            $this->register_record->save($save_data);
        }
        else{
            return false;
        }

    }

    public function point_change($referrals_member_id,$register_point)
    {
        $mem_point =kernel::single('b2c_mdl_member_point');
        $msg = '推荐送积分';
        $mem_point->change_point($referrals_member_id,$register_point,$msg,'referrals',2,0,$referrals_member_id,'charge');
    }

    public function create_code($member_id)
    {
        $this->members_info($member_id);
        return $this->return_code_rule();
    }

    public function members_info($member_id)
    {
        $columns=array('member_id','crm_member_id','referrals_code');
        $columns = implode(',',$columns);
        $this->member_info = $this->b2c_members_model->getRow($columns,array('member_id'=>$member_id));

    }

    public function return_code_rule()
    {
        if(!empty($this->member_info['referrals_code'])){
            return $this->member_info['referrals_code'];
        }
    	$code = $this->code_rule();
        $this->b2c_members_model->update(array('referrals_code'=>$code),array('member_id'=>$this->member_info['member_id']));
        return $code;
    }

    public function code_rule()
    {
    	$code='ec_'.substr(md5(time().$this->member_info['member_id']),0,14);
    	return $code;
    }

    public function referrals_members_info($member_id)
    {
        $reference_id = $this->register_record->getRow('reference_id',array('register_id'=>$member_id));
        if($reference_id){
            $this->members_info($reference_id);
            return  $this->member_info;
        }else{
            return false;
        }
    }


}
?>
