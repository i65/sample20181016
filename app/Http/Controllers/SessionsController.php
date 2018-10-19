<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

class SessionsController extends Controller
{
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
            return redirect()->route('users.show', [Auth::user()]);
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