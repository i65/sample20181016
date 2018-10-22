<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Mail;
use Auth;

class UsersController extends Controller
{
    //使用中间件来过滤用户操作
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
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

        //Auth::login($user);//登录
        // session()->flash('success', '欢迎，您将在这里开始一段新的旅程！');
        // return redirect()->route('users.show', [$user]);

        $this->sendEmailConfirmationTo($user);//上面的登录操作改为发送邮箱操作
        session()->flash('success', '验证邮件已发送到您的注册邮箱上，请注意查收！');
        return redirect('/');
        
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

    //发送邮件通知
    protected function sendEmailConfirmationTo($user)
    {
        //现在我们已经在环境配置文件完善了邮件的发送配置，因此不再需要使用 from 方法：
        $view = "emails.confirm";
        $data = compact('user');
        // $from = "zhanyuanwen@qq.com";
        // $name = "zhanyuanwen";
        $to = $user->email;
        $subject = "感谢您注册 Sample 应用，请确认您的邮箱";

        // Mail::send($view, $data, function($message) use ($from, $name, $to, $subject){
        //     $message->from($from, $name)->to($to)->subject($subject);
        // });

        Mail::send($view, $data, function($message) use ($to, $subject){
            $message->to($to)->subject($subject);
        });
    }

    //邮件激活
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        
        $user->activated = true;
        $user->activation_token = null;
        $user->save();
        
        Auth::login($user);
        Session()->flash('success', '恭喜您，激活成功！');
        return redirect()->route('users.show', [$user]);
    }
}
