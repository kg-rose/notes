# 使用 intended 访问之前的页面
> 需求：用户直接输入 ../user/10/edit 进入编辑界面，由于 中间件auth 的管理，会将用户弹回登陆界面。
> 但此时，用户如果登陆成功，则会返回网站根。
> 先要求：用户不登陆，访问编辑界面，跳转到登陆界面，登陆之后，跳转回编辑界面。
> 类似于 “发表评论” 等场景

* 非常简单：使用 `return redirect()->intended()` 跳转即可，在 LoginController.php 中
```
return redirect()->intended(route('home')); // intended() 可以填写一个参数为如果没有历史记录（比如我是直接访问的登陆页面），则默认跳转的地址（我设置为跳转到首页）
```

# 使用模型工厂填充数据
> 在项目开发时，比如现在，我即将学习如何在页面上分页展示数据列表。但此时，我没有多少数据，我想虚拟出来很多数据，难道要使用 sql语句 一条一条插入吗？当然不用，使用模型工厂即可。

* 模型工厂位于 /database/factories/
* 创建模型工厂使用命令 `php artisan make:factory XxFactory --model=Xx` => 创建模型工厂，最好指定一个具体被操作的模型。
* 模型工厂自带一个 UserFactory.php
```
$factory->define(App\User::class, function (Faker $faker) { //这里的 Faker类 是用来创建模拟的数据的
    return [
        'name' => $faker->name, //使用 faker 生成一个虚拟的用户名
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt('laravelstudy'), // bcrypt() => Hash::make()
        'remember_token' => str_random(10),
    ];
});
```
* 在 tinker 模式下使用模型工厂 `php artisan tinker`
```
# 调用全局 factory() 函数后链式函数调用 ->make(); 来虚拟一条新的数据
factory(App\User::class)->make();  // factory() 第一参数使用 UserFactory 定义的第一参数。 make() 只会虚拟数据，不会插入数据库

# 调用 create() 插入数据
factory(App\User::calss, 20)->create(); // factory() 第二参数是你要插入的数据条数
```

# 结合 Seeder 完成数据填充
* Seeder 都位于 /database/seeds/ 下
* 创建一个 Seeder `php artisan make:seeder XxSeeder`
* 默认有一个 DatabaseSeeder.php => 可以理解为所有 Seeder 的入口。
```
public function run() //调用该方法即可调用内部我们插入虚拟数据的方法 
{
    // $this->call(UsersTableSeeder::class); //这里给我们举了一个如何调用其他Seeder的例子
    $this->call(UserSeeder::class); //调用我们的UserSeeder
}
```
> 假如我们有一个 文章表 n:1 用户表 的数据表关系，我们应该在 DetabaseSeeder 中通过先后关系声明 => 先插入关于用户表的虚拟数据，再插入文章表的虚拟数据。
* 注意，打开 UserSeeder.php 你会发现该文件没有命令空间：它们的结构关系其实存在于 composer.json 中
```
"autoload": {
    "classmap": [
        "database/factories"
    ],
    "psr-4": {
        "App\\": "app/"
    }
},
```
* 因此，最好执行一次 `composer dumpautoload` 来更新一下classmap。
* 编辑 UserSeeder.php @ run()
```
<?php

use Illuminate\Database\Seeder;
use App\User; //这里引用一下我们的 App\User

class UserSeeder extends Seeder
{
    public function run()
    {
        // 创建100条数据
        factory(User::class, 100)->create();
        // 找到 id=1 的数据
        $user = User::find(1);
        // 将他设置为我们可以用的数据
        $data = [
            'name' => 'liuhaoyu',
            'email' => 'seederstudy@laravel.com',
            'password' => bcrypt('liuhaoyu')
        ];
        // 更新
        $user->update($data);
    }
}
```
> 有时候，我们需要一条可用的数据进行具体操作：因此我们可以在 run() 方法中再定义一次针对莫数据的 update() 操作。

* 调用 seed `php artisan db:seed` => 找 DatabaseSeeder.php 根据它的 run() 方法来按顺序调用里面的其他 Seeder 的 run() 方法

