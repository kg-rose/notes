# 引用vue
* 可以引用来自 [bootcdn的vue资源](http://www.bootcdn.cn/vue/)
* 也可以直接下载下来。
* `<script src="vue.js"></script>` 在.html文件中引用.js即。

# 1 构建第一个vue程序
* 先上代码
```
<!-- 指定元素id -->
<div id="app"> 
    <h1> {{title}} </h1> <!-- 插值{{}} -->
</div>
<script>
    // 新建 app 组件
    var app = new Vue({
        el: '#app', //绑定id
        data: {
            title: '标题', //配置数据
        },
    });
</script>
```
* vue 不能够挂载在 `<html>` 和 `<body>` 标签上。
* vue 可以找到 class 和 id的元素
* vue 使用 `new Vue(传递配置信息对象)` 的方式实例化
* 配置信息中我们接触了2个属性 `el: '绑定的元素'`, `data: {传递给元素键值对数据}`
* 在页面调试的时候，我们可以使用控制台输入代码 `app.title="测试"` 来体验Vue动态渲染页面 **注意这里不是app.data.title** 

# 2 操作元素属性
* 要操作元素属性，必要要在 html代码 中使用 `v-bind:属性="key"` 来绑定属性，比如 `<h1 v-bind:class="color"> ... </h1>`
* 然后在 Vue 的 js代码中 编辑key
```
var app = new Vue({
    el: '#app',
    data: {
        // key: value,
        color: 'red',
    },
});
```
* 定义一个 css 样式 `.red { color: red; }`
* 这样一来，html代码在浏览器中其实是被解析成 `<h1 class="red"> ... </h1>`， 即展示红色字体。
* `v-bind:` 由于频繁使用，因此可以简写为 `:` 。
* 如果我又想绑定，同时又想使用其他的样式类呢？ `<h1 v-bind:class="color" :class="'text-center'"> ... </h1>` `:class="'如果使用双引号包单引号再包字符串，则直接解析为字符串'"`

# 3 v-model & v-once
* 先上代码
```
<div id="app">
    <h1> {{ content }} </h1>
    <h2 v-once> {{ content }} </h1>
    <input type="text" v-model="content">
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            content: '远走',
        },
    });
</script>
```
* 可以看到： h1就是普通的显示数据， h2则添加了一个`v-once`， input则添加了一个`v-model`
* 修改 input 的值，会发现 h1 的内容会发生改变， h2 则不会，是因为 h2 的 `v-once` 告诉Vue，**h2的内容，只从data里读一次，并不希望随着数据绑定的修改而修改**
* 修改 input 的值，h1 会随之发生改变的原因就是因为 `v-model` 绑定了 `app.content` 。而 h1 没有使用 `v-once`。
* 总结就是 `v-model` 绑定某个数据，input.value更改，该数据会随之发生变化。如果想只拿初始值，就需要使用 `v-once` 。

> 注意元素中的属性，不需要{{}} 。而标签中的内容，需要使用{{}}

# 4 v-text & v-html
* laravel框架在视图层也使用 `{{}}` 来显示变量，怎么解决？
```
<div id="app">
    <div v-text="content"></div>
    <div v-html="content"></div>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            content: '<p>v-text & v-html</p>'
        },
    });
</script>
```
使用 `v-text` 渲染绑定标签内容为纯文本， `v-html` 则会解析 html标签

# 5使用 js表达式 在标签属性或者内容里面进行运算
* 代码
```
<div id="app">
    <p :class="'style'+n">test...</p>
    <p><small>这是当前的n的值</small> {{n+'因为切换颜色的单选框.value是字符串，所以我这里也变成字符串了'}} </p>
    <input type="radio" v-model="n" value="1"> 红 
    <input type="radio" v-model="n" value="2"> 蓝
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            n: 1,
        },
    });
</script>
```
* 在内容进行运算 `{{ n+1 }}`
* 在属性进行运算 `:class="'style'+n"` => 这里的 style 对应 `.style`（因为单引号包起来的），而 n 对应 `app.n` => js中， '字符串'+数字 = '字符串连接数字'。因此对应的样式就是 .style1 和 .style2。
* 在我们使用 单选框 修改n的value的时候，不用给 input.name 指定值，通过 `v-model` 绑定的，html会自动识别他们的关系。（不会出现两个都能选中的情况） 
* 因为 value="字符串" 所以我们在内容再显示 `{{ n+1 }}` 的时候，就变成了 11 或者 21 (+当字符串连接运算处理了)

# 6 computed 计算
* 代码
```
<div id="app">
    <input type="text" v-model="num1"> +
    <input type="text" v-model="num2"> =
    <input type="text" v-model="sum">
</div>

<script>
    var app = new Vue({
        el: '#app',
        computed: {
            sum: function() {
                return this.num1*1 + this.num2*1;
            }
        },
        data: {
            num1: 0,
            num2: 0,  
        },
    });
</script>
```
* 定义计算属性 computed `computed: { 虚拟属性: function() { //执行运算的代码 } }`
* 上面提到过，访问 data 里定义的数据应该是直接用 `app.key` 而不是 `app.data.key` ，在组件定义的内部也一样，使用 `this.key` 访问 data 下定义的值
* `v-model="sum"` 即绑定了 computed 中的 虚拟属性'sum' (因为这个sum其实不存在于内存中)
* js中想要 + 处理为 加法运算， 而 + 两边又是 字符串的时候，给字符串做一次乘法运算。
* `sum: function() { //... }` 可以简写为 `sum() { //... }`

# 7 watch 监听某个 data 的变化
* 先用 cnpm 装一个 插件 `cnpm install axios` ，并在页面上引用 `<script src="node_modules/axios/dist/axios.min.js"></script>` 。
* 代码
```
<div id="app">
    <input type="text" v-model="keyWord">
    {{ content }}
</div>

<script>
    var app = new Vue({
        el: '#app',
        watch: {
            keyWord: function(newWord, oldWord) {
                // 将新值作为数据参数 $_GET['word'] 请求 7.php 
                axios.get('./7.php?word=' + newWord).then(function(response) {
                    app.content = response.data;
                });
            }
        },
        data: {
            keyWord: '',
            content: '',
        },
    });
</script>
```
* 监听的使用 `watch: { 监听的data: function(第一个参数是旧值, 第二个参数是新值) { //值发生变化后触发函数执行的代码 } }`
* 使用 `axios.get(url)` 方式请求我们创建的 7.php `<?php echo '测试 test...' . $_GET['word'];` 。
* 使用 `axios.then()` 内部使用回调函数处理逻辑，修改我们的 `app.content` ，这里不能用 `this.content` 。
* 最终实现了一个： 监听 app.keyWord 值的变化，发生变化时异步请求(通过 axios 实现)请求后台php程序，然后修改了前台的app.content的值的功能：（百度搜索，输入关键字，显示可以热门搜索列表的功能），只是我们没有在php程序中去访问和检索数据库罢了。 

> 通常来说502是后台有问题(php) 404是前台有问题(没找到url，地址写错了)

* 优化： 监听 keyWord 的变化时，，每变化一次，我们都去请求了一次 7.php ，相当消耗资源，所以我们装一个 lodash `cnpm install lodash` ， 在页面引用它并且使用 `_.becoune(方法, 等待时间)` 来改写监听，以控制一定时间内的请求次数
```
keyWord: _.debounce( //第一个参数是调用的函数， 第二个参数是等待的时间
        function(newWord, oldWord) {
        // 将新值作为数据参数 $_GET['word'] 请求 7.php 
        axios.get('./7.php?word=' + newWord).then(function(response) {
            app.content = response.data;
        })
    },
    3000, //等待时间（3s才执行一次函数）
)
```

# 8 使用数组的对象导入样式
* 先定义几个样式
```
.blue {color: blue;}
.red {color: red;}
.font {font-size: 30px;}
```

* 具体代码
```
<div id="app">
    <!-- vbind:class 简写为 :class -->
    <!-- 会覆盖 class 定义的样式 因为 :class 会在 class 载入后再载入。 -->
    <h1 class="blue" :class="style1">测试文字</h1>
    <hr>
    <!-- 可以绑定数组 -->
    <h1 :class="[style2, style3]">测试文字</h1>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            style1: {red: true, font: true}, //设置为true 则开启使用样式
            style2: 'blue', // key: value 这里的value对应 <style>里面定义的样式</style>
            style3: 'font',
        },
    });
</script>
```

* `v-bind:class` => `:class` 会覆盖普通的html的 `class` 属性
* `:class=对象` , 在 data 中定义对象， `key为自己定义的样式: value为true则确定使用 false则不使用`

# 9 修改 style 属性的几种方式
```
<div id="app">
    <!-- 1、直接写对象 -->
    <p :style="{color: 'red', fontSize: size+'px'}">嗯</p> 
    <!-- 注意: 【''】单引号包起来的才是字符串，否则比如 size 就一定是在data里面定义的变量 -->
    <input type="number" v-model="size">
    
    <!-- 2、使用对象： 再次强调这里的 styleObj 是 data 里定义的对象 -->
    <p :style="styleObj">哈</p>
    
    <!-- 3、使用数组：导入多个对象  -->
    <p :style="[styleObj, styleObj2]">哦</p>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            size: 20,
            styleObj: {
                color: 'blue',
                fontSize: '30px',
            },
            styleObj2: {
                background: 'red',
            }
        },
    });
</script>
```
