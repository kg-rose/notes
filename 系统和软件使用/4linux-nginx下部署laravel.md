# 事先准备
* [查阅文档](https://laravel-china.org/docs/laravel/5.6/installation#server-requirements)
* 安装laravel最新版有以下要求：
    * php 版本 >= 7.1.3 (我们之前装的是7.2.3)
    * pdo 扩展、MbString 扩展、Tokenizer 扩展、XML 扩展、Ctype 和 Json 扩展。（我们挨个用 sudo apt-get install php-扩展） 执行一次。把没安装上的装一次。
* 安装全局的laravel脚手架(帮我们new项目的命令行工具)
```
composer global require "laravel/installer"
# 安装的时候提示我没有安装 php-zip，那么安装一次
sudo apt-get install php-zip
```
* 安装完成后坑来了：laravel命令找不到
```
# 经过我的摸索，发现laravel是存在于这里的
~/.config/composer/vendor/bin/laravel
# 因此我们使用zsh提供的alias，将该地址手动添加到zsh命令中
# 打开配置
subl ~/.zshrc
# 末尾添加
alias laravel="~/.config/composer/vendor/bin/laravel"
```
* 创建项目
```
# laravel new 一个项目放在/var/www/html/laravelStudy中
# laravel new /var/www/html/laravelStudy
# 上面这条命令有个坑： 他不把第一个 / 读为根目录，而是读成了 当前路径，因此他会在当前路径创建一个 /var/www/html/laravelStudy文件， 因此我们应该
cd /var/www/html
laravel new laravelStudy
```

# 也可以直接通过composer创建项目
```
# 进入项目存放的目录后执行
composer create-project --prefer-dist laravel/laravel 你的项目名称
```

# 安装完成后，我们照着路径访问一次
```
# 有可能会出现跟路径storage有关的几个错误，都是由于storage没有被操作的权限
cd .../storage/
sudo chmod 777 *
cd framework
sudo chmod 777 *

# 其实这个方法极其不安全：我们给所有用户都授权了操作这些文件夹的所有权限
# 但是由于我们是为了在自己电脑上搞个人开发，所以也没有所谓的不安全
# 具体权限方面相关的知识可以去学习linux系统管理方面的知识。（运维知识）
```
# 部署虚拟主机的大致流程
1. 创建一个laravel项目
2. 编辑hosts 创建一个虚拟域名
3. 创建一个配置文件，并编辑它
4. 给配置文件一个软链接使其投入使用，重庆并访问我们配置的地址

> nginx的“虚拟主机”应该叫“服务器模块”

# 部署虚拟主机的具体过程
1. composer创建项目
```
# 我把项目叫做laravelStudy
composer create-project --prefer-dist laravel/laravel laravelStudy
```
2. 编辑hosts文件
```
# subl打开hosts
subl /etc/hosts
# hosts中添加内容
127.0.1.1	www.laravelstudy.com
```
3. 创建一个nginx配置文件
    * 这里我直接拷贝default
    ```
    # 先进入这个配置文件路径
    cd /etc/nginx/sites-availabel/
    # 这里如果说权限不够就用超级管理员吧
    sudo cp default laravelStudy 
    ```
    * 编辑 laravelStudy
    ```
    # 改端口，去掉尾巴“default”，不然会报错
    listen 80;
    listen [::]:80;

    # 设置根目录 => laravel框架/public
    root /var/www/html/laravelStudy/public;

    # 设置服务器名称（跟hosts）的一直
    server_name www.laravelstudy.com;
    ```
4. 重启 service nginx restart 访问 laravelstudy.com 搞定

# 补充，Laravel文档其实为nginx部署写了一份[官方配置](https://laravel-china.org/docs/laravel/5.6/deployment/1357#nginx)
```
server {
    listen 80;
    server_name www.laravelstudy.com;
    root /var/www/html/laravelStudy/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        // 这里，如果你和我一样指向 fpm 模块物理地址有问题的话，直接上端口
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        // 只有一个坑：就是添加上这句
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
