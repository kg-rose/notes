# Windows
* 环境所需
    * web服务器 APACHE/IIS
    * PHP 
    * MySQL 或其他支持的数据库
    * VC++Runtime **Windows 版的PHP/APACHE 依赖VC，所以运行时需要对应版本的“VC运行时”**
* VC运行时:缺少vcruntime140.dll的解决办法
    * 下载一个VC++2015，对应32位64位即可（通常一个安装32位64位都给你装上了）。
    * VC15兼容VC14。
    * 小知识：VC的年份不代表版本，版本是在后面的版本信息，例如VC++2015其实是VC14。
* [Apache](https://www.apachelounge.com/download/)
    * 解压后配置 **/conf/httpd.conf**
    ```
    ServerRoot "Apache目录绝对路径"
    Listen 端口默认80
    DocumentRoot "网站根目录"
    <Directory "网站根目录">
    
    # php支持 找到：
    <IfModule dir_module>
        DirectoryIndex index.html index.htm index.php
    </IfModule>
    
    # php支持 文件末尾添加
    PHPIniDir "php.ini文件地址"
    # phpX X为php版本
    LoadModule phpX_module "phpXapache2_4.dll地址"
    # 告诉apache 由php 处理这些后缀名文件
    AddType application/x-httpd-php .php .html .htm
    ```
    * 安装服务：控制台进入 **/Apache目录/bin/**
    ```
    httpd -k install
    ```
    * 添加httpd 进入环境变量（右键“我的电脑”->选择“属性”->“高级系统设置”->“环境变量”->**Path**中添加 **/bin** 目录绝对路径）

* [PHP](http://windows.php.net/download#php-7.2)
    * 版本选择
    ```
    TS √	线程安全(Apache推荐) 
  	NTS		非线程安全(IIS必须)
    ```
    * 解压（注意：php压缩包内的文件不是一个文件夹，为了方便，可以创建一个文件夹，然后将压缩包里的文件解压进去，我的命名规则是(小写php+版本)，例如php7.2.3 就存放为 php723）
    * 创建php.ini，进入php目录
    ```
    php.ini-development 用于开发环境的
    php.ini-production  用于生产环境的
    ```
    * 开发就选用development 重命名为为 **php.ini**
    ```
    # 指定扩展库目录
    extension_dir = "php目录/ext目录 绝对路径",
    # 开启扩展库
    extension=curl
    extension=gd2
    extension=mysqli
    extension=openssl
    extension=pdo_mysql
    extension=pdo_odbc
    extension=mbstring
    # 配置mbstring扩展库
    mbstring.language = Chinese
    mbstring.internal_encoding = UTF-8
    mbstring.http_input = UTF-8
    mbstring.http_output = UTF-8
    mbstring.encoding_translation = On
    mbstring.detect_order = UTF-8
    mbstring.substitute_character = none
    # 修改时区
    date.timezone = Asia/Shanghai
    ```
    
    * 添加php进入环境变量 略
    * 进入网站根目录，新建**phpInfo.php**
    ```
    <?php
        phpinfo();
    ```
    *  打开网页浏览localhost/phpinfo.php ，如果出现php详细信息页面，且
  **Loaded Configuration File** ，这一栏的地址指向的是正确的php.ini所在地址，则配置正确。
    * 配置好环境变量后，控制台输入php -v，如果没有报错而是告诉你三行数据（php版本 copyright版权 zend引擎版本）的话，则是正确的。否则会提示你找不到.dll的地址，那么再次核对php.ini，全部都是对的但是依然报错？：去引号，"/撇"改成" \双捺\ "

* [MySQL](https://dev.mysql.com/downloads/mysql/)
    * 选择Community Server , GPL（社区版）
    * 选择版本后，直接选择页面下方的 **No thanks, just start my download**
    * 注意：mysql压缩包中的文件命名很复杂(mysql-5.7.21-winx64)，为了规范，我们新建一个mysql57，压缩包中进入mysql-5.7.21-winx64文件夹，将里面的内容选中，解压到mysql57。
    * *由于解压版的mysql57安装过程需要使用的命令较多，先配置环境变量，添加mysql文件夹下/bin目录到Path。(同样，关闭所有窗口，右键bin目录，属性，安全，复制对象名称)
控制台输入path 查看环境变量是否配置成功，有误，系统变量用户变量Path都添加一次该目录，再有误，手输地址。
    * 重命名 **mysql57/my-default.ini** 为       **my.ini** 并配置该文件(没有my-default.ini就新建一个my.ini)：
    ```
    [mysql]

    # 设置默认字符集
    default-character-set=utf8
    [mysqld]
    #跳过授权表 初次安装时应不注释，安装成功后记得注释。该字段若不注释，则可以不使用账号密码直接访问数据库(危险)
    #skip-grant-tables
    
    # 端口设置
    port = 3306
    
    #设置mysql安装目录
    basedir = "mysql57目录绝对路径"
    
    #设置数据库文件存放目录
    datadir = "新建一个data文件夹指定该项为该文件夹绝对路径"
    
    #配置允许的最大连接数
    max_connections = 128
    
    #设置服务端使用的字符集
    default-storage-engine = INNODB
    ```
    * 控制台依次执行以下命令
    ```
    #安装mysql服务
    mysqld -install
    #初始化mysql库
    mysqld --initialize
    #启动mysql服务
    net start mysql
    #进入mysql 配置root账户的密码
    mysql
    update mysql.user set authentication_string=password('新密码') where user='root';
    ```
    * **my.ini** 注释掉 **#skip-grant-tables** 字段，并控制台重启mysql，然后进入mysql，使用命令查看数据库是否初始化成功，密码配置是否成功
    ```
    # 重启MySQL
    net stop mysql
    net start mysql 
    # 进入mysql
    mysql -uroot -p密码
    # 查看初始化后的数据库：应该出来4个系统库
    show databases; 	
    ```
* **坑**
    * 启动Apache 时说 DocumentRoot/ServerRoot 不是一个目录： httpd.conf中去目录两边引号，或者把"/撇"换成" \双捺\ "。
    * 添加环境变量后控制台命令仍不起作用： 系统变量的Path中添加一次，用户变量的Path中再添加一次，重启电脑。 可以用cmd>path; 调试看看目录地址是否正常。每次配置完了之后把所有窗口关了再试能不能使用命令。如果通过右键属性安全复制对象地址的方式配置不行，就手动输入地址。
    * httpd.conf中像【PHPIniDir "C:/wamp/php723/php.ini"】这种配置，如果找不到phpinfo页面中Loaded Configuration File 指向的地址是none，就需要仔细排查，【...Dir "地址"】 左边引号和r之间之有一个空格，同样，还是不行，去引号，/改成\ \
    * mysql，my.ini 记得注释 #skip-grant-tables
    * 检查php.ini中的extension_dir是否配置正确有2个方法：
        * 1,phpinfo.php页面中中看那些开启的功能(页面每个开启的扩展库都有一个单独的居中大标题)，或者
        * 2,控制台输入php -v 没加载成功会报错的。

* 常用命令
```
sc delete 服务名称		,从windows 系统中删除某个服务,需要管理员模式cmd

Apache
httpd /?				,命令帮助
httpd -k uninstall 		,卸载apache服务
httpd -k install        ,安装,需要添加apache/bin到环境变量或者进入该目录,需要管理员模式
httpd -k start 			,启动apache
httpd -k stop			,停止apache
httpd -k restart		,重启apache,更新配置文件后需要重启
httpd -t				,测试(检查语法是否有误)

Mysql
mysqld -install			,安装mysql
myslqd --initialize		,初始化mysql
net stop mysql			,停止mysql
net start mysql			,启动mysql
update mysql.user set authentication_string=password('新密码') where user = 'root';	,设置密码

*关于windows端口：1~65535 没被占用均可用
*cmd>netstat 查看活动中的端口检查是否被占用
*cmd>netstat -ano 可以查看到端口和对应的pid，可用通过记住pid，在任务管理器中释放pid。
```

# 虚拟主机配置
* httpd.conf
```
# Virtual hosts
# Include conf/extra/httpd-vhosts.conf
# 去掉上一行代码的注释 开启虚拟主机
```
* vhosts
```
# *:端口号
<VirtualHost *:80>
    # 管理员联系邮箱
    ServerAdmin vhosts@example.com
    # 根目录
    DocumentRoot c:/www.test.com
    # 网址
    ServerName www.example.com
    # 错误日志和一般日志的存放地址
    ErrorLog "logs/errorLog.log"
    CustomLog "logs/CustomLog.log" common
</VirtualHost>
```
* host
```
cmd > notepad C:\Windows\System32\drivers\etc\hosts
```
编辑该文件:
```
127.0.0.1 c:/www.test.com
```
* 重启Apache

# composer
* 是什么？依赖管理工具（下轮子的）

* 安装：确保php.exe处于环境变量
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') **= '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
* 全局使用中文镜像
```
composer config -g repo.packagist composer https://packagist.phpcomposer.com
```
* 升级
```
# 自我更新
composer self-update
# 回滚更新
composer self-update --rollback
```
