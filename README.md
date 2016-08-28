# 请高手平台 

## 说明
每个模块一个目录，目录名以`module-`开头
`doc`目录为文档
`tools`目录为辅助工具

## 模块

- （系统平台）
- 高手问答
- 妙答
- 精品课程
- 我房我管



# 【信息发布模块】


采用wordpress
发表信息，必须指定一张封面图片

## wp安装和设置

### 安装
常规安装

### 设置homeurl
通常`homeurl`不一定要设置为同安装目录
建议设`homeurl`为域名根目录，如 http://qinggaoshou.com/
为了配合这个设置，需要在该目录下加一个index.php文件，负责显示wordpress

### homeurl下创建index.php 文件
代码库中的 `module-wp\index.php` 文件 可做为 homeurl目录下的index.php使用
建议 homeurl目录下做一个软链接到git代码目录，以便随时更新。

### homeurl下创建 index.site-config.php 文件

````
$GLOBALS['cfg']=[
'WP_PATH'=>'./wordpress', // wp安装路径
'WX_APPID'=>'',
'WX_APPSECRET'=>''
];
//以上3个选项可以在index.site-config.php这个自定义文件中重定义
// index.site-config.php 文件代码仓库中是没有的，需要自已新建，把上面几行拷过去，然后修改为正确的配置
````

### 安装微信扫描登录wordpress的插件`qgs-wechat-login`

·plgin-qgs-wechat-login.php·是个登录wordpress的插件。
它的功能有：
- 修改wp默认的登录界面，显示微信扫描登录的二维码。
- 创建一个“高手帐户”的widegt小工具，可显示微信扫描登录的二维码，或已登录的的用户信息。


