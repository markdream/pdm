#pdm 2.0（密码管理系统）#


##演示地址##

https://i.markdream.com/demo


##依赖##

PHP5.3+（需要mcrypt组件支持）

##安装##
警告：PDM2.0版本不兼容1.0项目

1、请在mysql中创建一个名为pdm（你可以自行修改）的数据库，然后将pdm.sql文件导入到pdm数据库中，即完成数据库导入工作。

2、在根目录中找到wp-config.php文件，修改里面的数据库配置文件参数为您实际数据库连接信息即可。

3、修改index.php文件中的"PDM_URL"改成你的PDM实际访问地址， 
比如：“http://127.0.0.1/”
“http://127.0.0.1/pdm/”
“http://demo.markdream.com/pdm/”
具体以你项目部署的情况而定。

4、注册需要邮件发送支持，请修改“wp-include/load.php”文件中的“邮件设置”项目进行填写即可，具体参见各个邮件服务商的配置信息。

OK，然后试着打开浏览器访问PDM2.0吧。