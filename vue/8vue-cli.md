# 组件
* 组件都放在 /src/components/ 下
* 组件建议使用大写字母开头，驼峰命名法，后缀名为 .vue
* 一个常见的组件定义如下
```
<!-- 模板 -->
<template>
  <div>
  </div>
</template>

<!-- 业务逻辑 -->
<script>
// 暴露组件
export default {
  name: 'HomePage', // 这里的 name 建议和组件文件名一样 
}
</script>

<!-- 样式 scoped 告诉 Vue 这里面的 css 代码只对该组件有效 -->
<style scoped>
</style>
```

# 在根组件上手动挂载其他组件
1. 引用组件，在 script 中 `import 暴露的名称 from './components/组件文件的名称.vue';`
2. 注册组件，在 script 中 `components: {  //填写要注册的组件名称  }`
3. 在模板上使用标签的形式挂载 `<组件名称></组件名称>`


> 所有组件的模板都是必须有一个根元素把其他内容包起来。（说白了就是要个最大的 div）

# 简单解释生命周期函数
* 前面的代码中用过 `mounted()` 函数，是指组件被挂载后自动调用的生命周期函数，是生命周期函数之一，初次之外，还有 `beforeMount()` , `beforeCreated`, `created()` 等很多生命周期函数，都是组件在实例化和运行过程中某一阶段（某个时间点）会自动调用的函数：
```
// 一些生命周期函数
beforeCreate() {
    console.log('开始创建组件');
},
created() {
    console.log('组件创建成功');
},
beforeMount() {
    console.log('准备挂载组件');
},
mounted() {
    console.log('组件挂载完毕');
},
beforeDestroy() {
    console.log('准备卸载组件'); // 这个 生命钩子 可以用于保存用户临时输入一些信息却又要切换页面时的数据
},
destroyed() {
    console.log('组件卸载完毕');
}
```
* 可以使用 `v-if="布尔值"` 改变该布尔值来实现挂载/卸载组件。
```
# 模板
<HomePage  v-if="flag"></HomePage>
<input type="checkbox" v-model="flag"> 挂载/销毁组件

# 脚本里面写一个 flag 属性为布尔值即可。
```
