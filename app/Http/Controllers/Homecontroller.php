<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class Homecontroller extends Controller
{
    
    //个人中心
    public function index(){  
        // 站点设置
        $site = DB::table('samsite')->where('weizhi','index')->first();
        // 结束 	
        $sion = session('user_name');
        $sioninfo=DB::table('user')->where('name',$sion)->select('id','name','email','phone','ztid')->first();
        $sioninfo->phone = substr_replace($sioninfo->phone, '****', 3,4);
        $info=DB::table('userinfo')->where('id',$sioninfo->id)->first();
         // 分类结束
        return view('home.index',['sioninfo'=>$sioninfo,'info'=>$info,'site'=>$site]);
    }

    //编辑个人中心数据
    public function bianji(Request $request){
        
        $data=$request->only('phone','email','id');
        $datas=[];
        if($request->hasFile('touimg')) {
            //获取文件的后缀名
            $suffix = $request->file('touimg')->extension();
            //创建一个新的随机名称
            $name = uniqid('img_').'.'.$suffix;
            //文件夹路径
            $dir = './uploads/'.date('Y-m-d');
            //移动文件
            $request->file('touimg')->move($dir, $name);
            //获取文件的路径
            $datas['touimg'] = trim($dir.'/'.$name,'.');
        }
        $datas['shengri']=$request->input('shengri');

      $xg=DB::table('user')->where('id',$request->input('id'))->update($data);
      $touimg=DB::table('userinfo')->where('id',$request->input('id'))->update($datas);
      return redirect('/home');
    }

     //订单
    public function order(){
        // 站点设置
        $site = DB::table('samsite')->where('weizhi','index')->first();

        $data = DB::table('order')->where('userid',session('user_id'))->get();

        foreach($data as $k=>&$v){

            $order_goods = DB::table('order_goods')->where('order',$v->id)->select('goodsid','num','price')->get();

            foreach ($order_goods as $key => &$value) {
                
                $value->goodstitle = DB::table('goods')->where('id',$value->goodsid)->value('title');
                $value->goodscontent = DB::table('goods')->where('id',$value->goodsid)->value('content');

                $value->goodsimg = DB::table('goods_pic')->where(['goodsid'=>$value->goodsid,'img_lx'=>0])->value('imgs');
            }
            $v->goods = $order_goods;
        }
        



        // 结束
        return view('home.order',['site'=>$site,'data'=>$data]);
    }
    //结算
    public function jiesuan(Request $request){
        $datas = $request->input('data');
        $zongji = 0;
        foreach ($datas as $k => $v) {
            $datas[$k]['goods'] = DB::table('goods')->where('id',$v['goodsid'])->select('title','price','content')->first();
            $datas[$k]['goods_pic'] = DB::table('goods_pic')->where(['goodsid'=>$v['goodsid'],'img_lx'=>2])->select('imgs')->take(1)->first();
            $datas[$k]['xiaoji'] = floatval(($datas[$k]['goods']->price))*intval($datas[$k]['num']);
            $zongji += $datas[$k]['xiaoji'];
        }

        $addresses = DB::table('address')->where('userid',session('user_id'))->get();
        $num=count($addresses);
            //dd($num);
        foreach ($addresses as $key => &$value) {
            $value->pname = DB::table('dt_area')->where('id',$value->pro)->value('area_name');
            $value->cname = DB::table('dt_area')->where('id',$value->city)->value('area_name');
            $value->xname = DB::table('dt_area')->where('id',$value->county)->value('area_name');
            }   
        // 站点设置
        $site = DB::table('samsite')->where('weizhi','index')->first();
        // 结束    
        $cartid = $request->input('cartid');
        return view('home.jiesuan',compact('datas','zongji','addresses','num','site','cartid'));
    }

    
    //评论
    public function pinglun(){
        // 站点设置
        $site = DB::table('samsite')->where('weizhi','index')->first();
        // 结束 
        return view('home.pinglun',['site'=>$site]);
    }
    
}
