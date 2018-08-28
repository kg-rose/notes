# 安装mysql
* 安装
```
sudo apt-get install mysql-client
sudo apt-get install mysql-server
```
* 配置
```
# subl 打开 /etc/mysql/debian.cnf 查看默认的账号的密码，然后进入mysql命令行
subl /etc/mysql/debian.cnf
-mysql -u默认账号 -p默认密码
# 干掉root用户
DROP USER 'root'@'localhost';
# 创建一个root用户，默认密码为root
CREATE USER 'root'@'%' IDENTIFIED BY 'root'; 
# 给root用户所有的权限
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';
# 有时候代码争取却报错，是需要刷新一次权限才行。
FLUSH PRIVILEGES;
```

> 为什么这么干：因为ubuntu用apt-get下载的mysql，没有root密码，非常麻烦，既然我们都用ubuntu了，所以应该暴力一点，直接干掉root，再创建一个新root。

> 还嫌麻烦，直接用这个命令干进mysql的控制台 sudo mysql 

# 安装mysql-workbench
* 安装
```
sudo apt-get install mysql-workbench
```
* 2018-5-15 补充: MySQL Workbench 实现 root 功能： 上面我那个方法，其实 root 是没有授权功能的（可以看到所有数据库并修改他们， 但是不能给新建的其他用户授权）， 所以我们用 Workbench 新建一个链接， 帐号就用 /etc/mysql/debian.cnf 这个文件夹里的，因为该账户拥有最高权限。

# 安装apche
* 安装
```
sudo apt-get install apache2
```
* [测试](http://localhost/)
* 默认文档根路径为 /var/www/html，很不方便。但是我又不想改配置文件，所以我创建一个软连接，把这个文件放进 ~/ 目录里
```
# 类似于windows创建 html 文件夹的快捷方式 到 “我的文档”
ln -s /var/www/html ~/wampDocs
```

# 安装php
* 安装
```
# 可以在php后面写版本：但是要求php和php对apache的支持文件以及对mysql的支持文件必须三个版本必须一致
sudo apt-get install php
sudo apt-get install libapache2-mod-php
sudo apt-get install php-mysql
```
* 测试
```
# 给html文件夹所有权限
sudo chmod 777 /var/www/html
# 在html/下新建phpinfo
<?php phpinfo();
# 访问localhost/phpinfo.php （主要看mysqli支持是否开启）
```

# 配置
* apache配置中添加对.php文件的支持
```
# 打开配置文件
subl /etc/apache2/apache2.conf
# 添加
AddType application/x-httpd-php .php .htm .html
AddDefaultCharset UTF-8
```
# 特别注意
> 无论对 /etc/apache2下的任何文件作修改，都必须重启一次apache2

# 安装composer
```
# 安装
sudo apt-get install composer
# 配置全局使用中文镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```

# 命令
```
# 无论 apache2 还是 mysql5 都可以使用 service 命令操作
# 比如 重启apache2
service apache2 restart
```
