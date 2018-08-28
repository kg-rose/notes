# 什么是 Git

> Git是目前世界上最先进的 **分布式版本控制系统**

* 打个比方，Git很像单机游戏中的存档功能：我们打掉了一个Boss后从NPC领取奖励（完成了一次软件开发中某功能的实现），但是我们觉得领取的奖励很不好（开发完成的功能需要改良重写），我们就读取打Boss前的档案（回到之前的版本）。

# 开始使用 Git
* [git](https://git-scm.com/)官网，先下载&安装。
* 在使用前，需要进行配置，建议在本地个人开发环境下，进行全局配置
```
git config --global user.email "邮箱"
git config --global user.name "用户名
```
* 所有全局配置都位于 **~/.gitconfig**
* 你也可以单独配置某个项目，在有 .git/ 目录的项目下，使用
```
git config user.name "本项目用户名"
```
> 如果你不给项目单独配置 user.name & user.email 的话，它就使用全局的配置。

* 项目单独配置信息文件位于 **项目/.git/config**

# Git 在本地的基础使用
* “给游戏增加存档功能” （让本地项目支持被 Git 管理，物理上则是添加了 .git/ 目录）
```
git init #让git初始化当前路径，使当前路径可以被git管理。
git init ~/test #让 家目录/test 可以被git管理。
```
* “干掉Boss后搜刮战利品” （添加文件到“待提交列表”）
```
git add . #添加所有 新建、 被编辑、 被重命名的文件到 “待提交列表”
git add <文件名> #添加某个文件到“待提交列表”
```
* “我不想拾取灰色的物品” （编辑 .gitignore 忽略某些文件）。

> 每一个目录下都可以有 .gitignore 文件，这个隐藏的文件告诉 git 在 git add 时要忽略当前目录下的哪些文件。

```
*.txt #告诉 git 在 git add 时忽略掉后缀名为 .txt 的文件
!a.txt #但不要忽略 a.txt
vendor #忽略 ./vendor/ 目录下的所有文件
```
* “查看游戏进度” （看看项目的文件/文件夹变化）
```
git status
# 文件通常有以下几种状态
    # 未被追踪：新建的文件或文件夹
    # 被修改过：已经记录过的文件后被编辑过
    # 被删除了：被删除了的文件
    # 被重命名：被修改过名字的文件
```
* “存档” （提交）
```
git commit -m "这是一个必要的描述，主要告诉从上次提交前到本次提交你都对项目干了哪些重要的事情，比如 '编辑了 IndexController@index' "
```
* “给里程碑时刻的存档写上特别的名字” （使用 tag 命令打上标签）

> 游戏中，我们做掉了守关大Boss，值得纪念，所以我们给存档重命名为“我杀掉了这一关最难的Boss”。在 Git 管理中，我们也可以给软件开发某个阶段的重要更新打上一个标签。通常这个版本一定是可用、稳定的。
```
git tag 标签名 #通常标签名是 v1.0.0 版本的形式
```

* “查看所有存档” （查看提交日志）
```
git log #常规显示所有提交记录
git log -p #显示所有提交记录的同时，显示每次提交对文件和文件夹的具体更改
git log -整数n #显示n条提交记录
git log --oneline #精简显示提交记录
git log --graph #图形化显示提交记录（会用于更直观地显示分支开辟及合并情况）
```
* “读取存档” （回到项目开发中保存的某一状态）
```
# 以下几种方法都可以回滚项目到某种状态
git checkout 哈希值前6位
git checkout 标签名
git checkout 分支名
```
* 一些其他的操作
    * 修改已经提交过文件的文件名 `git mv 已提交文件名 新名称`，此时该文件处于 “被重命名” 状态，确认修改需要再次提交。
    * 删除“存档”中已经提交过的文件 `git rm 文件名` ，注意，这样也会从本地中删除该文件，而使用 `git rm --cached 文件名` 是从“存档”中删除文件（文件会处于未被追踪状态）。
    * 改变当前存档名称 `git commit --amend` 会打开一个最近一次提交配置信息，改变第一行即改变了 `git log` 中显示的名称。
    * `git tag` 可以显示所有标签。

# Git 的分支

### 基础使用
* “开始支线任务” （创建分支）
```
git branch 新分支名称 #此时新分支会 “继承主线存档”
git checkout -b 新分支名称 #新建分支并且切换到新分支
```
* “完成支线任务，回到主线” （合并分支）
```
git merge 其他分支 #此时 当前分支 继承 其他分支 的状态。
```
* 解决分支冲突：在两个不同的分支中，某一个文件被两个分支都编辑了，只有打开该文件，自行解决。
* 查看分支
```
git branch #查看所有本地分支
git branch --merged #查看已经合并过的分支
git branch --no-merged #查看没有合并过的分支
```

* “删除支线记录”  （删除分支）
```
git branch -d 分支名称 #只允许删除已经合并过的分支
git branch -D 分支名称 #强制删除没有被合并过的分支
```

### “暂停任务” （stash的使用）

> 在开发过程中，如果出现：正在a分支上开发，需要离开处理b分支，而a分支上的代码不必要提交，则此时禁止从a分支切换到b分支。

* 使用 stash “暂停当前任务线” => 创建一个 “临时的存档” `git stash`，此时你可以切换到其他分支。
* 查看所有 “临时的存档” `git stash list`
* 回到 “被暂停的任务” 后，读取临时存档 `git stash apply`
* 我们可以用多个 stash “临时存档”
```
git stash #每一次执行命令都添加一个临时存档
git stash list #列出所有临时存档
git stash apply stash@{临时存档编号n} #读取某个临时存档
git stash drop stash@{临时存档编号n} #删除某个临时存档
git stash pop stash@{临时存档编号n} #赌球并删除某个临时存档
```

### 在其他分支上读取另一条分支的最新进度

> 在新建分支时，是会读取当前基础分支的最新状态，作为新分支的初始状态。

* 如果我们在开发时，出现继承的分支已经更新了（则新分支的初始状态已经不是基础分支的最新状态了，基础分支更新了），则使用 `git rebase 基础分支名称` 来读取基础分支的最新状态。

* 如果此时出现冲突，那么准则是在新分支中解决冲突。

# “网络游戏”

> 上面的操作都是在本地进行的，现在我们有一个团队，需要一起进行这款游戏，我们分离出无数个存档，然后每个人去发展自己的线路，击败不同的Boss，最后回到中心存档“master”

### 第一步，我们需要一个用于 “贡献和分享线上存档” 的服务器，最好的是[GitHub](https://github.com/login)！
### 第二步，我们需要一个账号（略）
### 第三步，我们需要连接本地和线上服务器
* 配置 ssh 连接
    * 在本地生成 ssh 密钥 `ssh-keygen -t rsa`
    * 在本地使用编辑器打开 **~/.ssh/id_rsb.pub** 里面就是本地密钥的具体值
    * 在 GitHub 中打开个人资料 -> 打开 [SSH and GPG keys](https://github.com/settings/keys) -> 增加一个ssh密钥 [New SSH Key](https://github.com/settings/ssh/new)
* 使用 ssh ，在本地读取网络存档
```
git clone git@github.com:username/projectName.git #克隆存档
# 基本操作
touch test.md
git add .
git commit -m "增加了 test.md"
git push #上传存档，push 时根据本地 ssh 配置自动连接
```
* 使用 ssh ，以本地存档为中心，传递给存档服务器。
```
# 新建本地库 & 进入本地库
git init test & cd test
# 绑定线上服务器
git remote add orgin git@github.com:username/projectName.git
# ...省略一些基本操作
# 提交
git push -u orgin 分支名称
```

> 我更喜欢：GitHub新建库，本地克隆，然后直接使用 `git push` 推送代码

### 本地新建分支，线上也建一个一样的分支
* `git branch -a` 罗列本地和线上所有的分支
* 如果此时新建分支 `git branch 新分支`，而线上依然是没有的。
* 如果需要将新建的分支也同步到线上，那么在当前分支下使用`git push --set-upstream origin 新分支名称`同步一个新分支到线上。（线上就有 master 和 新分支 两条分支了）
* 删除线上分支 `git push origin --delete 线上拥有的分支的名称`

# 真正的使用
### pull 开发中的分支，做出自己的贡献，然后将代码再pull上去
* 假设线上有 test 分支
```
# 克隆线上项目
git clone ...
# 同步 test 分支
git pull origin test:test #这句话的意思是，在本地创建一个test分支同步线上的test分支
# 切换到 test 分支 & 基本操作...
git checkout test & ...
# 保存，提交，更新
git add . & git commit -m "edit" & git push
# 此时会报错，那么使用错误提示后推荐的命令即可
git push --set-upstream origin test
```

### 具体开发过程
1. 线上仓库的建设
2. 线下开发人员克隆仓库 `git clone ...`
3. 项目组长开设实现不同功能的分支 `git branch ...`
4. 在不同的分支下实现功能 `git checkout ... & do something ...`
5. 合并分支 `git merge ...`
6. 有可能需要下载分支的最新状态 `git pull 分支:分支`
7. 推送分支的最新状态 `git push / git push --set-upstream origin 分支`
8. 下载最新的主线分支，在master分支下 `git pull`

### 使用 GitHub 自动化部署项目
* 大概过程
    * GitHub 托管代码
    * 本地开发代码并推送最新代码给GitHub，出发GitHub.hook（GitHub上的一个钩子程序）
    * Web服务器自动读取最新代码，GitHub.hook 请求 web.hook（web服务器上的钩子程序） 。
* 具体实现
    * 第一步省略（前面已经做过了）
    * 第二步，我们需要在 GitHub 上，打开项目，然后打开设置,最后打开这个地址:(username / projectName)对应你的GitHub用户名和项目名称：
    
        https://github.com/username/projectName/settings/hooks/new
    * 第三步，在你的Web服务器中写一个 WebHook
    ```
    <?php

        $secret = "你配置的 GitHub.hook 的 Secret";

        $path = "项目在本地物理地址的相对路径";

        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];

        if($signature) {
            $hash = "sha1=" . hash_hamac('sha1', file_get_contents("php://input", $secret));
            if(strcmp($signature, $hash) ** 0) {
                echo shell_exec("cd {$path} && /user/bin/git reset --hard origin/master && /user/bin/git clean -f && /user/bin/git pull 2>&1");
            exit();
            }
        }

    http_response_code(404);
    ?>
    ```
