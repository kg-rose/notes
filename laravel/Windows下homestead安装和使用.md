# 需要下载的东西
* 虚拟机软件： [Virtual Box](https://www.virtualbox.org/wiki/Downloads)
* 虚拟机管理软件： [Vagrant](https://www.vagrantup.com/downloads.html)
* LaravelChina提供的 [Homestead 镜像](https://laravel-china.org/docs/laravel-development-environment/5.5/development-environment-windows/938) =》找到“安装和使用 Homestead”那一节
* [GitForWindows](https://github.com/git-for-windows/git/releases/download/v2.18.0.windows.1/Git-2.18.0-64-bit.exe)

# 安装 Homestead
* **首先全部命令都使用 Git Bash 命令行工具**
* 解压下载好的 homestead镜像压缩包，然后使用 `vagrant box add metada.json` 添加该 Box 到 vagrant 中
* 通过命令 `git clone https://git.coding.net/summerblue/homestead.git ~/Homestead` 下载 homestead 管理脚本
* `cd ~/Homestead` 进入克隆下好的目录后，使用 `git checkout v7.8.0` 切换到7.8.0版本
* 使用命令 `bash init.sh` 初始化 Homestead

# 配置 Homestead
* 使用命令打开 Homestead.yaml `code ~/Homestead/Homestead.yaml`
```
# 虚拟机设置
ip: "192.168.10.10"
memory: 2048
cpus: 1
provider: virtualbox

# 登录虚拟机授权连接的公钥文件，实现 ssh 免密登陆 （记住密码功能）
authorize: ~/.ssh/id_rsa.pub

keys:
    - ~/.ssh/id_rsa # ssh 私钥
    - ~/.ssh/id_rsa.pub # ssh 公钥

# 目录映射：本机和虚拟机上的代码是镜像关系，本机在 ~/Code 中编辑代码，则在虚拟机中实时更新
folders:
    - map: ~/Code # 本机上的代码目录
      to: /home/vagrant/Code # homestead 虚拟机中的代码目录

# 站点配置
sites:
    - map: homestead.test
      to: /home/vagrant/Code/Laravel/public

# 数据库名称
databases:
    - homestead

# 变量配置（了解即可）
variables:
    - key: EXAMPLE # 变量名
      value: this is an example to tell you how to use variables # 变量值
```
* 管理员模式下通过命令 `code C:/Windows/System32/Drivers/etc/hosts` 打开 hosts 并添加
```
192.168.10.10       homestead.test
```

> 默认没有 ~/Code 目录，所以 `mkdir ~/Code` 创建一个
> 默认没有 ssh 密钥，初始化一个密钥 `ssh-keygen -t rsa`


# 启动和使用 homestead
* 通过命令 `cd ~/Homestead && vagrant up` 进入 homestead 目录并且启动它
* 通过 `vagrant ssh` 登陆虚拟机， `exit` 推出虚拟机。
* 关闭 homestead `vagrant halt`
* 下面是一些常用命令
```
# 所有命令都需要在 ~/Homestead 中执行
vagrant init        初始化 vagrant
vagrant up	        启动 vagrant
vagrant halt	    关闭 vagrant
vagrant ssh	        通过 SSH 登录 vagrant（需要先启动 vagrant）
vagrant provision	重新应用更改 vagrant 配置
vagrant destroy	    删除 vagrant
```

> [参考文档](https://laravel-china.org/docs/laravel-development-environment/5.5/development-environment-windows/938)

# 后续补充
* 在 homestead 中 `cd ~/Homestead && vagrant up && vagrant ssh` ，配置 composer 使用中文镜像 `composer config -g repo.packagist composer https://packagist.phpcomposer.com` 