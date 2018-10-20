<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;

class UsersController extends Controller
{
    //使用中间件来过滤用户操作
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function index()
    {
        $users = user::paginate(10);
        return view('users.index', compact('users'));
    }

    //
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    } 

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|min:2|max:50',
            'email'     => 'required|email|unique:users|max:255',
            'password'  => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        Auth::login($user);//自动登录
        session()->flash('success', '欢迎，您将在这里开始一段新的旅程！');
        return redirect()->route('users.show', [$user]);
    }

    // 编辑页面
    public function edit(User $user)
    {
        //授权策略，用户只能更新自己的个人资料
        $this->authorize('update',$user);
        
        return view('users.edit', compact('user'));
    }

    //保存更新
    public function update(User $user, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $this->authorize('update', $user);
        
        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        Session()->flash('success', '个人资料更新成功');
        return redirect()->route('users.show', $user->id);
    }

    // 删除用户
    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        $user->delete();

        Session()->flash('success', '成功删除用户！');
        return back();
    }
}
