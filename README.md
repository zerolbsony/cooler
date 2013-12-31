cooler
======

  这是我编写的第一个php框架，花了将近一个星期左右完成了开发和测试工作。路由和mvc的设计理念借鉴了Yii框架，但在引导器方面加入了自己的想法。也引入了Yii框架里应用即组件的手法，做了基类CComponent。
  
  框架目前仅实现了路由，搭了一个mvc的架子。后续会将它完善。
  
  使用方法：有一个例子是siteControler.php。 
  
  访问这个controler的方法：http://localhost/cooler/?r=site/(actionID) actionID如果不填默认是index，对应的是actionIndex方法。