* 使用 `php artisan migrate:refresh --seed` 命令 **重构整个数据库（表删完再重建）--seed 即调用一次 db:seed 命令**

# 总结一下模拟填充数据
* /database/factories/ 下存放的都是模型工厂的文件，它们定义了我们将怎样插入数据（帮我们虚拟出 sql 语句）
* 模型工厂使用了 Faker 类帮我们虚构数据
* 创建模型工厂使用 `php artisan make:factory XxFactory --model=Model`
* 可以使用全局函数 `factory(模型工厂名::class, 创建条数)->create()` 来往数据表中插入数据。 `->make()` 可以看看具体虚拟的数据。 可以在 `php artisan tinker` 模型下调试这些命令。
--------------------------------------------
* /database/seeds/ 下帮我们存放了 Seeder 文件 => 帮助我们自动在多表生成模拟数据的文件
    * DataBaseSeeder.php => 可以理解为 `php artisan db:seed` 调用的文件
    * 在定义 DataBaseSeeder 的时候，注意区分一下多张数据表的关系，比如 “多篇文章 belongsTo 一个用户”，我们应该先虚拟插入用户数据，再插入文章的数据。因此再 run() 方法中，需要先 `$this->call(用户Seeder)` 再 call() 文章Seeder
    * 创建其他Seeder 使用命令 `php artisan make:seeder XxSeeder`
    * 我们需要定义 XxSeeder@run
    ```
    public function run()
    {
        // 在内部通过调用 factory() 来生成数据
        factory(User::class, 100)->create();
    }
    ```
    * 有时候我们需要在模拟了很多条数据之后，弄一个可用的 (比如我模拟很多用户，但是得有一条我可以用作登陆的)
    ```
    // 1 引用模型
    ...
    use App\User;

    // 2 在 run() 方法中更新某条数据
    ...
    $user = User::find(1);  //找到第1条数据
    $user->name = 'najiuyuanzou';
    $user->email = 'najiuyuanzou@test.com';
    $user->password = bcrypt('najiuyuanzou'); // bcrypt() = Hash::make()
    $user->save();
    ```
