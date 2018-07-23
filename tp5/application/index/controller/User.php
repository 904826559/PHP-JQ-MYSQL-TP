<?php

namespace app\index\controller;

use function PHPSTORM_META\elementType;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class User extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //判断是否已经登录，没登录则跳到登录界面
         if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
        //查询
        $data = Db::query("select * from user");

        //给页面分配数据
        $this->assign("data",$data);

        // 加载页面
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //判断是否已经登录，没登录则跳到登录界面
        if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
        //加载页面
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    //处理增加数据
    public function save(Request $request)
    {
        //判断是否已经登录，没登录则跳到登录界面
        if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
        //接收数据
        $data=input("post.");
//        var_dump($data);
        //执行数据库插入
        $code = Db::execute("insert into user VALUE(null,:name1,:name2)",$data);
        //判断是否成功
        if($code){
            //跳转
            $this->success("操作成功",'/User');

        }else{
            $this->error("操作失败");
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //判断是否已经登录，没登录则跳到登录界面
        if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
        // 从数据库中查询被修改的数据

        $data=Db::query("select * from user where id = ?",[$id]);
        //分配数据
//        var_dump($data);
        $this->assign("data",$data[0]);//因是数组，所以$data[0]
        //加载页面
        return view();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //判断是否已经登录，没登录则跳到登录界面
        if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
//        var_dump(input());
        //接收数据并过滤掉_method数据
        $data=Request::instance()->except('_method');
//        var_dump($data);
        //执行数据库修改操作
        $code=Db::execute("update user set name1=:name1,name2=:name2 where id=:id",$data);
        //判断是否成功
        if ($code){
            $this->success("数据修改成功",'/user');
        }else{
            $this->error("修改操作失败");
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断是否已经登录，没登录则跳到登录界面
        if(!Session::has('user_uid')){
            $this->redirect('Login/index');
            exit;
        }
        //执行删除
        $code = Db::execute("delete from user where id = $id");
        //判断删除成功
        if ($code) {
            #code...
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

}
