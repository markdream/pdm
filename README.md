#pdm（密码管理系统）#


##演示地址##

https://i.markdream.com/demo/security

##安装##
由于时间仓促，没有提供web形式的安装，所以请先在mysql中创建一个名为pdm（你可以自行修改）的数据库，然后将data.sql文件导入到pdm数据库中。

然后在根目录中找到config.db.php文件，修改里面的数据库配置文件参数即可。

然后修改index.php文件中的"P_HOST"改成你的地址 比如：http://127.0.0.1

然后再修改"P_LINK"中的参数，P_HOST不需要修改，如果你的PDM系统是在根目录应该改成“define ( 'P_LINK', P_HOST  );”，
如果不在根目录那么就应该写成“define ( 'P_LINK', P_HOST . '/yourdir' );”  "yourdir"是你的pdm目录名称。

##修改加密key##
打开“fck/bin.php”文件，将"PonyChiangpdb$92"字符串修改成你所想的字符串吧，注意必须保证是16个字符，如果你担心你key暴露，还可以可以将bin.php文件进行加密。推荐使用zend safeguard

OK，然后试着打开浏览器访问 http://127.0.0.1/yourdir 吧。


