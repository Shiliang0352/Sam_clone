<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class indexController extends Controller
{
	// 首页展示
    public function index()
    {
    	return view('index');
    }
    // 列表页
    public function list()
    {
    	return view('list');
    }
    //登陆
    public function login()
    {
    	echo "denglu";
    }
    //注册
    public function signin($value='')
    {
    	# code...
    }
}
