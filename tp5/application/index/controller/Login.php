<?php

namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;
require_once('BinTodec.php');

class Login extends Controller{
    // 登录页面
    //判断是否自动登录
    public function index(){
        //判断session是否存在
        if (!session::has('uid')){
            //判断cookie是否存在
            if (Cookie::has('ck_uid') && Cookie::has('ck_pass')){
            $ck_uid=Cookie::get('ck_uid');
            $ck_pass=Cookie::get('ck_pass');
            $cc_name=Cache::get('cc_name');
            $cc_uid=Cache::get('cc_uid');
            $cc_pass=Cache::get('cc_pass');
            //判断cookie是否正确
            if ($ck_pass===$cc_pass && $ck_uid===$cc_uid){
                //登录成功，设置session
                session::set('user_uid',$ck_uid);
                session::set('user_name',$cc_name);
                session::set('user_pass',$ck_pass);
                $this->redirect('http://www.tp.com/User');
                }else{
                return view();
            }
            }else{
                return view();
            }
        }

    }
    //处理登录的提交页面
    public function check(){
        //接收数据
//        $a = $_POST;
//        if ($a){
//           return json_encode($a);
//           return view();
//        }

        $username=$_POST['username'];
        $password=md5($_POST['password']);
        //免登陆
        $remeber=$_POST['remember'];
        //验证码
        $code=$_POST['code'];
        //处理成16进制
        $str = $username;
        $arrs=bin_todec(md5($str),16);
        //SQL分表处理
        $table_name = 'users'.fmod($arrs[0],3);

        $data = Db::table($table_name)->where("username",$username)->find();
        if ($username===''or $password===''or $code===''){
            echo '输入不能为空';
        }else{
            if (captcha_check($code)) {
                //用户不存在
                $data = Db::table($table_name)->where("username",$username)->find();
                if ($data['username']){
                    if ($remeber) {
                        if ($username && $data['password'] === $password) {
                            ////免登陆成功，设置session
                            session::set('user_uid', $data['uid']);
                            session::set('user_name', $data['username']);
                            session::set('user_pass', $data['password']);
                            //设置缓存和cookie
                            $user_uid = session::get('user_uid');
                            $user_pass = session::get('user_pass');
                            Cookie::set('ck_uid', $user_uid, 3600);
                            Cookie::set('ck_pass', $user_pass, 3600);
                            Cache::set('cc_name', $data['username'], 3600);
                            Cache::set('cc_uid', $user_uid, 3600);
                            Cache::set('cc_pass', $user_pass, 3600);

                            echo 1;
                        } else {
                            echo'账号密码错误';
                        }
                    } else {
                        if ($username && $data['password'] === $password) {
                            //登录成功，设置session
                            session::set('user_uid', $data['uid']);
                            session::set('user_name', $data['username']);
                            session::set('user_pass', $data['password']);
                            //将1提交给JQ 进行跳转
                            echo 1;
                        } else {
                            echo'账号密码错误';
                        }
                    }
                }else{
                    echo '用户不存在';
                }
            }else{
                echo'验证码错误 ';
            }
        }
    }
    //加载到注册页面
    public function register(){
        return view();
    }
    //处理注册
    public function insert(){
        //获取数据
//        $a = $_POST;
//        if ($a){
//           return json_encode($a);
//           return view();
//        }

        $datas = input('post.');


        //处理成16进制
        $str = $datas['username'];
        $arrs=bin_todec(md5($str),16);
        //SQL分表处理
        $table_name = 'users'.fmod($arrs[0],3);
        //判断用户是否输入
        if ($datas['username']){
            # code...
            //判断用户名长度
            $size=strlen($datas['username']);
            if ($size>=6 && $size<=12){
                //判断用户是否存在
                $username=$datas['username'];
                $data = Db::table($table_name)->where("username",$username)->find();
                if (!$data['username']){
                    //判断是否输入密码
                    if ($datas['password']){
                        #code...
                        //判断密码是否一致
                        if ($datas['password']===$datas['repassword']){
                            //判断号码是否存在
                            $CellPhone=$datas['CellPhone'];
                            $dats0 = Db::table("users0")->where("CellPhone",$CellPhone)->find();
                            $dats1 = Db::table("users1")->where("CellPhone",$CellPhone)->find();
                            $dats2 = Db::table("users2")->where("CellPhone",$CellPhone)->find();
                            if (!$dats0['CellPhone']&&!$dats1['CellPhone']&&!$dats2['CellPhone']){
                                //数据库插入
                                $arr['uid'] = uniqid();
                                $arr['username']=$datas['username'];
                                //
                                $arr['password']=md5($datas['password']);
                                $arr['CellPhone']=$CellPhone;
                                //注册时间
                                $arr['time']=time();
                                $arr['status']=$datas['status'];

                                if (Db::table($table_name)->insert($arr)){
                                    echo 7;
                                }else{
                                    echo 6;
                                }
                            }else{
                                echo 5;
                            }
                        }else{
                            echo 3;
                        }
                    }else{
                        echo 4;
                    }
                }else{
                    echo 1;
                }

            }else{
                echo 2;
            }

        }else{
            echo 4;
        }
    }

    //退出
    public function quit(){
        Session::delete('user_name');
        Session::delete('user_uid');
        Session::delete('user_pass');
        Cookie::delete('ck_uid');
        Cookie::delete('ck_name');
        Cookie::delete('ck_pass');
        $this->redirect('Login/index');
    }
    public function createcode(){
        //创建URL资源
        $ch = curl_init();
        //设置URL
        $url = 'https://sms.yunpian.com/v1/sms/send.json';
        curl_setopt($ch,CURLOPT_URL,$url);
        //设置参数
        $mobile = input('CellPhone');//获取到手机号
        $code = substr(str_shuffle("012345678901234567890123456789"),0,6);//创建随机数字的验证码
        $paramArr = array(
            'apikey'=> '8bb78a20cebbbaca452cfe38118b7f3d',
            'mobile' => $mobile,
            'text' => '您的login验证码为'.$code
        );
        $param = '';
        foreach ($paramArr as $key => $value){
            $param.=urlencode($key).'='.urlencode($value).'&';
        }
        $param = substr($param,0,strlen($param)-1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//不验证证书
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //返回作为字符串的传输
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //输出字符串内容
        $output = curl_exec($ch);
        //关闭curl资源以释放系统资源
        curl_close($ch);
//        echo $output;
//        将js解码
        $outputArr = json_decode($output,true);
        if($outputArr['code']=='0') {
            //验证码发送成功
            //存到缓存中
            Cache::set('code', $code, 60);
            echo "验证码发送成功，请在60s内输入验证码";
        }else{
            echo "发送失败";
        }
    }
    public function checkcode(){
        $mobile =input('CellPhone');
        $code = input('code');
        $nowTimestr = date('Y-m-d H:i:s');
        $smscode = Cache::has('code');
        //判断缓存是否有code
        if($smscode){
            $smscodeObj = Cache::get('code');
            if ($smscodeObj==$code){
                $result = '验证码正确';
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
            }else{
                $result = '验证码错误';
                echo json_encode($result,JSON_UNESCAPED_UNICODE);
            }
        }else{
                $result = '验证码过期';
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }



}