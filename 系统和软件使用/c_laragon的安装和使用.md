* [下载地址](https://laragon.org/)
* 安装方式是傻瓜式的，安装的软件的端口都是默认的(apache80 mysql3306)。

> 我新装的windows就直接装了laragon ，成功的避开了端口的坑：）不过安装好了之后可以去修改

* 安装完成后，有几个事情还是需要做一下的
  * 如果不想装node ， 它自带node， 但是没有写进环境变量。将 ..\laragon\bin\nodejs\node-v8 放进环境变量（不推荐，不如自己下一个nodeJS，自动全局）
  * 将mysql 放进环境变量Path， 如果你愿意
  * 将php放进环境变量Path，php也装在 ..\bin 下
  * 将composer放进环境变量Path， 一样的
  * 打开终端，配置composer中文镜像 `composer config -g repo.packagist composer https://packagist.phpcomposer.com`

* laragon 每次启动都会检测 www\ 文件夹下的文件，如果你将新的项目放进去，它会自动给你创建一个 "项目名称.test" 的虚拟主机（hosts写入，apache配置）
