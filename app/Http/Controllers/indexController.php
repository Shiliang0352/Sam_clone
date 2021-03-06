<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;
use Hash;

class indexController extends Controller
{
	// 首页展示
    public function index()
    {   
        // 站点设置
        $site = DB::table('samsite')->first();    	
        // 广告设置
        $ad = DB::table('ad')->get();
        // 评论设置
        $pl = DB::table('pinglun')->where(['shenghe' => 1,'ztid' => 1])->get();
        foreach ($pl as $k => $v) {
            $v->username = DB::table('user')->where('id',$v->userid)->value('name');
            $tu = DB::table('pl_img')->where('userid',$v->userid)->select('img_url')->get();
            foreach($tu as $ks=>$vs){
                $v->plt[$ks] = $vs->img_url;
            }            
            $v->tx = DB::table('userinfo')->where('id',$v->userid)->value('touimg');
        }
        // 商品设置
        $goods = DB::table('goods')->get();
        foreach ($goods as $key => &$value) {
            $value->pic = DB::table('goods_pic')->where('goodsid',$value->id)->where('img_lx',2)->value('imgs');
        } 
        // 输出视图
        return view('index',compact('site','ad','pl','goods'));
    }
    // 列表页
    public function list($id)
    {   
        // 站点设置
        $site = DB::table('samsite')->where('weizhi','index')->first();
        // 站点设置结束
        // 分类
        $a['yi']=DB::table('class')->where('id',$id)->first();
        $a['yi']->er = DB::table('class')->where('pid',$a['yi']->id)->get();
        foreach($a['yi']->er as $k=>$v){
           $v->san =  DB::table('class')->where('pid',$v->id)->get();
        }
        // 分类结束        
        // 商品分类
        // dd($a);
        $shop=[];
        foreach ($a as $key => $value) {
            foreach ($value->er as $ke => $val) {
                foreach ($val->san as $k => $v) {
                    $shop[]=$v->id;
                }
            }
        }
        // 商品分类结束
        // var_dump($shop);
        // 商品
        $goods = DB::table('goods')->select('id','title','price','content','huodong','flid','ztid')->get();
        foreach ($goods as $key => &$value) {
            $value->pic = DB::table('goods_pic')->where('goodsid',$value->id)->where('img_lx',2)->value('imgs');
        }        
        // 商品结束
        // dd($goods); 
    	return view('list',compact('goods','a','shop','site'));
    }
    //登陆
    public function login()
    {
        if(session('user_name')){
            return redirect('/');
        }else{
            return view('login');
        }
    }
    //登陆验证
    public function dologin(Request $request)
    {
        $data = $request->except('_token');
        $rs = DB::table('user')->where('name',$data['name'])->first();
        if($rs){
            $rss = Hash::check($request->pwd,$rs->pwd);
            if($rss){
                session(['user_name'=>$rs->name,'user_id'=>$rs->id]);
                return back();
            }else{
                return back()->with('msg','用户名密码错误!');
            }
        }else{
            return back()->with('msg','用户名密码错误!');
        }
    }
    //注销操作
    public function logout()
    {
        session()->forget('user_name');
        return redirect('/');
    }
}
