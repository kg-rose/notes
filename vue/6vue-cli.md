# 简介和对比
* Vue Angular React 都是基于 “单页面组件化” 思想的前端框架。

# 安装脚手架
* 必须安装[nodeJS](http://nodejs.cn/download/)（其中自带npm）
* 安装cnpm以提升npm下载速度 => [node包中国淘宝镜像](http://npm.taobao.org/) `npm install -g cnpm --registry=https://registry.npm.taobao.org`
* 使用cnpm安装cli脚手架工具(命令行工具) `cnpm install -g @vue/cli`

# 创建和启动项目
* 创建项目 `vue create prjectName`
* 进入创建的项目目录，执行命令启动项目 `npm run serve` 访问 localhost:8080(默认) 即可看到构建好的项目
* 打包构建项目 `npm run bulid` 。
> 命令都是是从 **./package.json** 文件中的 "scripts" 属性中找到的。

# 基本目录结构
* node_modules/ => node包目录
* public/ => 公开目录，里面有入口文件index.html
* src/ => 源代码目录（我们主要编写的文件）
    * assets/ => 静态资源存放目录
    * components/ => 组件目录
    * App.vue => 根组件
    * main.js => 实例化 Vue 挂载组件。
* package.json => 项目管理文件（依赖声明、脚本声明等等...）
* ...
> 只列出了重点

# 了解第一个组件
* App.vue
```
<!-- 模板 -->
<template>
  <!-- 所有组件元素必须被一个根元素包含起来 -->
  <div id="app">
    <!-- 这里导入了Vue的Logo -->
    <img src="./assets/logo.png">
    <!-- 这里导入了组件HelloWorld -->
    <HelloWorld msg="Welcome to Your Vue.js App"/>
  </div>
</template>

<!-- js -->
<script>
// 这里引用了组件
import HelloWorld from './components/HelloWorld.vue'

export default {
  name: 'app',
  // 这里声明了组件
  components: {
    HelloWorld
  }
}
</script>

<!-- css -->
<style>
 ...
</style>
```
* 可以看到，官方提供的组件是3个部分组成：`<template>` 静态模板， `<script>` js对组件的定义和声明， `<style>` 编辑模板中元素的css样式。
* 在 `<script>` 中，先引用了组件 `/src/components/HelloWorld.vue` ，然后在暴露模块时声明了属性 components 导入组件 HelloWorld.vue `components: {HelloWorld}` 。
* 最后在 `<template>` 中，使用 `<HelloWorld>` 标签把组件展示在页面上，并且传递了一个叫 `msg` 的变量。
* 再看 /src/components/HelloWorld.vue 组件
```
<template>
  <div class="hello">
    <!-- 在模板上显示接收的数据 -->
    <h1>{{ msg }}</h1>
  </div>
</template>

<script>
export default {
  name: 'HelloWorld',
  // 声明接收的数据
  props: {
    // 接收叫 msg 的变量，类型为 String 字符串。
    msg: String
  }
}
</script>

<!-- 在style标签上添加 "scoped" 属性来声明以下css只对当前组件有效 -->
<style scoped>
 ...
</style>
```

* Vue 根组件向 HelloWorld 组件传递参数后，在 HelloWorld 组件中必须使用 `props: {msg: String}` 来接收。
* 限制 `<style>` 样式只对该组件有效，需要在添加 **scoped** 属性。
* 模板插值 `{{ 使用两个花括号 }}`

# 根组件定义数据，子组件显示
* App.vue -> `<script>` 中定义3种数据
```
<script>
import HelloWorld from './components/HelloWorld.vue'

export default {
  name: 'app',
  components: {
    HelloWorld
  },

  // 这里定义数据
  data() {
    return {
      // 字符串
      msg: "测试",

      // 对象
      person: {
        name: "张三",
        age: 18
      },

      // 数组（对象集合）
      students: [
        {id: 1, name: "张三", age:18},
        {id: 2, name: "李四", age:18},
        {id: 3, name: "王五", age:18},
      ]
    }
  }
}
</script>
```

* App.vue -> `<template>` 中，在子组件标签 `<HelloWorld>` 上传递数据 `<HelloWorld v-bind:msg="msg" :person="person" :students="students" />` 
> 这里 `v-bind:` 可以 简写为 `:`

* 在 ./components/HelloWorld.vue -> `<script>` 中接收数据
```
<script>
export default {
  name: 'HelloWorld',
  props: {
    msg: String,
    person: Object,
    students: Array,
  }
}
</script>
```

* HelloWorld.vue -> `<template>` 中遍历数据
```
<!-- 任何组件必须有一个根元素包起来（不能有两个最大的元素，可以把这个div理解为普通 html 中的 body 标签） -->
<div class="hello">
  <!-- 展示字符串 -->
  <h1>{{ msg }}</h1>
  
  <!-- 展示对象 -->
  <span>姓名 - {{ person.name }}</span>
  <br>
  <span>年龄 - {{ person.age }}</span>
  
  <!-- 遍历数组 -->
  <ul>
    <!-- 在遍历数组时，必须告诉 Vue 这个数组的“主键” -->
    <li v-for="student of students" :key="student.id">
      {{ student.id }} - {{ student.name }} - {{ student.age }}
    </li>
  </ul>
</div>
```
> 根组件 script 中使用 `data() { return { //定义数据 } }` 定义数据 -> 根组件模板中，使用`:key="value"`传递数据 -> 子组件 script 中使用 `props` 接收数据 -> 子组件使用 `{{ string }}` , `{{ object.prop }}`, `v-for="data of datas"` 显示数据。
