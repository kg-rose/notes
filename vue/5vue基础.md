# 32 组件
* 什么是组件： 用我自己的话理解就是 “可以复用的、易于管理的div”。 => 一个页面需要轮播图，轮播图写成一个组件，如果需要复用在另一个页面载入该组件即可。用 Vue 开发的网页就是由不同的组件组成的。（之前都是写无数个div划分区域，现在写无数个组件然后组装成网页）
* 基础代码
```
<div id="app">
    <!-- 定义好全局组件之后， -->
    <test1></test1>
    <test2></test2>
    <test3></test3>
    <test4></test4>
</div>


<script>
    // 我们在这里定义 “全局组件”
    Vue.component('test1', { // Vue.component('标签', { //... 定义 })
        template: '<h1> 这是全局组件的模板test1 </h1>', //模板
    });

    // 为了方便我们还可以定义一个变量存储局部组件然后在根组件中载入 （这个定义也必须声明在前面）
    var test4= {
        template: '<h2> 这是局部组件的模板test4 </h2>',
    }

    // 我们之前一直定义的其实是 “根组件”
    var app = new Vue({
        el: '#app',
        data: {
            
        },
        // 我们还可以定义 “局部组件”
        components: {
            test3: {
                template: '<h2> 这是局部组件的模板test3 </h2>',
            },
            // test4: test4,
            // 可以使用 ES6 语法，由于 key: value 是一样的，直接:
            test4
        }
    });

    // “全局组件” 必须在根组件之前定义
    Vue.component('test2', {
        template: '<h1> 这是全局组件的模板test2 </h1>',
    });
</script>
```

* 定义全局组件 `Vue.component('这里对应载入时在html代码中写的标签名', { //这里面写具体定义 })`
* 全局组件 **必须** 在根组件前面声明。
* 定义局部组件，写在根组件的 **components** 里面。 `标签名: { //...定义 }`。
* 也可以在根组件前面声明并用变量存储起来，然后直接在 **components** 中载入该变量即可。
* ES6的语法，如果 json 的 键值对， key的名字和value的名字一样，可以直接写 key ，不写 value 。

# 33 组件中定义 data 数据
* 代码
```
 <div id="app">
    <test></test>
</div>

<!-- 在 Vue 中可以使用这样的方式定义模板 -->
<script type="text/x-template" id="myComponent">
    <ul>
        <li v-for="v in news">{{ v.id }} - {{ v.title }}</li>
    </ul>
</script>

<script>
    var myComponent = {
        template: "#myComponent",
        // data: {} //子组件中不能把data定义成属性。会报错：
        // The "data" option should be a function that returns a per-instance value in component definitions.
        data() { //必须这样定义 data() { return {json对象} }
            return {
                news: [
                    {id:1, title:'测试1'},
                    {id:2, title:'测试2'},
                ],
            };
        }
    };
    var app = new Vue({
        el: '#app',
        data: {

        },
        components: {
            // 有个坑： ES6 方式注册子组件不能使用 驼峰 风格命令，Vue会自动转换成全小写。
            test: myComponent,
        }
    });
</script>
```

* 我们是在外面定义的子组件，然后在根组件的 components 中注册。
* 在注册的时候发现了一个问题： 用ES6语法注册时，我将组件名定义为 "myComponent" ，Vue 解析为 "mycomponent"。没办法注册。
* 在子组件中定义data必须使用 `data() { //json对象 }` 这样的语法，而不能直接写 `data: {}`。
* 可以使用 `<script type="text/x-template" id="test"></script>` 来定义模板。然后再子组件定义中使用模板 `template: "#test"`

