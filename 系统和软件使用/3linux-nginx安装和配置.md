# 事先准备
* nginx默认端口也是80，我们安装过了apache，端口也是80，我们主要用nginx，所以我们把apache的端口改一改
```
# 用sublime打开port.conf
subl /etc/apache2/port.conf
# 找到Listen 80，改为 8080
# 重启apache访问localhost:8080查看是否成功
service apache2 restart
```

# 安装
```
# 更新apt-get的库
sudo apt-get update
# 安装nginx
sudo apt-get install nginx
# 访问 localhost查看是否成功
```

# 添加php支持
* 安装php-fpm（这是一个帮助nginx处理php请求的模块）
```
sudo apt install php7.2-fpm
```
* 编辑 /etc/nginx/sites-availabel/default
```
# subl打开
subl /etc/nginx/sites-available/default
# 具体要更改的地方

...

    # Add index.php to the list if you are using PHP ： 这里解释的很清楚了，如果你要用php，就在后面加上 index.php
	index index.html index.htm index.nginx-debian.html index.php;

...

# pass PHP scripts to FastCGI server ： 这里告诉你，把php脚本交给FastCGI 服务器处理
location ~ \.php$ {
    include snippets/fastcgi-php.conf;

#	这种方式是直接告诉php-fpm的地址
#	fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
# 	With php-cgi (or other tcp sockets): 这里用端口去处理
fastcgi_pass 127.0.0.1:9000;
}
```

* 如果选择 ip:port 去处理php请求的话，则需要再编辑etc/php/7.2/fpm/pool.d/www.conf
```
# 找到这一段
; listen = /run/php/php7.2-fpm.sock
# 注销了（添加;），写这一段：
listen = 127.0.0.1:9000
```

* **不知道为什么，php-fpm只能支持ip:port，如果我直接在nginx配置中告诉php-fpm的地址，会出现502错误。**

* 重启nginx 和 php-fpm
```
service nginx restart
service php7.2-fpm restart 
# web文档的默认路径依然是 /var/www/html ，在下面新建phpinfo.php，内容你懂得，看看是否成功
```


# 常用命令
```
# 开、关、重启
sudo systemctl start nginx
sudo systemctl stop nginx
sudo systemctl restart nginx

# 重新加载
sudo systemctl reload nginx

# 禁止服务（开机自动运行）和允许服务
sudo systemctl disable nginx
sudo systemctl enable nginx
```

* 如果启动报错，你可以使用这个命令检查配置文件的语法是否有错误
```
sudo nginx -t
# 这里有个问题：如果你不用超级管理员身份运行这条命令，会很多错：因为普通用户无法操作nginx。所以当你忘记写sudo而引起报错时不要怕，只要sudo执行这条命令没错，就没问题。
```

# 配置失败，但是看不到错误？开启php报错功能
```
# php.ini 在这里
subl /etc/php/7.2/fpm/php.ini

# 开启php 报错功能
display_errors = On
display_startup_errors = On
```
