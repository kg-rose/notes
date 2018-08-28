# 21 事件的基本使用
* 代码
```
<div id="app">
    <ul>
        <!-- v-on:事件="触发事件调用的函数(没有参数可以省略这个括号)" -->
        <!-- 可以简写为 @事件="函数" -->
        <li v-for="comment of comments" @dblclick="remove()"> {{ comment.id }} - {{ comment.content }} </li>
    </ul>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            comments: [
                {id: 1, content:"这是一条评论1"},
            ],
        },
        // 事件必须要写到 methods 里面
        methods: {
            remove() {
                console.log(1);
            }
        }
    });
</script>
```

* 事件绑定：在宿主元素内 `v-on:事件="事件触发调用的函数()"` , 如果没有参数列表可以省略。
* 事件可以简写为 `@事件="函数()"`。
* 事件都写在 **methods** 里面。

# 22 事件修饰符 prevent 阻止默认事件
* 代码
```
<div id="app">
    <h3>表单一：不阻止，会刷新页面（action提交给本页）</h3>
    <form action="">
        <p>{{ time }}</p>
        <button type="submit">提交</button>
    </form>
    <hr>
    <h3>表单二：阻止默认事件的表单: @绑定事件在事件中阻止</h3>
    <!-- 传递 $event 参数，代表这个表单。 -->
    <form action="" @submit="submitFormOne($event)">
        <p>{{ time }}</p>
        <button type="submit">提交</button>
    </form>
    <hr>
    <h3>表单三：阻止默认事件的Vue写法: @事件.prevent</h3>
    <!-- 直接使用 @submit.prevent 来阻止 -->
    <form action="" @submit.prevent="submitFormTwo">
        <p>{{ time }}</p>
        <button type="submit">提交</button>
    </form>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            time : new Date(),
        },
        methods: {
            // 阻止表单二的默认事件
            submitFormOne(event) {
                // 采用原生的js方式，阻止表单提交
                event.preventDefault();
                alert("事件已阻止");
            },
            // 阻止表单三的默认事件
            submitFormTwo() {
                alert("事件已阻止");
            }
        }
    });
</script>
```

* 方法二是在将 **$event** 作为参数，传递给了 methods 里的方法，然后使用 `event.preventDefault()` 原生的方式来阻止提交的默认事件。
* 方法三是直接再绑定事件时，使用 **prevent** 修饰符 `@submit.prevent` 来阻止默认事件。

# 23 利用事件修饰符 阻止冒泡向上执行相同事件
* 代码
```
<div id="app">
    <!-- 先来看这个例子：冒泡向上执行事件 -->
    <div @click="showDiv"> <!-- 这是一个父级元素 -->
        <a href="https://www.baidu.com" @click.prevent="stopClick">点我1</a> <!-- 这是一个子元素 -->
    </div> <!-- 上面这个例子，点击a会触发 stopClick 事件 **以及 showDiv** -->

    <!-- 阻止这样的事情发生1 **stop** 不继续向上冒泡执行  -->
    <div @click="showDiv">
        <a href="https://www.baidu.com" @click.prevent.stop="stopClick">点我2</a> <!-- 这里通过链式操作，增加stop -->
    </div>

    <!-- 阻止这样的事情发生2 **self** 只有点击自己才触发 -->
    <div @click.self="showDiv" :style="{border: '1px solid'}">
        <a href="https://www.baidu.com" @click.prevent="stopClick">点我3</a> <!-- 这里通过链式操作，增加stop -->
    </div>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            // 阻止 a 跳转到百度
            stopClick() {
                alert('阻止了a默认事件')
            },
            // 父级 div 的点击事件
            showDiv() {
                alert('触发了div的点击事件');
            }
        },
    });
</script>
```

* 例子中显示了JS的冒泡执行： 子元素和父元素都有点击事件，子元素触发，如果不通过一定方法阻止，那么会向上继续执行父元素的点击事件。
* 可以通过 `<子元素 @事件.stop="函数">` 来阻止向上执行。
* 或者 `<父元素 @事件.self="函数">` 来迫使只有点击父元素才执行该事件。

# 24 利用事件修饰符 once 绑定一次事件
* 代码
```
<div id="app">
    <a href="https://www.baidu.com" @click.prevent.once="alert">点击打开百度</a>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {

        },
        methods: {
            alert() {
                alert('你将打开百度。');
            }
        }
    });
</script>
```
* `@click.prevent.once` => 阻止默认事件、只绑定一次。
* 再次点击即可打开百度。



# 25 键盘修饰符
* 代码
```
<div id="app">
    <!-- @keyup 默认所有按键 -->
    <input type="text" @keyup="consoleLogSth">
    <!-- @keyup.enter 回车键才有用 -->
    <input type="text" @keyup.enter="consoleLogSth">
    <!-- 
        除此之外还有
        space tab 空格 制表符
        up down left right 方向键
        @key.ctrl, shift alt + 任意按键 
        -->
        <!-- 这是 ctrl+s (ASCII中 s = 83) -->
        <input type="text" @keyup.ctrl.83="consoleLogSth">
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
        
        },
        methods: {
            // 按键触发某些方法
            consoleLogSth() {
                console.log('触发了keyup事件');
            }
        },
    });
</script>
```
* `@keyup` , `@keyup.btn` , `@keyup.[ctrl|alt|shift].ASCII`。

