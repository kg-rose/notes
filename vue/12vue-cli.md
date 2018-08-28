# 什么是vuex
> vuex 是专门为 Vue 开发的状态管理模式，它采用集中式存储管理应用程序的所有状态。

* 说白了就是一个实现大量组件之间数据共享的一个扩展包。

> 建议 vuex 在大型项目中投入使用，而在小型项目中可以使用 html5 提供的 localStorage/sessionStorage.

# 安装和投入使用
1. 使用 npm 命令安装 `npm install vuex --save`
2. 使用 vue create 命令创建项目时勾选上 vuex
------------------------------------------
* 如果使用 `vue create projectName` 创建项目的话，会生成一个 /src/store.js 文件：
```
import Vue from 'vue' //引用核心库
import Vuex from 'vuex' //引用 vuex

Vue.use(Vuex) //使用 vuex

// 暴露模块
export default new Vuex.Store({
  // 存储的数据
  state: {

  },
  // 方法
  mutations: {

  },
  // 行为（调用方法）
  actions: {

  }
})
```
* 同时在 main.js 中： `import store from './store'` 引用了 store.js。
--------------------
* 使用 vuex 实现一个不同页面的计数器，每当路由挂载其他组件时，计数器+1。
    * store.js 中定义技术器数据和增加方法
    ```
    // 存储的数据
    state: {
        count: 0, //计数
    },
    // 方法
    mutations: {
        // 增加方法
        increment (state) {
            state.count++; 
        },
    },
    ```
    * 在其他组件中引用 store.js 并且调用该方法
    ```
    import  store  from "../store.js";
     
    ...

     mounted() {
        store.commit('increment');
        console.log(store.state.count);
    }
    ```

    > 相对路径引用 store：`import  store  from "../store.js";` 读取数据： `store.state.数据名称`， 调用方法 `store.commit('方法名称')`
--------------------------------------------
* actions 的使用

> 在 actions 中可以 **异步** 调用 mutations 中写好的方法
```
# store.js 
// 行为：用于调用 mutations 里面写好的方法，同时调用 actions 时是异步的操作
actions: {
    increment (context) { // 这里需要传入一个 context 作为实例
        context.commit('increment'); // 使用 context.commit 调用 mutations 中写好的 increment 方法
    },
},

# 在其他组件中
import store from "../store.js"; // 相对路径引用store
store.dispatch('increment'); // dispatch 调用 actions 中定义的方法
```

* getters 的使用

> 当监控的 state 发生改变时自动调用，类似组件中的 computed 属性

```
// 计算，类似 computed : 改变 state 中的数据时，会触发里面的方法，获取新的值
getters: {
    computeCount (state) {
        return state.count * 2;
    }
}
```

# 通过 vuex 实现数据持久化
> 假设我们现在通过 vue-resourece 请求某 api 获得了一组数据，我希望在我切换页面时该数据依然存在，可以通过存储在 vuex 中来实现这一功能
* state 创建存储数据的变量， mutations 中写保存数据的方法
```
state: {
    list: [],
}

mutations: {
    addList (data) {
      state.list.push(data);
    }
}
```
* 在其他组件中，请求到数据之后，假设叫data
```
# 引用 store ...
# 在某个方法中加上这一句代码
store.commit('addlist', data); //将 data 存放在 store.state.list 中即可

# 为了避免反复请求数据，我们可以在组件中的某些方法判断 store.state.list.length 来判断我们是否请求过数据
```

> commit 调用 mutations 中的方法时，第一个参数传 ('方法名', 第二个参数传数据)
