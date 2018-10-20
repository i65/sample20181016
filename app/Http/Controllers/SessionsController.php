<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{

    //guest用于指定一些只允许未登录用户访问的动作
    public function __construct()
    {
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }
    //
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials, $request->has('remember'))){//登录成功
            Session()->flash('success', '欢迎回来！');

            //intended方法可将页面重定向到上一次请求尝试访问的页面上，并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上
            return redirect()->intended(route('users.show', [Auth::user()]));
        }else{//登录失败
            Session()->flash('danger', '抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }       
    }

    //退出登录
    public function destroy()
    {
        Auth::logout();
        Session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
