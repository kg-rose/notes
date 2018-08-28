# 10 v-if 使用
* 主要作用： 控制宿主元素是否显示/隐藏。
* 基础使用 常见的注册时要求你同意协议然后显示和隐藏 “确定注册” 按钮功能的实现
```
<div id="app">
    <input type="checkbox" v-model="checked"> 当前checked = {{checked}}
    <span v-if="!checked">请勾选</span>
    <span v-else>您已勾选</span>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            checked: false,
            age: 0,
        },
    });
</script>
```
* `v-if="条件表达式"`, `v-if & v-else` **两个宿主元素必须挨在一起，中间不能有其他元素。**
* if else-if else
```
...
    <label for="age">请输入你的年龄</label>
    <input type="text" v-model="age" id="age"> ，
    <span v-if="age<20">少年</span>
    <span v-else-if="age<50">中年</span>
    <span v-else>老年</span>
...
```
* 在 data 里面定义一个 `age: 默认值` 即可

# 11 使用 key 给宿主元素一个单独的标识令牌
```
<div id="app">
    <input type="radio" value="email" v-model="registerType">使用邮箱注册
    <input type="radio" value="phone" v-model="registerType">使用手机注册
    <br>
    <template v-if="registerType ** 'email'">
        <label for="email">邮箱：</label> <input type="text" id="email" name="username" key="username-email">
    </template>
    <template v-if="registerType ** 'phone'">
        <label for="phone">手机：</label> <input type="text" id="phone" name="username" key="username-phone">
    </template>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            registerType: 'email'
        },
    });
</script>
```

* 在上面的代码中，我们要求无论使用 邮箱 还是 手机 注册，写进数据表的字段都叫 'username' ，因此我们的表单中input.name = "username"， 那么就会出现一个问题，比如我先用手机注册，填好了表单，又选择用邮箱注册，按理说，这时最好的用户体验应该是将 input.value 清空，然而如果我们让两个表单的 input.name 一样，则不会清空。

* 所以给 表单项 input 绑定一个属性 `key="令牌名称"` ，就等于声明这个元素是一个与众不同的元素，即使两个 input.name 一样。

# 12 v-show
```
<div id="app">
    <h1 v-if="status">测试内容1</h1>
    <hr>
    <h1 v-show="status">测试内容2</h1>
    <hr>
    <input type="checkbox" v-model="status">显示/隐藏
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            status: true,
        },
    });
</script>
``` 

* v-show 和 v-if 的区别： **v-if="false" 则直接从html代码中移除元素** ， **v-show="false" 则设置元素style="display:none; "**

* v-if 还有 v-else-if 以及 v-else 配合使用, 而 v-show 没有。
* v-show 因为只操作 css ，所以性能比 v-if 好。

> 能用 v-show 就尽量使用 v-show

# 13 v-for 遍历数据
* 代码
```
<div id="app">
<ul>
    <!-- 遍历数组集合 -->
    <!-- (单个对象, 对象在数组中的下标) of 对象集合 -->
    <li v-for="(user, key) of users">
        <span> {{ key }} </span>
        <span> {{ user.id }} </span> |
        <span> {{ user.name }} </span>
    </li>
</ul>
</div>

<script>
var app = new Vue({
    el: '#app',
    data: {
        users: [
            {id: 11, name: 'DouZiZhang'},
            {id: 22, name: 'ZuiZiMa'},
            {id: 33, name: 'LaJi'},
        ]
    },
});
</script>
```

* `v-for="item of items"` => 遍历数据，有多少条，就生成多少个宿主元素和它内部的内容
* `v-for="(item, key) of items"` => 通过 key 我们可以获取当前被遍历的对象在对象集合中的下标 

# 14 v-for 直接遍历对象和整数
```
<div id="app">
    <!-- v-for 可以直接遍历对象，此时key为属性名称, index属性下标 -->
    <p v-for="(item, key, index) in user">{{index}} - {{key}} - {{item}}</p>
    <ul>
        <!-- v-for 可以直接遍历 integer 数值 -->
        <li v-for="v in 20">这是第{{v}}条列表项</li>
    </ul>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            user: {
                name: 'liuhaoyu',
                age: 22,
                lang: 'php, html, js, css'
            }
        },
    });
</script>
```

* `v-for="data of datas"` => `v-for="data in datas"` 一样的效果

# 15 v-for 配合 obj.filter，在 computed 中实现只显示 男用户/女用户的功能
* 代码
```
 <div id="app">
    <ul>
        <li v-for="user of showUsers">
            <span>{{ user.name }}</span>
            <span>{{ user.sex }}</span>
        </li>
    </ul>
    <input type="radio" v-model="sex" value="male"> 男
    <input type="radio" v-model="sex" value="female"> 女
</div>

<script>
    var app = new Vue({
        el: '#app',
        computed: {
            showUsers() {
                if(this.sex ** 'all') {
                    return this.users;
                }else {
                    // 这里必须使用 ES6提供的 链式函数 ，否则 this 会指向 window 而不是 app
                    return this.users.filter((user)=> { //调用对象的过滤方法 filter(回调函数，为真则不过滤，为假则过滤)
                        return user.sex ** this.sex;
                    });
                }
            }
        },
        data: {
            users: [
                {name: 'boy', sex: 'male'},
                {name: 'man', sex: 'male'},
                {name: 'girl', sex: 'female'},
                {name: 'woman', sex: 'female'},
            ],
            sex: 'all',
        },
    });
</script>
``` 

* 定义一个 `computed: { 虚拟属性():{ //通过代码确定这个属性的值 } }`
* 通过 `obj.filter(回调函数，为假则过滤掉，为真则保存下来)` 来过滤 男女性别
* 回调函数中必须写 `()=> {}` ES6链式函数， 否则this指向很迷(指向window)

