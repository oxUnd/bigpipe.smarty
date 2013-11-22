Quickling解决方案

### 介绍
Quickling解决方案，包括两部分

+ 分片延迟渲染(LazyRender)
+ 局部刷新

---
**分片延迟加载** 一个页面可以分成多次请求进行渲染，减少首屏渲染时间。

**局部刷新** 后端渲染方式下实现前端局新效果，实现一站式的体验效果。提升静态资源缓存命中率。

### 使用

lazyrender示例程序在 `lazyrender` 目录

局部刷新 示例程序在 `single` 目录

#### 使用示例程序

第一步，需要安装[fis-plus][0]

```bash
$ npm install -g fis-plus
```

第二步，clone 示例代码到本地

```bash
$ git clone https://github.com/xiangshouding/bigpipe.smarty.git
$ cd bigpipe.smarty
$ git submodule init
$ git submodule update
```

局部刷新
```
$ cd single
```

LazyRender

```bash
$ cd lazyrender
```

我想你有必要了解一下[git submodule](http://git-scm.com/book/en/Git-Tools-Submodules)

第三步，使用安装的[fis-plus][0]编译发布项目

局部刷新

```
$ fisp release -cmpr common
$ fisp release -cmpr index
```

LazyRender
```
$ fisp release -cmp
```
第四步，启动开发服务器

```
$ fisp server start
```

第五步，安装本地测试框架

```
$ fisp server install pc
```

第六步，打开浏览器访问

局部刷新: [http://127.0.0.1:8080/index/page/index]()

LazyRender: [http://127.0.0.1:8080/pagelet/page/index]()




### 使用文档

ok，到现在都看到了一个比较简单的例子，当然看着这个例子，估计有可能也不知道怎么用。通过下面的内容，
来阐述具体使用。

----
#### **lazyrender**

首先，你得拥有一个FIS-PLUS的项目；可以下载我提供的demo。

其次，你得使用[Quickling解决方案的插件][1]，引入[前端loader][2]，[modjs][3]保持最新，[前端loader][2]依赖[lazyload.js][4]


```smarty
{%html framework="pagelet:static/mod.js"%}
    {%head%}
    	...
        {%require name="pagelet:static/lazyload.js"%}
        {%require name="pagelet:static/BigPipe.js%}
        ...
    {%/head%}
    {%body%}
    	...
    {%/body%}
{%/html%}
```

最后，发布这个项目；访问对应URL查看页面。


现在看一下`asyncrender/page/index.tpl`

有些widget添加了属性`pagelet_id` 和 `mode`

```smarty
{%widget name="pagelet:widget/box/box.tpl" pagelet_id="second" mode="quickling"%}
```

+ pagelet_id 给这个widget取了个名字，这个名字用来异步请求这个widget时使用
+ mode 这个参数的值只有一个`quickling`，表明当前这个widget要进行延迟异步渲染

OK，已经指明widget延迟渲染了，那么这样是不是就可以work了。NO，因为我们没有**触发**异步请求渲染页面。

来看一下加了pagelet_id, mode属性后的widget输出源码长啥样？

```html
<textarea class="g_fis_bigrender" style="display:none;">BigPipe.asyncLoad({id: "second"});</textarea><div id="second"></div>
```

原来widget位置被上面的代码占据了。了解过bigrender的同学一眼就看出这是干什么的了。

`class="g_fis_bigrender"`的`textarea`里面包含的是触发异步请求的接口。你只需要
取出里面的内容触发即可。可以根据滚动事件，或者是其他什么条件来控制触发异步请求渲染。

```javascript
window.onload = function() {
    var elms = document.getElementsByClassName('g_fis_bigrender');
    for (var i = 0, len = elms.length; i < len; i++) {
        window['eval'] && window['eval'](elms[i].innerHTML);
    }
};
```
demo中就简单粗暴的执行了它。执行完成后，会发起一个异步请求

```
http://127.0.0.1:8080/pagelet/page/index?pagelets[]=second&t=858607
```
表示请求的是一个`pagelet_id = 'second'`的widget。

pagelets是个数组，可以一次请求多个widget。

到这里，你应该知道怎么用及整个执行过程了。恭喜，你又知道了一个延迟加载的方法。想快速
使用这个方案，那就用FIS改造你的项目吧。

上面提到一次请求多个widget，那该如何处理呢。FIS提供了组(`group`)的概念

只需要添加group属性即可；

```smarty
{%widget name="a.tpl" mode="quickling" pagelet_id="a" group="A"%}
{%widget name="b.tpl" mode="quickling" pagelet_id="b" group="B"%}
{%widget name="a1.tpl" mode="quickling" pagelet_id="a1" group="A"%}
```

在渲染时a + a1会发起一个请求，b发起一个请求。

添加group以后输出的源码是什么样子的？

```html
<textarea class="g_fis_bigrender g_fis_bigrender_a" style="display: none">BigPipe.asyncLoad([{id: "second"},{id:"third"}])</textarea><div id="second"></div><div id="third"></div>
```
追加了class `g_fis_bigrender_a`，为了更好的适应不同用户需求。

看看发起的请求；

```
http://127.0.0.1:8080/pagelet/page/index?pagelets[]=third&pagelets[]=second&force_mode=1&t=915745
```

---------

#### **局部刷新**

首先，你得拥有一个FIS-PLUS的项目；可以下载我提供的demo。

其次，你得使用[Quickling解决方案的插件][1]，引入[前端loader][5] （跟上面提到有所不同，这个相对于大一些），[modjs][3]保持最新，[前端loader][2]依赖[lazyload.js][4]

```smarty
{%html framework="common:static/lib/js/mod.js"%}
    {%head%}
    	...
        {%require name="common:static/lib/js/lazyload.js"%}
        {%require name="common:static/lib/js/BigPipe.js"%}
        ...
    {%/head%}
    {%body%}
    	...
    {%/body%}
{%/html%}
```

最后，发布这个项目；访问对应URL查看页面。如果你使用的是demo[single]，那么现在就可以看到效果了。如果是你自己的项目，你会发现啥反应也没有。

`局部刷新` 中FIS提供了一个可被异步请求的`后端框架`(以[smarty插件][0]的方式)；
[前端loader][5]。

前端loader提供接口`fetch`方法，来异步请求渲染一个widget。

```javascript
BigPipe.fetch(url, containerId);
```

例子

```javascript
BigPipe.fetch('/index/page/index?pagelets[]="pager"', 'pager');
```
表示请求`paglet_id="pager"`的widget，并把它渲染到页面的`<div id="pager"></div>`
内。

So，这个接口提供了异步请求渲染一个widget的能力。这样就可以实现局刷了。

但但但是，这个似乎用着实在太不顺手了。在前端需要考虑很多。

OK，跟[@donny](https://github.com/doith) 同学合作写的页面管理的前端库[page.js](https://github.com/xiangshouding/bigpipe.smarty/blob/master/single/common/static/lib/js/spljs/page.js)

##### **page.js**

+ 事件代理，代理需要局刷的URL, 绑定异步接口;
+ 前进后退控制， 使用pushState

提供接口

###### appPage.start()

```javascript
appPage.start(
	containerId: 'pager',   //pagelets渲染容器
	pagelets: 'pager',  	//请求的pagelet
	validateUrl: /.*/i, 	//符合这个规则的链接或者带data-href属性的元素进行事件代理
	cacheMaxTime: 1000		//每一个pagelet的缓存时间，视访问情况而定。
);
```
在这个设定下，A页面 -> B页面



| A 			  | B 				|
|:----------------|:----------------|
| {%widget name="xxxx" pagelet_id="pager"%} | {%widget name="oooo" pagelet_id="pager"%}|


两个都有相同的pagelet_id的widget，整页切换。

当然我们提供了`widget_block`来搞定这类问题。只需要在layout里面使用widget_block
其他页面extends即可。

```smarty
{%widget_block pagelet_id="pager"%}
	{%block name="body"%}{%/block%}
{%/widget_block%}
```

整个页面就这样切换起来了。

如果更新小范围的内容该如何办？

+ 只需在触发元素上添加`data-area`属性，


如;

```html
<a href="/xxxxx" data-area="left-bar">A</a>
```

当点击时回请求页面的`pagelet_id="left-bar"`的widget，并渲染到当前页面的`<div id="left-bar"></div>`内。

###### appPage.redirect()

```javascript
appPage.redirect(
	"/index/page/index",
	{
		"pagelets": "test"     // 需要请求的pagelet
		"containerId": "xxx"   // pagelet渲染的容器
	}
);
```

如果还有一些小的pagelet（widget）没有考虑到，可以用这个接口做加载。

#### 渲染模式

支持**普通渲染模式**和**pipeline**两种渲染模式。

+ 普通渲染模式`noscript`       没有被`pipeline`的渲染模式
+ pipeline渲染模式`pipeline`   如果需要比较完整的支持pagecache，可选择这种模式。

如何设定

```smarty
{%html ... mode="noscript"%} //noscript mode
```

or

```smarty
{%html ... mode="pipeline"%} //pipeline mode
```


[0]: https://github.com/xiangshouding/bigpipe.smarty "BigPipe.smarty"
[1]: https://github.com/xiangshouding/fis-smarty-bigpipe-plugin "quickling plugin"
[2]: https://github.com/xiangshouding/bigpipe.smarty/blob/master/lazyrender/static/BigPipe.js "loader"
[3]: https://github.com/zjcqoo/mod "modjs"
[4]: https://github.com/xiangshouding/bigpipe.smarty/blob/master/lazyrender/static/lazyload.js "lazyload.js"
[5]: https://github.com/xiangshouding/bigpipe.smarty/blob/master/single/common/static/lib/js/BigPipe.js "loader"