# 34 父组件给子组件传递参数
* 代码
```
<div id="app">
    <!-- 在标签里面传递 :子组件.props中声明的名字 = "根组件.data中的变量名" -->
    <!-- 如果不写 :属性="value" 的话，会解析为字符串 -->
    <students :students="students" flag1="false" :flag2="true"></students>    
</div>

<!-- 模板 -->
<script type="text/x-template" id="students">
    <ul>
        <li v-for="student in students"> {{ student.id }} - {{ student.name }} </li>

        <span v-if="flag1"> 这其实是字符串 {{ flag1 }} </span> <!-- 这里之所以flag = "false" 会显示是因为 字符串"false" = 布尔true -->
        <span v-if="flag2"> ：这才是布尔值 {{ flag2 }} </span> <!-- 因此这里如果传递 flag2 = false 则不会显示 -->
    </ul>
</script>

<script>
    // 子组件
    var students= {
        template: "#students",
        // 在接收时需要在 props 属性中声明
        props: ['students', 'flag1', 'flag2'],
    };
    
    // 根组件
    var app = new Vue({
        el: '#app',
        // 定义父组件的数据
        data: {
            students: [
                {id: 1, name: 'liuihaoyu'},
                {id: 2, name: 'lidaye'},
                {id: 3, name: 'linainai'},
            ],
        },
        // 注册子组件
        components: {
            students,
        }
    });
</script>
```

* 整个过程：（根组件）父组件.data中定义数据，然后在html代码中 **子组件的标签上** 使用 `:xxx="定义的变量"` 传递数据。
* 子组件需要声明 `props = ['接受的数据xxx', '接受的数据yyy', '接受的数据zzz']`
* 如果在html中， 子组件的标签上这么传递数据 `xxx="value"` ，这样 子组件.props 接受的其实是字符串，而不是父组件data中声明的值。 

# 35 props 数据验证
* 代码
```
<div id="app">
    <!-- 在标签里面传递 :子组件.props中声明的名字 = "根组件.data中的变量名" -->
    <!-- 如果不写 :属性="value" 的话，会解析为字符串 -->
    <students></students>    
</div>

<!-- 模板 -->
<script type="text/x-template" id="students">
    <ul>
        <li v-for="student in students"> {{ student.id }} - {{ student.name }} </li>
    </ul>
</script>

<script>
    // 子组件
    var students= {
        template: "#students",
        // 在 props 中进行数据验证
        props: { // 1、要求props定义成一个对象
            students: { // 2、用 【属性名: { //...相关配置 }】 进行数据接收
                type: [Array, Object], // 指定数据类型
                // required: true, // 设置数据是否必填
                default() { // 设置默认值
                    return [
                        {id: 1, name: 'liuhaoyu'},
                    ];
                },  
                validator(value) { // 设置数据验证规则
                    return value[0].id > 0;
                }
            },
        }
    };
    
    // 根组件
    var app = new Vue({
        el: '#app',
        // 定义父组件的数据
        data: {
        },
        // 注册子组件
        components: {
            students,
        }
    });
</script>
```
* 如果想对 子组件.props 属性声明并接收的数据进行数据校验的话，需要将 props 写成对象。
* 之后接受数据时用 `变量名: { //...相关定义 }` 进行接收。
* 在相关定义里，我们可以：
    * `type: 指定数据类型，可以使用数组的形式指定多种数据类型`,
    * `required: true | false` 指定是否必须传入该参数
    * `default() {  //return一个默认值  }` 设置默认值
    * `validator(value) { //执行相关验证为true通过 }` 进行数据验证，为真则通过。 这里发现不通过也会显示数据，但是会在浏览器控制台提醒错误。记得参数里要写一个 value 对应传递进来的数据。

# 36 通过子组件呼叫父组件实现简单的购物车
* 代码
```
<div id="app">
    <cart :goods="goods" @refresh="totalPrice"></cart>
    <span>
        总计：￥ {{ total }} 元
    </span>
</div>

<!-- 模板 -->
<script type="text/x-template" id="cart">
    <table border="1">
        <thead>
            <tr>
                <th>商品名称</th>
                <th>价格</th>
                <th>数量</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="good in goods">
                <td>{{ good.name }}</td>
                <td>{{ good.price }}</td>
                <td>
                    <input type="text" v-model="good.number" @blur="refresh">
                </td>
            </tr>
        </tbody>
    </table>
</script>

<script>
    //子组件
    var cart = {
        template: "#cart",
        props: {
            goods: {
                type: Array,
            }
        },
        methods: {
            refresh() {
                this.$emit('refresh')
            }
        },
    }
    
    // 根组件
    var app = new Vue({
        el: '#app',
        data: {
            // 定义商品信息
            goods: [
                {name: "macbookPro 2018", price: "20000", number:1},
                {name: "iphone 8", price: "6888", number:1},
                {name: "iphone8 Plus", price: "8888", number:1},
            ],
            // 总价初始化
            total: 0,
        },
        // 注册子组件
        components: {
            cart,
        },
        methods: {
            // 计算总价
            totalPrice() {
                this.total = 0;
                this.goods.forEach((good) => {
                    this.total += good.price * good.number;
                });
            }
        },
        // 挂载钩子程序
        mounted() { // 类似于 初始化程序
            this.totalPrice(); // 直接调用计算总价的方法
        },
    });
</script>
```

