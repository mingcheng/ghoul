Ghoul - Simple MiniBlog

Ghoul 为使用 Sqlite 的简单微博客系统

=特性=

    1、使用 HTTP 验证
    2、自定义配置文件（data/config.ini）
    3、方便外部 API 调用
    4、使用 Apache 重写 URL 友好
    5、具有插件系统，可以同步发送至其他微博客系统 


=系统需求=

    1、PHP5 以上，需要 PDO_Sqlite 支持
    2、Apache 支持 .htaccess 文件以及打开 mod_rewrite 模块


=安装=

    1、将安装包解压缩至某 Apache 可访问路径
    2、根据自身情况，编辑 data/config.ini 配置文件
    3、在 *ix 中，将 data 目录设置为 777
    4、运行 install.php ，运行无误后请务必删除


=API=

    本例子中，Ghoul 安装至 http://127.0.0.1/micro_blog/，那么定义接口地址如下

    ==发送（正确返回最后插入数据库 ID）==

        可以直接使用 http://127.0.0.1/micro_blog/post.html 测试

        http://127.0.0.1/micro_blog/post

        参数：content 发送内容
        验证：需要 HTTP 验证

    ==获取（JSON 格式）==

        ===所有条目===

            http://127.0.0.1/micro_blog/show/?ajax=true

        ===指定 ID 的条目===

            http://127.0.0.1/micro_blog/show/[id]/?ajax=true

            注：在 data/config.ini 中配置了 AUTH_OBTRUSION = true 时，需要 HTTP 验证

    ==删除（正确返回最后插入数据库 ID）==

        http://127.0.0.1/micro_blog/delete/[id]/

        或者

        http://127.0.0.1/micro_blog/delete?id=[id]

        验证：需要 HTTP 验证


=联系方式=

    mingcheng<i.feelinglucky[at]gmail.com>
    Blog: http://www.gracecode.com/
