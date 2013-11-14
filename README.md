Quickling解决方案

### 介绍
Quickling解决方案，包括两部分

+ 分片延迟渲染(LazyRender)
+ BigPipe

---
**分片延迟加载** 一个页面可以分成多次请求进行渲染，减少首屏渲染时间。

**BigPipe** 后端渲染方式下实现前端局新效果，实现一站式的体验效果。提升静态资源缓存命中率。

### 使用

lazyrender示例程序在 `lazyrender` 目录

BigPipe 示例程序在 `single` 目录

#### 使用示例程序

第一步，需要安装[fis-plus][0]

```bash
$ npm install -g fis-plus
```

第二步，clone 示例代码到本地

```bash
$ git clone https://github.com/xiangshouding/bigpipe.smarty.git
$ cd bigpipe.smarty/single
$ git submodule init
$ git submodule update
```
我想你有必要了解一下[git submodule](http://git-scm.com/book/en/Git-Tools-Submodules)

第三步，使用安装的[fis-plus][0]编译发布项目

```
$ fisp release -cmpr common
$ fisp release -cmpr index
```

第四步，启动开发服务器

```
$ fisp server start
```

第五步，安装本地测试框架

```
$ fisp server install pc
```

第六步，打开浏览器访问[http://127.0.0.1:8080/index/page/index]()


[0]: https://github.com/xiangshouding/bigpipe.smarty.git "BigPipe.smarty"