* 在父组件挂载钩子程序 `mounted() { //调用里面的方法，记得 **this.**function() }` 。 “相当于初始化方法” => 当组件载入完的时候就执行。
* 子组件在更改商品数量时，更新父组件中的总价:
    * 第1步： html代码中，在子组件的标签上绑定事件 refresh `@refresh="totalPrice"` 即子组件调用 refresh() 方法时，就调用的是父组件里的 计算总价 方法。
    * 第2步： 给绑定了商品数量的 input 添加一个失焦事件 `@blur` 当它改变时，调用子组件的 refresh() 方法
    * 第3步： 子组件的 refresh() 方法被调用时，使用 `$this.$emit('refresh')` 调用 第1步 上绑定的自定义事件 refresh。
    * 即： 子组件标签上的 `@refresh` => 事件， `@blur="refresh"` => 子组件的 methods 中定义的方法。 是 refresh() 方法，通过 `this.$emit('事件名')` 呼叫了事件。

# 37 更优写法实现36购物车功能
* 代码
```
<div id="app">
    <!-- 这里使用 :绑定属性.sync同步数据="父组件的goods" => 达到了当子组件的goods发生变化时，父组件的goods也会变化 -->
    <cart :goods.sync="goods"></cart>
    <span>
        总计：￥ {{ totalPrice }} 元
    </span>
</div>

<!-- 模板 -->
<script type="text/x-template" id="cart">
    <table border="1">
        <thead>
            <tr>
                <th>商品名称</th>
                <th>价格</th>
                <th>数量</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="good in goods">
                <td>{{ good.name }}</td>
                <td>{{ good.price }}</td>
                <td>
                    <input type="text" v-model="good.number">
                </td>
            </tr>
        </tbody>
    </table>
</script>

<script>
    //子组件
    var cart = {
        template: "#cart",
        props: {
            goods: {
                type: Array,
            }
        },
    }
    
    // 根组件
    var app = new Vue({
        el: '#app',
        data: {
            // 定义商品信息
            goods: [
                {name: "macbookPro 2018", price: "20000", number:1},
                {name: "iphone 8", price: "6888", number:1},
                {name: "iphone8 Plus", price: "8888", number:1},
            ],
            // 总价初始化
            total: 0,
        },
        // 注册子组件
        components: {
            cart,
        },
        // 计算总价
        computed: {
            totalPrice() {
                var sum = 0;
                this.goods.forEach((good) => {
                    sum += good.price * good.number;
                });
                return sum;
            }
        }
    });
</script>
```

* 不需要在子组件标签上绑定自定义事件了，只需要 `:goods.sync="goods"` 来实现：当子组件的变量 goods 发生变化时， 父组件的goods也同步(sync)发生变化。
* 同时不需要再在子、父组件中定义 methods 了。 子组件的 input 中也不需要 `@blur` 绑定失焦事件了。
* 在父组件中定义 computed 计算属性 `computed: { 属性() { //...进行计算 return 结果 } }` 
* 在 html 代码，总价中载入 计算属性 中定义的 **totalPrice** 即可。

