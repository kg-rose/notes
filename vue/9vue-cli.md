# 事件调用方法时传递参数
* 代码
```
# template
<!-- methods 传递参数的两种方法 -->
<button v-on:click="showParam1('我是参数1')">点我显示参数1</button>
<button data-param="我是参数2" @click="showParam2($event)">点我显示参数2</button>

# script
...
methods: {
    // 直接传参
    showParam1(str) {
        alert(str);
    },
    // 传递过来一个 事件对象 通过该对象获取参数
    showParam2(e) {
        console.log(e.srcElement.dataset.param);
    }
}
```

* `v-on:事件="调用方法(参数列表)"` 可以简写为 `@事件="方法(参数)"`
* 第一种方法是直接传递参数。
* 第二种方法是传递 `$event` => 即触发事件的对象作为参数，并且在标签上使用 `data-参数名` 来配置参数。
* 然后在方法参数列表中接收 $event ，然后通过 `接收的event.srcElement.dataset.参数名` 来实现读取标签上配置的参数。

> 第二种方法用于无法直接传参的情况。

# 创建新的初始化文件
* 使用 `vue create projectName` vue-cli 创建新项目时，会让你选择一种初始化版本：
    * **default** => 带 babel, esLint
    * **Manually select features** => 自己选包
* 选择 Manully => 【空格】选取创建项目时就自动带上的包，选好后【回车】，然后可以选择 **Indedicated config files** 输入一个名字，就可以再下次使用 vue-cli 创建项目时，选择通过这样的方式保存的预置配置了。

# 构建一个简单的“代办事项”
* 增加、删除、遍历
```
# template

<!-- 文本框 -->
<input type="text" v-model="todo">
<!-- 添加按钮 -->
<button @click="add()">增加</button>
<hr>
<!-- 遍历数据 -->
<ul class="list">
    <!-- 使用 v-for="(单个对象, 该对象在集合中的下标)" 在循环中利用下标实现删除 -->
    <li v-for="(thing, key) of things" :key="thing.id"> {{ thing.id }} - {{ thing.todo }} <button @click="remove(key)">删除</button> </li>
</ul>

# script
export default {
  name: 'app',
  data() {
    return {
      todo: '', //文本框
      // 这是代办事项集合
      things: [
        {id: 1, todo: '请输入代办事项'},
      ],
    }
  },
  methods: {
    // 添加
    add() {
      // 先获得id
      var id = this.things.length;
      // 然后获取文本框能容
      var todo = this.todo;
      // 组装成和 things 中的每一项一样的格式（ES6语法, key: value 标识符是一样的时候，可以只写其中一个）
      var newThing = {id, todo};
      // 最后使用 push() 将新数据放进 this.things 中。
      this.things.push(newThing);
    },

    // 删除
    remove(key) {
      // 使用 splice(开始下标, 往后删除几个) 来删除数据 
      this.things.splice(key, 1);
    },
  }
}
```
* 增加删除都使用 JS 提供的数组操作， `push(在数组末尾添加的新元素值)` & `splice(从哪里开始删除的下标, 删除个数)`
* 在模板上遍历显示对象集合（数组）时，可以通过 `v-for="(obj, index) of objs"` 中的 index 来获取下标。
---------------------------------------------
* 完成后勾选功能的实现
```
# 修改 template
<ul class="list">
  <h2>代办</h2>
  <li v-for="(thing, key) of things" :key="thing.id" v-if="!thing.status">
      <input type="checkbox" v-model="thing.status">
      {{ thing.id }} - {{ thing.todo }} 
      <button @click="remove(key)">删除</button>
  </li>
  <hr>
  <h2>完成</h2>
  <li v-for="(thing, key) of things" :key="thing.id" v-if="thing.status" class="active">
      <input type="checkbox" v-model="thing.status">
      {{ thing.id }} - {{ thing.todo }} 
      <button @click="remove(key)">删除</button>
  </li>
</ul>

# 数据定义改一下
things: [
  {id: 0, todo: '请输入代办事项', status: false},
],  
```
* 上面实现了修改 `thing.status` 的功能：使用 `<input type="checkbox" v-model="thing.status">` 绑定当前被遍历事件的状态值，当勾选时，该值为 true。
* 使用 `v-if` 来控制页面上元素的显示和隐藏。
-----------------------------------------------
* 使用 **localStorage 实现存储本地事件**
  1. 新建 /src/module/storage.js
  ```
  /**
  * localStorage 封装
  */

  // 设置一个带有 set() 和 get() 方法的对象
  var storage = {
      // 设置
      set(key, value) {
          localStorage.setItem(key, JSON.stringify(value));
      },

      // 读取
      get(key) {
          return JSON.parse(localStorage.getItem(key));
      },
  }

  // 并包路这个对象
  export default storage;
  ```
  > `JSON.stringify(对象)` => 对象转JSON字符串， `JSON.parse(字符串)` => 字符串转对象。

  2. 在 /src/App.vue 根组件中引用上面代码定义和暴露的模块
  ```
  // 引用对象
  import storage from "./module/storage";
  // console.log(storage);
  ```
  3. 在 添加，删除，清空三个方法中添加这样一句代码 `storage.set('things', this.things);` => 调用引用的 storage 对象的set() 方法。
  4. 使用 **生命周期函数 mounted(): 当组件被挂载完成后自动调用**，当页面刷新时读取存储在 localStorage 中的代办事项数据
  ```
  // 初始化函数
  mounted() {
    var things = storage.get('things');
    if(things) {
      this.things = things;
    }
  }
  ```
  5. 解决一个无法正确读取状态的问题：因为我们的“开关” `<input type="checkbox" v-model="thing.status">` 在改变时没有进行 localstorage 的存储，所以需要在标签上添加一个被修改时触发的事件 `@chage="saveStatus()"` ，然后在 methods 里定义一个 saveStatus() 函数，调用 `storage.set('things', this.things);` 存储即可。

> 上面的知识用了 nodeJS 提供的模块化思想：定义并且使用 export 暴露某个对象。然后在其他文件使用 import 引用一些被暴露的对象。实现对象分类封装。
