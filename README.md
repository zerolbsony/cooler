cooler
======

  这是我编写的第一个php框架，花了将近一个星期左右完成了开发和测试工作。路由和mvc的设计理念借鉴了Yii框架，但在引导器方面加入了自己的想法。也引入了Yii框架里应用即组件的手法，做了基类CComponent。
  
  框架目前仅实现了路由，搭了一个mvc的架子。后续会将它完善。
  
  使用方法：有一个例子是siteControler.php。 
  
  访问这个controler的方法：http://localhost/cooler/?r=site/(actionID) actionID如果不填默认是index，对应的是actionIndex方法。

  通过修改http web server的conf文件来支持rewrite，则可以通过配置host指定域名来直接访问。
  
 Apache配置文件(http-vhosts.conf):
<pre><code>
 <VirtualHost *:80>
    ServerAdmin 邮箱
    DocumentRoot "项目地址"
    ServerName 域名
    <Directory "项目地址">
	DirectoryIndex index.html index.htm default.htm index.php default.php index.cgi default.cgi index.pl default.pl index.shtml
	AllowOverride None
	Order allow,deny
	Allow from all

	Options +FollowSymLinks
	IndexIgnore */*
	RewriteEngine on

	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d

	RewriteRule . index.php
    </Directory>
 </VirtualHost>
</code></pre>
 Nginx配置文件(nginx.conf):
<pre><code>
 server {
 	listen  80;
 	server_name  域名
 	root "项目地址"
 	
 	location / {
 		root "项目地址"
 		index index.html index.php
 		
 		if(!-f $request_filename){
 			set $rule_0 1$rule_0;
 		}
 		if(!-d $request_filename){
 			set $rule_0 2$rule_0;
 		}
 		if($rule_0 = "21"){
 			rewrite /. /index.php;
 		}
 	}
 	
 	location ~ \.php {
 		fastcgi_pass  backend;
 		fastcgi_index index.php;
 		fastcgi_param SCRIPT_FILENAME  $document_root$fastcgi_script_name;
 		include       fastcgi_params;
 	}
 }
 </code></pre>