Quickling解决方案-一站式

### 介绍

### 使用

#### 使用示例

第一步，需要安装[fis-plus][0]

```bash
$ npm install -g fis-plus
```

第二步，clone 示例代码到本地

```bash
$ git clone https://github.com/xiangshouding/bigpipe.smarty.git
$ cd bigpipe.smarty/single
```

第三步，使用安装的[fis-plus][0]编译项目

```bash
$ fisp release -cmpr common
$ fisp release -cmpr index
```

第四步，启动开发服务器

```bash
$ fisp server start
```

第五步，安装本地测试框架

```bash
$ fisp server install pc
```

第六步，打开浏览器访问[http://127.0.0.1:8080/index/page/index]()


[0]: https://github.com/xiangshouding/bigpipe.smarty.git "BigPipe.smarty"