# 38 子组件 slot 内容分发
* 代码
```
<div id="app">
    <test>
        <h1 slot="title">这是标题</h1>
        <p slot="content">这是内容</p>
        <test1 slot="myinput" type="email" title="邮箱" placeholder="username@example.com"></test1>
        <test1 slot="myinput" type="text" title="用户名" placeholder="yourNickName"></test1>
        <test1 slot="myinput" type="password" title="密码" placeholder="yourBirthday"></test1>
    </test>
</div>

<!-- 子组件模板 test -->
<script type="text/x-template" id="test">
    <div>
        <slot name="title"></slot>
        <slot name="content"></slot>
        <slot name="myinput"></slot>
    </div>
</script>

<!-- 子组件模板 test1 -->
<script type="text/x-template" id="test1">
    <div>
        <span>{{ title }}</span>
        <input :type="type" :placeholder="placeholder">
    </div>
</script>

<script>
    // 子组件 test
    var test = {
        template: "#test",
    };
    // 子组件 test1
    var test1 = {
        template: "#test1",
        props: ['type', 'title', 'placeholder']
    }
    // 根组件
    var app = new Vue({
        el: '#app',
        data: {
        },
        components: {
            test,
            test1,
        }
    });
</script>
```

* 在子组件中定义 **slot** `<slot name="取个名字">`
* 在根组件中填充 **slot** `<任意标签 slot="要填充的slot名字">填充的内容</slot>`
* 利用其他组件在根组件中填充某个组件 `<其他组件 slot="要填充的slot名字" 话可以传递属性...></其他组件>`
* 可以反复填充一个slot。

# 39 scope 的使用
* 代码
```
<div id="app">
    <users :users="users" scope="v">
        <!-- 这里 **必须** 使用 template 标签 -->
        <!-- 并且使用 scope="任意变量名" 接收 slot抛出的数据 -->
        <template scope="data">
            <li>
                {{ data.user.id }} - {{ data.user.name }}
            </li>
        </template>
    </users>
</div>

<script type="text/x-template" id="users">
    <ul>
        <!-- <li v-for="user in users"> {{ user.id }} - {{ user.name }}</li> -->
        <!-- 这里用slot定义，但是在后面用 :user="user" 抛出数据 -->
        <slot v-for="user in users" :user="user"></slot>
    </ul>
</script>

<script>
    var users = {
        template: "#users",
        props: {
            users: {
                type: Array,
            },
        }
    };
    var app = new Vue({
        el: '#app',
        data: {
            users: [
                {id: 1, name: 'liuhaoyu'},
                {id: 2, name: 'lidaye'},
                {id: 3, name: 'linainai'},
            ]
        },
        components: {
            users,
        }
    });
</script>
```

* 具体过程： 父组件中定义 data.users 数据，然后在子组件标签上传递给子组件。
* 然后在子组件循环时，使用 `<slot>` 来遍历数据
* 并且在遍历时，抛出数据 `<slot v-for="data in datas" :data="data"></slot>`
* 在父组件中，载入子组件的标签内
```
<子组件>
    <template scope="任意变量名这里暂时用data">
        <合适的标签>
            {{ data.data.属性 }} =>第一个data对应scope里的，第二个data对应子组件中使用 :data 抛出的。
        </合适的标签>
    </template>
</子组件>
``` 
* 总体来说，这个功能不常用。整个逻辑更像是在 html 代码中遍历子组件中的 slot。

# 40 动态组件
* 代码
```
<div id="app">
    <input type="radio" v-model="formType" value="myInput"> 使用 input
    <input type="radio" v-model="formType" value="myTextarea"> 使用 textarea

    <div :is="formType"></div>
</div>

<script>
    var myInput = {
        template: "<div> <input> </div>",
    };
    var myTextarea = {
        template: "<div> <textarea></textarea> </div>",
    }
    var app = new Vue({
        el: '#app',
        data: {
            formType: "myInput",
        },
        components: {
            myInput,
            myTextarea
        }
    });
</script>
```

* 我们实现了一个功能：通过勾选不同的单选框，显示不同的表单项（input | textarea）.
* 具体实现是通过一个 div 作为载体, 绑定 id 属性 `<div :id="formType">` .
* 这里的 formType 对应的是我们定义并在根组件的 components 属性中声明的根组件.
* 补充一点疑问: 子组件在 components 声明叫做 **myInput**, 那么在 html 中载入组件应该使用 `<my-input></my-input>` 载入,而不是 `<myInput></myInput>` => 因为这样浏览器会解析为全小写的变量名.(之前遇到过这个问题)
