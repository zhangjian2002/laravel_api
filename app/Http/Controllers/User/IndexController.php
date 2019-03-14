<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class IndexController
{

    public $redis_h_u_key = 'api:h:u:';   //用户个人信息 hash


    //
    public function index(){

        $arr = [
            'name' => 'zhangjian',
            'age' => 17,
            'email' => 'zhangjian@qq.com'
        ];
        return $arr;

    }


    public function info(){
        phpinfo();
    }


    public function testU(){
        echo "<pre>";print_r($_POST);echo "</pre>";
    }


    /**
     * 用户登录
     */
    public function login(Request $request){

        $u = $request->post('u');
        $pass = $request ->post('pass');

//        echo "<pre>";print_r($_POST);echo "</pre>";

        //验证用户信息
        if(1){      //登录成功
            $uid = 1000;
            $str = time() + $uid + mt_rand(1111,9999);
            $token = substr(md5($str),10,20);

            //将token存入Redis
            $key = $this -> redis_h_u_key . $uid;
            Redis::hSet($key ,'token',$token);
            Redis::expire($key,3600*24*7); //过期时间一周
            echo $token;

        }else{

            //登录失败
        }

    }


    /*
     * 个人中心
     */
    public function center(){
//        echo "<pre>";print_r($_SERVER);echo "</pre>";
        $uid = $_GET['uid'];
        if(empty($_SERVER['HTTP_TOKEN'])){
            $response = [
                'errno' => 50000,
                'msg' => 'TOKEN Require'
            ];
        }else{
            //验证token有效  是否过期  是否伪造
            $key = $this -> redis_h_u_key . $uid;
            $token = Redis::hGet($key,'token');
            if($token == $_SERVER['HTTP_TOKEN']){
                $response = [
                    'errno' => 0,
                    'msg' => 'ok',
                    'data' => [
                        'aaa' => '1234',
                        'ccc' => '4567'
                    ]
                ];
            }else{
                $response = [
                    'errno' => 50000,
                    'msg' => 'Invalid Token',
                ];
            }
        }
        return $response;
    }
}