# 16 使用 vue 提供的 变异push() 实现一个备忘录
* 代码
```
<div id="app">
    <ul>
        <li v-for="tip of tips"> {{ tip.content }} </li>
    </ul>

    <textarea v-model="newTip"></textarea>
    <br>
    <!-- 通过 v-on:click="addNewTip" 绑定 methods 下定义的 addNewTip() -->
    <button v-on:click="addNewTip">新增</button>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            newTip: '',
            tips: [
                {content: '今天要做的事情'},
            ],
        },
        // 定义事件
        methods: {
            addNewTip() {
                // 处理要插入的数据，是一个对象
                var newTip = {content: this.newTip};
                // JS 的 push() 只能向数组中插入数据，而这里调用的 push() 是 Vue 帮我们变异过的 push()，可以向对象集合中插入数据。
                this.tips.push(newTip);
            }
        }
    });
</script>
```

* `v-on:事件="methods下定义的函数"` => Vue 中为元素绑定事件
* 默认 tips 只有一条信息， 我们通过 `this.tips.push()` 向 tips 插入新信息。

# 17 另外三个操作数组的函数在 Vue 中的变异
* 代码
```
<div id="app">
    <ul>
        <li v-for="tip of tips"> {{ tip.content }} </li>
    </ul>

    <textarea v-model="newTip"></textarea>
    <br>
    <!-- 通过 v-on:click="addNewTip" 绑定 methods 下定义的 addNewTip() -->
    <button v-on:click="addNewTip(true)">添加到前面</button>
    <button v-on:click="addNewTip(false)">添加到后面</button>
    <br>
    <button v-on:click="deleteTip(true)">删除第一条</button>
    <button v-on:click="deleteTip(false)">删除最后一条</button>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            newTip: '',
            tips: [
                // {content: '今天要做的事情'},
            ],
        },
        // 定义事件
        methods: {
            addNewTip(flag) {
                var newTip = {content: this.newTip};
                // 通过 flag 判断从前面还是后面添加
                if(flag) {
                    this.tips.unshift(newTip); // unshift() 添加到前面
                }else {
                    this.tips.push(newTip); // push() 添加到后面
                }
            },

            deleteTip(flag) {
                // 通过 flag 判断删除第一条还是最后一条
                if(flag) {
                    this.tips.shift(); // shift() 删除最前面的一条数据
                }else {
                    this.tips.pop(); // pop() 删除最后面的一条数据
                }
            }
        }
    });
</script>
```

* 其实这些函数都是 原生js 自带的，只不过在 Vue 中，可以理解为 Vue 重写了他们，让他们有更多的功能，所以叫 “变异函数”

* `push()` / `unshift()` 添加信息到 最后面 / 最前面。
* `php()` / `shift()` 删除 最后面 / 最前面 的信息。

# 18 变异 splice 删除单条数据
* 接着使用 vue17.html 的代码
```
<!-- 在遍历的时候多添加一个 key（当前遍历元素的下标） -->
<!-- 绑定点击事件 romoveThisTip(下标) -->
<li v-for="(tip, key) of tips"> {{ tip.content }} <button v-on:click="removeThisTip(key)">删除这条</button></li>

<!-- 调用 splice() 方法删除信息 -->
removeThisTip(key) {
    // splice(从哪条开始删, 删除多少条);
    this.tips.splice(key, 1);
},
```

* `splice(key, howMany)` => key是删除开始的下标， howMany是删除多少条

# 19 变异 sort 排序 、 reverse 反转当前顺序
* 接着使用 vue18.html 的代码
```
# 给每条信息添加一个 id 属性
{id: 1, content: '今天要做的事情'},

# 在 html 代码中显示 id {{ tip.id }}
<li v-for="(tip, key) of tips">  {{tip.id}} - {{ tip.content }} <button v-on:click="removeThisTip(key)">删除这条</button></li>

# 增加几个按钮
<button v-on:click="doSort(true)">按编号从小到大排序</button>
<button v-on:click="doSort(false)">按编号从大到小排序</button>
<button v-on:click="doReverse">反转排序</button>

# 在 methods 中完成这几个方法
// 排序
doSort(flag) {
    if(flag) {
        this.tips.sort(function(a, b) { // sort(回调函数(前一个元素, 后面的元素) { 为真则移动 })
            return a.id < b.id; //注意参数进来的是对象，所以还得调用一下 obj.id 属性
        });
    }else {
        this.tips.sort(function(a, b) {
            return a.id > b.id;
        });
    }
},
// 反转
doReverse() {
    this.tips.reverse();
},
```

* `vueDataObj.sort(function(a, b) { // ...  })`  排序
* `vueDataObj.reverse()` 颠倒当前对象集合

# 20 变异 filter 配合 RegExp 实现搜索功能
* 继续使用 vue19.html 的内容
```
# html 
<!-- 现在 html 代码里面添加一个绑定 keyWord 的输入框 -->
<!-- 并绑定一个 keyup.enter 事件： 当敲击键盘enter时出发 search -->
<input type="text" v-model="keyWord" v-on:keyup.enter="search">

# script
// 搜索
search() {
    var reg = new RegExp(this.keyWord, 'i'); // 使用 RegExp 构建正则表达式 /keyword/i ， i 即 不区分大小写
    // 使用 filter 来过滤 tips 
    this.tips = this.tips.filter((tip)=> {
        return reg.test(tip.content); // RegExp.test(当字符串符合正则表达式规则，则返回true)
    });
    // 有一个问题： 过滤之后，原来的 this.tips.filter 不存在了...
},
```
