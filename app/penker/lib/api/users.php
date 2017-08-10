<?php
class penker_api_users{

    public $app;
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function login(){
        kernel::single('base_session')->start();
        $penker = $this->app->model('bind');
        $arr_bind = $penker->getList();
        $pack = $_GET['pack'];
        
        //$pack = 'fLd3cryW0F4v4fjSMHjd6YZ19lV0zaUex08cyl0z8AJKIvhur9o9fjWBmICvNx4bw8U6k%2ByKzyMj8ZTRcC4wpE%2B4eA0a82xNPdmd0tSzElH0R6f7ZQq1erI7G4x0zZ5ozptgoSv%2Bz%2Bnru98flYZ9MDmsBMdL%2BDLKBhAJJ790NK4rg6B6WBoVQ00oViDdU3RLi2pjerwXPP726DL6Du48lL5Yui69mgke%2Fbp3E0jKgUFYqHr%2FJZ4A%2FR%2BDMpdVOG17%2BzfCF98vlxQlvl8TWQwFi79Mxbsp92Hf%2FZ4OMDU1dkRjdE85tJM1OhE1QGt44cV%2FLAFfWkbkZvizAnj7MIvLZ11tKCmNrUKjwLEa8vMkYZ0V7yGVingys%2BlsCx6X4vw8ZvlOOU7v8LjPROrtAxAdawom943R5iS1bhsqNclr06qBoBjAu9sF1Xll2cETFLec0HBAztrx9fUgV2h8zl7R0ZWUbm65ZYSg2Thqh0I8b9siEXn9p1GD1iYCIItTyqvmTDwB9DZlW0J7AYvD8aDXjw%3D%3D';
        //$pack = urldecode($pack);

        $key = $arr_bind[0]['secret_key'];
        $iv = substr(md5($arr_bind[0]['node_id']),0,16);
        $params = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($pack), MCRYPT_MODE_CBC,$iv));
        
        $params = json_decode($params,true);
        $msg = 'fail!';
        if( is_array($params) ){
            if( isset($params['userinfo']) &&is_array($params['userinfo']) && !empty($params['userinfo']['openid']) ){
                $user_login = kernel::single('penker_service_user');

                $openid = $params['userinfo']['openid'];
                $_SESSION['weixin_u_nickname'] = $params['userinfo']['nickname'];
                $_SESSION['weixin_u_openid'] = $openid;
                $bindTagData = app::get('pam')->model('bind_tag')->getRow('tag_name,member_id',array('open_id'=>$openid));
                if( $bindTagData ){
                    $msg = $user_login->login($bindTagData['member_id']);
                }else{
                    $msg = $user_login->create($params['userinfo']);
                }
            }else{
                $msg = 'succ';
            }
        }else{
            $msg = 'params format error！';
            print_r($msg);
            exit();
        }

        if($msg == 'succ'){
            //$params['goods_id'] = '55';

            if( isset($params['product_id']) && !empty($params['product_id']) ){
                $product_id = $params['product_id'];
            }else{
                $msg = 'product_id is null';
            }

            if( isset($params['guide_identity']) && !empty($params['guide_identity']) ){
                $guide_identity = $params['guide_identity'];
            }else{
                $msg = 'guide_identity is null';
            }

            if( $msg != 'succ' ){
                print_r($msg);
                exit();
            }

            $this->gen_cookie($guide_identity);
            $url = app::get('wap')->router()->gen_url( array( 'app'=>'b2c','real'=>1,'ctl'=>'wap_product','args'=>array($product_id,'penker',$guide_identity)));
            header('Location: '.$url);
        }
        print_r($msg);
        exit();
    }
    private function gen_cookie($guide_identity){
        $path = kernel::base_url().'/index.php/wap/';
        if( !$_COOKIE['penker'] ){
            setcookie('penker','true',0,$path);
        }

        if( !$_COOKIE['guide_identity'] ){
            setcookie('guide_identity',$guide_identity,0,$path);
        }
    }
}
