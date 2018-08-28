# 在一台pc里安装windows & ubuntu
1. ## windows安装： 略
2. ## 在windows系统下需要做的准备工作：
* 分离一个空余的磁盘用于安装ubuntu：
    * 右键我的电脑->管理->磁盘管理->选择一个容量尽量大的磁盘->选择压缩卷
    * 压缩卷的4个值的具体意思是：1=>总容量，2=>可以分离的容量(总容量 - 已经使用的容量)，3=>要分离的容量（<=2），4=>分离后的本磁盘剩余的容量(>=已经使用的容量)
    * 所以我们可以把一张空磁盘进行”压缩”（分离）全部给变成”可用空间“
* 准备一个u盘，制作成启动盘
    * 下载[ubuntu](https://www.ubuntu.com/download/desktop)
    * 下载[rufus](http://rufus.akeo.ie/?locale=zh_CN)
    * 打开rufus，选择ubuntu.iso，将插入的u盘格式化并且将ubuntu写入u盘
3. ## 进入bios设置boot
    * 每个人的bios不同，但是配置项目是通的：bootMode=>启动方式(选择Legacy)，bootPriority(优先启动项，选择Legacy)，EFI(这一项是你bootMode选择UEFI时需要注意的)，Legacy(将U盘放在列表最上面第一项，这样就是U盘启动了)
    * 有一个很大的坑：ubuntu自带一个叫nouveau的显卡驱动，如果你有两张显卡（独显+集显），并且你的bios中的configuration中可以设置Graphic Device可以设置"UMA only"（仅使用集显），最好去这么设置一下，因为nouveau一旦不支持你的独立显卡，可能会让你无法启动ubuntu，会一直给你报一个nouveau 0000:01:00.0: fifo: SCHED_ERROR 08 []错误（命令行刷屏）
    * 补充：记得关闭Secure Boot(disabled)
4. ## 开始安装ubuntu
    * 设置好bios后（保证u盘是第一启动项，并且仅使用集成显卡）保存并退出，电脑自动重启，你就进入了u盘中的ubuntu系统，选择安装ubuntu
    * 弹窗左侧是语言选择，选择中文，右侧选择安装ubuntu。
    * 不要连接wifi
    * 选择最小安装，不要勾选"安装ubuntu时下载更新"以及"...安装第三方插件"（因为这些我们可以在安装完成后完善）
    * **安装类型** ： 选择 **其他选项** 开始分区：选择我们专门为ubuntu分离出来的空硬盘，点击“+”号
    * 具体分区：（单位mb）
        * 挂载点/ 逻辑分区 空间起始位置 Ext4日至文件系统 分完后面3区后剩下的都是它的
        * 挂载点/boot 逻辑分区 空间起始位置 Ext2日志文件系统 建议512mb
        * 挂载点/home 逻辑分区 空间起始位置 Ext4日志文件系统 看情况给，我给的很少，20G。（如果你喜欢拿ubuntu存照片看电影可以多给点，因为我的使用习惯更多是把我的代码这些东西写在/某一个文件夹中，这个盘符是~，可以理解为window中的“我的文档“）
        * 交换空间swap 逻辑分区 交换空间 内存的一半大小即可
    > 因为我们是windows(主系统) + ubuntu(子系统)，windows由C盘作为主分区，ubuntu则全部选择逻辑分区即可。

    > 通常来说如果不想折腾，你可以只分一个/根分区，这个行为就像windows只弄个C盘一样。 根的大小 = 总容量 - 引导分区boot - 用户分区home。

    > 你还可以分/usr, /etc, ...等等这些分区，每个分区就像一个文件夹，存放着不同类型的文件。不过通常我们就分一个/和一个/home，一个/boot即可。

    * 下面**安装启动引导器的设备选项**这里，一定要选 **/boot** 分区对应的分区。

    * 接着就是傻瓜式的安装了，安装完成后重启，进入windows系统

5. ## windows通过EasyBCD设置启动项
    * 下载[EasyBCD](http://neosmart.net/Download/ThankYou)，随便输入一个名称和邮箱就可以下载了。
    * 进入EasyBCD->添加新条目->选择Linux选项卡->类型选择grub2->名称自己写->驱动器选择linux安装的那个磁盘。
    * EasyBCD->选择编辑引导菜单，设置默认启动项为Windows。保存设置。

6. ## 进入ubuntu后需要做的事
    * 给root(超级管理员)修改密码：【ctrl+alt+T】打开命令行，输入
    ```
    # sudo 是指的这条命令是以超级管理员的用户发出的（我们登陆在桌面系统上的只是普通用户）
    sudo passwd root
    # 之后会让你输入一次旧密码和两次新密码
    ```
    * 更新apt-get
    ```
    # 更新一次apt-get的库
    sudo apt-get update
    ```
    * 卸载显卡驱动
    ```
    # purge 肃清（彻底删除）
    sudo apt-get purge nvidia-*
    ```
    * 安装一个vim(我们不把它当主要编辑器，主要是用来在命令行里编辑配置文件用)
    ```
    # apt-get 是 debian(ubuntu就是debian的衍生版)提供的一种安装包以来管理工具（类似php的composer，nodeJS的npm，说白了就是个软件下载工具，不过从它这里下载的都是可以在ubuntu上完美运行的程序）
    # install 是 apt-get的命令 意思是安装
    sudo apt-get install vim
    ```
    * 彻底干掉nouveau
    ```
    # 进入 /配置文件/自动载入模块(类似windows系统下的服务)配置文件
    cd /etc/modprobe.d/
    # 使用vim 编辑（不存在会新建）一个叫blacklist-nouveau.conf的文件
    sudo vim blacklist-nouveau.conf
    # 在编辑模式下，按i(insert)进入编辑模式，输入
    blacklist nouveau
    options nouveau modeset=0
    # 按一次esc退出编辑模式，再按一次“冒号”，输入wq（保存并退出）
    # 重置内核引导
    sudo update-initramfs -u
    # 重启ubuntu
    sudo reboot
    ```
    * 重启之后，你可以在bios里开启独显驱动了

    > 解释一下我为什么这么干：第一，我需要使用windows娱乐，则我需要独立显卡。第二，我使用ubuntu完成代码编写的工作，我在ubuntu环境下不需要独立显卡。如果我在bios开启独立显卡，则会导致ubuntu内置的nouveau通用显卡驱动不停地报错。所以：我在ubuntu里卸载了独立显卡（不让它挂载），在ubuntu里卸载了nouveau。然后在bios中再打开。这样ubuntu不认识独显，而windows认识。
    
    * 如果我依然想在ubuntu里使用独立显卡，那么可以在[NVIDIA 驱动程序下载](http://www.nvidia.cn/Download/index.aspx?lang=cn)下载一个对应显卡版本的.run文件，然后使用命令跑这个文件完成显卡驱动的安装
    ```
    sudo sh .run文件名
    ```
    * 安装curl
    ```
    sudo apt install curl
    ```
    * 安装git
    ```
    sudo apt-get install git
    ```
    * 安装oh my zsh
    ```
    # 安装zsh(需要先装curl和git)
    sudo apt-get install zsh
    # 安装 oh my zsh(需要装zsh)
    sh -c "$(curl -fsSL https://raw.github.com/robbyrussell/oh-my-zsh/master/tools/install.sh)"
    # 设置默认使用zsh为命令行工具
    chsh -s /bin/zsh
    ```
    * 如果设置不了
    ```
    # 打开passwd配置文件并编辑
    sudo vim /etc/passwd
    # 找到 用户:x500:500:x:用户,,,:/home/ubuntu:bin/bash
    # 把bash换乘zsh
    用户:x500:500:x:用户,,,:/home/ubuntu:bin/zsh
    # ESC，冒号，wq，回车，重启
    ```
    * vim太难用？下载[sublimeText3.bz2](https://download.sublimetext.com/sublime_text_3_build_3170_x64.tar.bz2)
    * 解压
    ```
    # 在.bz2文件下使用命令解压
    tar jxvf 下载的sublime.bz2
    ```
    * 编辑 ~.zshrc
    ```
    # 最后一次使用vim
    vim ~/.zshrc
    # 在文档末尾输入 **alias 命令别名="命令调用的程序的地址" 必须是双引号，等号两边最好不要有空格"
    alias subl="sublime_text所在路径"
    # 现在你可以使用subl 打开文件了
    # 我最后将命令写为
    alias subl="sudo ~/sublime_text_3/sublime_text"
    # 这样以来我每次使用subl必须输入一次管理员密码，但是是用超级管理员的身份使用sublime：我只用sublime编辑一些配置文件，主要使用vscode编码
    ```
    * 下载和安装[vscode](https://code.visualstudio.com/Download)，下载.deb，是傻瓜式的安装。
    * 下载和安装chrome
    ```
    # 添加源
    sudo wget http://www.linuxidc.com/files/repo/google-chrome.list -P /etc/apt/sources.list.d/
    # 添加密钥
    wget -q -O - https://dl.google.com/linux/linux_signing_key.pub  | sudo apt-key add -
    # 更新下载库
    sudo apt-get update
    # 安装谷歌浏览器
    sudo apt-get install google-chrome-stable
    ```
    * 优化界面
    ```
    # 安装一个叫gnome-tweak-tool gnome桌面(ubuntu18.04的桌面系统-优化-工具)
    sudo apt install gnome-tweak-tool
    # 在应用界面打开它，名字就要tweak，你可以设置字体大小了
    ```
    * 谷歌拼音。
    ```
    # 安装并且配置(im-config)
    sudo apt install fcitx fcitx-googlepinyin im-config
    # 有时侯不出来，再输入一次im-config即可，在弹出来的对话框中，选择fcitx作为中文输入法即可
    # 安装完成后记得重启，默认开启中文输入法的快捷键是 ctrl+空格
    ```
    * 2018-5-12 补充: 解决有时候开机黑屏的问题(还是显卡的锅)
    ```
    #打开 grub配置
    subl /etc/default/grub
    # 在"quiet splash" 后见加上 nomodeset
    GRUB_CMDLINE_LINUX_DEFAULT="quiet splash nomodeset"
    # 重启搞定
    ```

7. ## 永远记得的事情
    * 你正在挑战使用比windows复杂很多的操作系统。但是在你熟练使用之后，他会让你的工作效率大大提高。
    * 专著于使用ubuntu系统所带来的便利。
    * 记住，ubuntu属于debian，因此在很多有软件有提供debian和linux两种架构的安装包的时候，优先选择debian(.deb)。