* 总结整个过程就是： `php artisan db:seed` => 调用 DatabaseSeeder@run => DatabaseSeeder 调用它声明先后顺序的其他 Seeder@run => 将虚拟数据插入数据表
* 有时候我们需要将数据库重来一次 => `php artisan migrate:refresh` => 全部回滚，然后重新生成数据表并清空数据。如果加上 `--seed` 参数，则会重新生成并且插入我们定义好的模拟的数据。
* 总体来说 **根/Database/** 就是一个帮助我们管理数据库的文件夹。
    * migrations => 数据库迁移 （数据库版本管理）
    * factories => 模型工厂 （伪造Sql语句）
    * seeds => “播种工具” （执行模型工厂中为多表打造的伪造的sql语句）
        * DatabaseSeeder => 入口
        * XxSeeder, ... => 针对其他表的其他Seeder

> seeds 这个播种工具是我自己瞎取的。。。

# 分页展示所有用户
* 确定路由和控制器： UserController@index => 展示用户列表。 路由则是 resource 直接帮我们定义好了的
* 在 _navbar.blade.php 中添加一个超链接展示 “用户列表”
```
<ul class="navbar-nav mr-auto">
    <li class="nav-item"><a class="nav-link" href="{{ route('user.index') }}">会员列表</a></li>
    <li class="nav-item"><a class="nav-link disabled" href="#">不可用</a></li>
</ul>
```
* 在 UserController@index 中查询数据并分页然后跳转到视图
```
// 定义 where 条件
    $where = [
        ['id', '>', 0],
        ['created_at', '>', time()],
    ];
    // 查询数据并分页
    $users = User::where($where) // where(查询条件)
        ->orderBy('id', 'desc') // orderBy('字段', 'desc | asc') 
        ->orderBy('created_at', 'desc') // 多个需要 orderBy 的字段可以写多条
        ->paginate(30); // paginate(每页页数)
    return view('user.index', [
        'users' => $users, //可以通过 view('视图名称', ['变量1'=>$变量1, '变量2'=>$变量2,]); 来抓取视图并分配变量
    ]);
```

* 在视图遍历数据并分页
```
{{-- foreach 遍历 $users --}} 
@foreach ($users as $user)
<tr>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>{{ $user->created_at }}</td>
</tr>
@endforeach

{{-- 生成分页链接 --}} 
{{ $users->links() }}
```

# 删除用户
* 我们需要给用户表添加一个 boolean 字段 'is_admin'， 由此值来判断用户是不是管理员。因此新建迁移文件`php artisan make:migration add_is_admin_user --table=users` => 我把名字取名叫做 `--table=users` 指明我们要编辑的数据表。
```
// up()
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false); //添加 布尔类型 字段 'is_admin' 默认值为 false
});


// down()
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('is_admin'); // 回滚时 删除该字段
});
``` 
* 执行一次迁移 `php artisan migrate` => users 表中增加了 'is_admin' 字段，可以编辑一下1号用户，设置它的 `is_admin = true`

> 但是这样太麻烦了，并且如果我们下次执行刷新迁移的时候，又需要再来修改一次 is_admin 字段的值，所以我们改一改Seeder的逻辑

* 编辑一下 UserSeeder 让1号用户为管理员
```
public function run()
{
    // 创建100条数据
    factory(User::class, 100)->create();
    // 找到 id=1 的数据
    $user = User::find(1);
    // 将他设置为我们可以用的数据
    $data = [
        'name' => 'liuhaoyu',
        'email' => 'seederstudy@laravel.com',
        'password' => bcrypt('liuhaoyu'),
        'is_admin' => true // 这里使1号用户为管理员
    ];
    // 更新
    $user->update($data);
}
```

* 执行 `php artisan migrate:refresh --seed`

* 处理视图 user.index.blade.php
```
# 增加一个表格项
{{-- 判断当前用户是否是admin，是则显示 --}}
@if (Auth::user()->is_admin)
    {{-- 这里调用一个“删除确认”的函数 --}}
    <a href="javascript:deleteConfirm();" class="btn btn-danger btn-sm">删除</a>
    {{-- 删除表单 --}}
    <form id="delete-user-from" method="POST" action="{{ route('user.destroy', $user->id) }}">
        {{-- 注明csrf_token --}}
        @csrf
        {{-- 伪造表单 method="DELETE" --}}
        @method('DELETE')
    </form>
@endif

# 删除确认的函数
function deleteConfirm() {
    var confirm = window.confirm('确定要删除这个用户吗？');
    if(confirm **= true) {
        $("#delete-user-from").submit(); //提交表单
    }else {
        window.alert('您选择不删除。');
    }
}
```

* 完成 UserController@destroy 逻辑
```
public function destroy(User $user)
{
    // 判断当前用户是否是 admin 
    if(!Auth::user()->is_admin) {
        session()->flash('danger', '只有管理员可以删除用户！');
        return redirect()->back();
    }

    // 删除用户
    $user->delete();
    session()->flash('success', '用户删除成功');
    return redirect()->back();
}
```

* 其实我们还可以：
    * 定义 UserPolicy@delete 策略
    ```
    public function delete(User $user, User $model)
    {
        if($user->is_admin) {
            return true;
        }
    }
    ```
    * 然后在 UserController@delete 中
    ```
    // 验证用户是不是admin
    $this->authorize('delete', $user);

    // 然后再删除
    ```

# 第一阶段结束总结和优化
* 前5天，我们学会了配置路由 /routes/web.php
* 学会了定义控制器 包括 restful控制器
* 学会了使用 migration / modelFactory / Seeder 来管理数据库
* 学会了使用在资源控制器中对某一些资源进行 CURD 操作
* 学会了使用 auth 对用户进行认证、使用 Policy 对用户进行权限控制
* 学会了在视图上的插值、判断、循环。以及配合 session flash 发送临时消息。
* 学会了使用 composer 命令安装中文包等。
* 最后我们优化一下，因为没有用过 Vue，所以在 /resources/assets/js/app.js中取消引用 Vue
```
# 注销他们
// window.Vue = require('vue');

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

// const app = new Vue({
//     el: '#app'
// });
```