# 26 鼠标事件修饰符
* 代码
```
<div id="app">
    <!-- 
        鼠标滚轮 @click.middle
        鼠标右键比较特殊：因为浏览器中右键默认打开浏览器的右键，所以需要：
        @contextmenu调用浏览器右键事件->.prevent阻止右键默认事件->调用我们写的事件
        -->
    <div :style="style" @click.middle="showMiddle" @contextmenu.prevent="showRight"></div>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            style: {
                height: '100px',
                border: '1px solid'
            }
        },
        methods: {
            // 鼠标滚轮
            showMiddle() {
                alert('你点击了鼠标中键');
            },
            // 鼠标右键
            showRight() {
                alert('你点击了鼠标右键');
            }
        }
    });
</script>
```

* `@click` 点击事件
* `@click.middle` 滚轮点击事件
* `@contextmenu` 右键点击事件(默认打开浏览器菜单) -> `@contextmenu.prevent` 阻止默认事件，调用我们自己写的右键事件。

# 27 表单控件处理 v-model
* 代码
```
<div id="app">
    <!-- 使用 v-model 绑定数据 -->
    <label for="id">序号</label> <input type="number" id="id" v-model="info.id"> <br>
    <label for="name">姓名</label> <input type="text" id="name" v-model="info.name"> <br>
    <label for="age">姓名</label> <input type="number" id="age" v-model="info.age"> <br>
    <label for="desc">简介</label> 
    <!-- 文本域同样使用 v-model 绑定 -->
    <textarea id="desc" style="width: 100%" v-model="info.desc"></textarea>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            // 模拟从后台抓取的数据
            info: {
                id: 1,
                name: 'liuhaoyu',
                age: 22,
                desc: '全栈程序员', 
            }
        },
    });
</script>
```

* 使用 `<... v-model="数据">` 绑定数据。

# 28 表单处理 checkbox
* 代码
```
<div id="app">
    <!-- 开关：不指定value属性，v-model绑定一个Boolean -->
    <input type="checkbox" v-model="confirmed"> 确定？ {{ confirmed }}

    <div>
        <!-- 多选框：指定value属性，v-model绑定一个Array -->
        <input type="checkbox" v-model="categories" value="food"> 美食
        <input type="checkbox" v-model="categories" value="heal"> 健康
        <input type="checkbox" v-model="categories" value="trav"> 旅行
        <br>
        {{categories}}
    </div>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            confirmed: false,
            // 如果是多选，要求定义的数据必须是一个数组
            categories: [],
        },
    });
</script>
```

* 不指定 **value** 直接使用 `v-model` 绑定一个布尔值，则表示该checkbox是一个“开关switch”。
* 指定 **value** 并且绑定一个数组，则表示多个checkbox组成的一个多选框。

* 在 chrome 中使用官方开发调试工具[vue DevTools](https://github.com/vuejs/vue-devtools)
    * 能翻墙，直接用谷歌浏览器的应用商店下载
    ---------------------------------------
    * 不能翻墙则需要 `git clone https://github.com/vuejs/vue-devtools.git`
    * 然后进入克隆后的项目目录，执行命令`cnpm install`
    * 然后打开项目目录下的 /shells/chrome/manifest.json ，编辑
    ```
    # false 改为 true
    "persistent": false
    ```
    * 然后编译 `npm run build`
    * 然后打开[谷歌浏览器扩展管理](chrome://extensions/) -> 选择“加载已解压的扩展程序” -> 选择 /shells/chrome 文件夹。

# 29 表单处理 radio
* 代码
```
<div id="app">
    <input type="radio" value="male" v-model="sex"> 男 <br>
    <input type="radio" value="female" v-model="sex"> 女 <br>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            sex: 'male',
        },
    });
</script>
```

* 指定 **value** ， `v-model` 绑定一个具体的值即可。

# 30 表单处理 select
* 代码
```
<div id="app">
    <!-- 这里使用 v-model 绑定数据 -->
    <select v-model="selectedCategory">
        <!-- 默认数据 -->
        <option value="">请选择</option>
        <!-- 这里使用 v-for 循环， :value="绑定属性" -->
        <option v-for="category of categories" :value="category.id"> {{ category.title }} </option>
    </select>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            categories: [
                {id: 1, title: '选项1'},
                {id: 2, title: '选项2'},
                {id: 3, title: '选项3'},
                {id: 4, title: '选项4'},
            ],
            selectedCategory: "",
        },
    });
</script>
```

* 在 select 标签上绑定 v-model
* 在 option 标签上绑定绑定属性value `:value`

# 31 表单修饰符
* 代码
```
<div id="app">
    <!-- v-model.number 强制转换为 number -->
    <!-- 无论input.type是什么，表单里输入的数据，都一定是string类型的 -->
    <input type="number" v-model="age">
    <!-- 如果我非要要求数据类型是整形，在 v-model 绑定时添加 .number -->
    <input type="number" v-model.number="age">

    <!-- v.model.trim 忽略前后空格 -->
    <input type="text" v-model.trim="title">

    <!-- v.model.lay 懒更新，当输入框失焦时再更新值 -->
    <input type="text" v-model.lazy="content">
    <p><small>当前content值为：</small> {{ content }} </p>
</div>

<script>
    var app = new Vue({
        el: '#app',
        data: {
            age: 100,
            title: 'Hello World',
            content: '',
        },
        watch: {
            // 监听 age 变化
            age(newVal, oldVal) {
                console.log(typeof(newVal));
            },
            title(newVal, oldVal) {
                console.log(newVal.length);
            }
        },
    });
</script>
```

* 强制将输入内容转换为整数 `v-model.number`
* 强制删除输入内容前后的空格 `v-model.trim`
* 懒更新，当输入框失焦时才更新绑定的数据的值 `v-model.lazy` (常用于数据验证等，等用户输入完，再更新该数据的值，再执行验证)。 
