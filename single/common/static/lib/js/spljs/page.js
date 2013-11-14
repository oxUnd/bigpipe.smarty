(function(w, undefined) {
    var exports = w,
        cache = {},         // resourceMap cache
        cacheMaxTime = 0,   // 缓存时间
        appOptions = {},    // app页面管理的options
        curPageUrl,
        isPushState,
        urlReg = /^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/i;

    /**
     * 启动页面管理
     * @param  {Object} options 初始化参数
     * @param {String} options["selector"] 全局代理元素的选择器匹配，写法同 document.querySeletor 函数
     * @param {Number} options["cacheMaxTime"] 页面缓存时间
     * @param {Function|RegExp} options["validate"] url验证方法，
     * @return {void}
     */

    function start(options) {
        console.log("start")
        /**
         * 默认参数 {
         *     selector : <string> // 代理元素的选择器规则
         *     cacheMaxTime: <integer> //缓存存活时间，默认5min
         * }
         */
        var defaultOptions = {
            selector: "a,[data-href]",
            cacheMaxTime: 5 * 60 * 1000,
            pushState : true
        };

        appOptions = merge(defaultOptions, options);
        cacheMaxTime = appOptions.cacheMaxTime;
        isPushState = appOptions.pushState;

        curPageUrl = getCurrentUrl();

        // 绑定事件
        bindEvent();
    }

    /**
     * 事件绑定
     * @return {void}
     */

    function bindEvent() {
        // 处理history.back事件
        window.addEventListener('popstate', onPopState, false);
        // 全局接管指定元素点击事件
        document.body.addEventListener('click', proxy, false);
        // bigpipe回调事件
        BigPipe.on('pagerendercomplete', onPagerendered, this); // 执行完页面的ready函数后触发
    }


    /**
     * 处理popstate事件，响应历史记录返回
     * @param  {PopStateEvent} e popstate事件对象
     * @return {void}
     */

    function onPopState(e) {

        var currentUrl = getCurrentUrl(),
            pageUrl;

        if (!curPageUrl || currentUrl === curPageUrl) {
            return;
        }
        fetchPage(currentUrl, e.state);
    }

    /**
     * 渲染完成事件函数
     * @param  {String} obj bigpipe回传事件参数
     * @return {void}
     */

    function onPagerendered(obj) {
        cache[obj.url] = {
            resource: obj.resource,
            time: Date.now()
        };
        console.log("%cregister cache", "color:red;font-size:16px;", cache);
        //page render end
        trigger('onPageRenderComplete',{
            url : obj.url
        });
    }


    /**
     * 简单merge两个对象
     * @param {object} _old
     * @param {object} _new
     * @returns {*}
     */

    function merge(_old, _new) {
        for (var i in _new) {
            if (_new.hasOwnProperty(i)) {
                _old[i] = _new[i];
            }
        }
        return _old;
    }

    /**
     * 事件代理
     * @param {MouseEvent} 点击事件对象
     */

    function proxy(e) {
        var element = e.target,
            parent = element,
            selector = appOptions.selector;

        console.log("proxy", element, e);

        while (parent !== document.body) {

            if (matchSelector(parent, selector)) {

                urlAttr = parent.tagName.toLowerCase() === "a" ? "href" : "data-href";
                url = parent.getAttribute(urlAttr);

                // 验证url, 可以自行配置url验证规则
                if (validateUrl(url)) {
                    // debugger;

                    e.stopPropagation();
                    e.preventDefault();

                    var opt = {
                        replace: parent.getAttribute("data-replace") || false,
                        containerId: parent.getAttribute("data-area"),
                        pagelets: parent.getAttribute("data-area")
                    }

                    redirect(url, opt);
                }
                return;
            } else {
                parent = parent.parentNode;
            }
        }
    }

    /**
     * 检查元素是否匹配选择器
     * @param  {HTMLElement} element
     * @param  {String} selector 选择器规则
     * @return {boolean}
     */

    function matchSelector(element, selector) {
        if (!element || element.nodeType !== 1) {
            return false
        }

        var parent,
            match,
            matchesSelector = element.webkitMatchesSelector || element.matchesSelector;

        if (matchesSelector) {
            match = matchesSelector.call(element, selector)
        } else {
            parent = element.parentNode;
            match = !! parent.querySelector(selector);
        }

        return match;
    }

    /**
     * 验证URL是否符合validate规则
     * @param  {string} url
     * @return {boolean}
     */

    function validateUrl(url) {
        var validate = appOptions.validate,
            type = Object.prototype.toString.call(validate);

        if (type === "[object RegExp]") {
            return validate.test(url);
        } else if (type === "[object Function]") {
            return validate(url);
        } else {
            return true;
        }
    }

    /**
     * 获取url的pathname 和 query部分
     * @param  {String} url
     * @return {String}     返回url的pathname 和 query部分
     */

    function getUrl(url) {
        if (urlReg.test(url)) {
            return RegExp.$5 + (RegExp.$6 ? RegExp.$6 : "");
        } else {
            "console" in window && console.error("[url error]:", url);
        }

    }

    /**
     * 获取当前的url
     * @return {String} 获取当前url
     */

    function getCurrentUrl() {
        return getUrl(window.location.href)
    }

    /**
     * 跳转页面
     * @param {String} url      目标页面的url
     * @param {Object} options  跳转配置参数
     * @param {Array|String} options[pagelets]  请求的pagelets
     * @param {String} options[containerId]  pagelets渲染容器
     * @param {Boolean} options[trigger]  是否触发加载
     * @param {Boolean} options[forword]  是否替换URL
     * @param {Boolean} options[replace]  是否替换当前历史记录
     * @param {HTMLElement} options[target]  触发跳转的DOM元素
     */

    function redirect(url, options) {
        url = getUrl(url);
        var method,
            defaultOptions = {
                trigger: true,
                forword: true,
                replace: false
            },
            eventsOptions = {
                url : url
            };


        options = merge(defaultOptions, options);
        eventsOptions.target = options.target || null;
        if (!isPushState) {
            options.replace ? (location.href = url) : (location.replace(url));
            return;
        }

        //page render start
        trigger('onPageRenderStart' , eventsOptions);

        // 之所以放在页面回调中替换历史记录，是因为在移动端低网速下
        // 有可能后续页面没有在下一次用户操作前返回，而造成添加无效历史记录的问题
        fetchPage(url, options, function(){
            if (options.forword) {
                method = options.replace ? "replaceState" : "pushState";
                window.history[method](options, document.title, url);
            }
        });
    }

    function fetchPage (url, options, callback){
        if(!url) {
            return;
        }
        var now = Date.now(),
            pageletsParams = [],
            containerId = options.containerId ? options.containerId : appOptions.containerId,
            pagelets = options.pagelets ? options.pagelets : appOptions.pagelets;

        if(typeof pagelets === "string" ) {
            pagelets = [pagelets]
        }

        curPageUrl = url;

        if (pagelets.length > 0) {
            for (var i = 0, len = pagelets.length; i < len; i++) {
                pageletsParams.push('pagelets[]=' + pagelets[i]);
            }
            url = (url.indexOf('?') == -1) ? url + '?' + pageletsParams.join('&') : url + '&' + pageletsParams.join('&');
        }

        BigPipe.refresh(url, containerId, function(){
            callback && callback();
        })
    }

    // -------------------- 事件队列 --------------------
    var SLICE = [].slice;
    var events = {};

    function trigger(type /* args... */ ) {
        var list = events[type];
        if (!list) {
            return;
        }

        var arg = SLICE.call(arguments, 1);
        for (var i = 0, j = list.length; i < j; i++) {
            var cb = list[i];
            if (cb.f.apply(cb.o, arg) === false) {
                break;
            }
        }
    }

    function on(type, listener, context) {
        var queue = events[type] || (events[type] = []);
        queue.push({
            f: listener,
            o: context
        });
    }

    exports.appPage = {
        start: start,
        redirect: redirect,
        on: on
    };

    // 模块化支持
    if ("define" in window && typeof module != "undefined") {
        module.exports = exports.appPage
    }

})(this);
