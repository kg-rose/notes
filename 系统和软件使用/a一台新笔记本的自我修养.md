> 购买了一台电脑，我需要重新配置整个机器。

1. 安装 [腾讯电脑管家](https://guanjia.qq.com/main.html)
2. 通过腾讯电脑管家安装 Chrome 浏览器，并且通过 Chrome 的引导，设置其为默认浏览器，并且对其进行配置（开启主页按钮和书签栏，设置主页和起始页，设置默认搜索引擎等）
3. 安装主力编辑器 [VS code](https://code.visualstudio.com/docs/?dv=win) 并且装插件：
    * 简体中文汉化（打开自动提示）
    * php 语言调试和支持等（自动提示）
    * laravel 框架的输入提示等（在插件中搜索 laravel）
    * ...（ 为什么不用 SubLime 了？因为插件很难找，而 VS code 在你打开某些新语言文本的时候，会自动推荐你插件 ）
4. 安装了 [Git for Windows](https://gitforwindows.org/)，并且配置 SSH
    * 在本地生成 ssh 密钥 `ssh-keygen -t rsa` (注意这个只能用 Git Bash 命令行工具打开)
    * 在本地使用编辑器打开 **~/.ssh/id_rsb.pub** 里面就是本地密钥的具体值
    * 在 GitHub 中打开个人资料 -> 打开 [SSH and GPG keys](https://github.com/settings/keys) -> 增加一个ssh密钥 [New SSH Key](https://github.com/settings/ssh/new)
5. 安装了 [Laragon](https://laragon.org/download/) => 一款php集成开发环境，并配置环境变量
    * 需要添加 php, composer 到环境变量中：
    * 右键我的电脑，属性，左侧高级系统设置，弹出窗口右下角的环境变量，配置系统变量中的Path。将 php, composer 的文件夹地址放进去（右键，属性，安全，可以复制地址），在命令行中(机器默认是 PowerShell )输入 `composer -v` 测试，如果失败则使用 cmd (【Win】 + 【R】 => 输入 cmd 打开)，然后输入 `path` 命令查看环境配置是否异常，如果新配置的 Path 出现了一个 "Tel" 标记，则需要手动输入文件夹地址。
6. 安装了 [NodeJS](https://nodejs.org/en/download/)

> 所有跟编程相关的文件我都放在 **c:/code** 中。