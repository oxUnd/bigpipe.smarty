var App = function() {
    //是否绑定切换页面逻辑
    var enableProxy = null;
    //resourceMap cache
    var cache = {};
    //缓存时间
    var cacheMaxTime = 0;

    function init(params) {
        /**
         * 默认参数 {
         *      enableProxy: <function> //判断那些元素需要绑定切页逻辑
         *      cacheMaxTime: <integer> //缓存存活时间，默认10s
         * }
         * @type {{enableProxy: Function}}
         */
        var defaultParams = {
            enableProxy: function(e) {
                return e.target.tagName == 'A';
            },
            cacheMaxTime: 10000
        };
        params = merge(defaultParams, params);
        enableProxy = params.enableProxy;
        cacheMaxTime = params.cacheMaxTime;
        window.addEventListener('popstate', function(e){
            var state = e.state;
            if (state) {
                state.forword = false;
                redirect(state.referer, state);
            }
        }, false);
        BigPipe.on('pagerendercomplete', onPagerendered, this);    // 执行完页面的ready函数后触发
    }

    /**
     * 简单merge两个对象
     * @param _old
     * @param _new
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
     * @param et
     */
    function proxy(et) {
        if (enableProxy(et)) {
            var elm = et.target;
            if (elm.hasAttribute('data-href')) {
                var v = elm.getAttribute('data-area');
                et.stopPropagation();
                et.preventDefault();
                redirect(elm.getAttribute('data-href'), {
                    containerId: v,
                    pagelets: [v]
                });
            }
        }
    }

    function getCurrentPageUrl() {
        var href = window.location.href;
        return href;
    }

    function getUrlWithoutHash(url) {
        var href = url;
        if (href.indexOf('#') !== -1) {
            href = href.substr(0, href.indexOf('#'));
        }
        return href;
    }

    /**
     * 跳转页面
     * @param url
     * @param options
     */
    function redirect(url, options) {
        url = getUrlWithoutHash(url);
        var default_options = {
            pagelets: [],
            containerId: null,
            referer: getCurrentPageUrl(),
            forword: true,
            replace: false
        };
        options = merge(default_options, options);
        if (window.history.pushState) {
            if (options.forword) {
                if (options.replace) {
                    window.history.replaceState(options, null, url);
                } else {
                    window.history.pushState(options, null, url);
                }
            }
        }

        if (options.pagelets.length > 0) {
            var pagelets = [];
            for (var i = 0, len = options.pagelets.length; i < len; i++) {
                pagelets.push('pagelets[]=' + options.pagelets[i]);
            }
            url = (url.indexOf('?') == -1) ? url + '?' + pagelets.join('&') : url + '&' + pagelets.join('&');
        }
        var now = (new Date()).getTime();
        //page render start
        trigger('onPageRenderStart');
        if (cache[url] && now - cache[url].time <= cacheMaxTime) {
            BigPipe.onPagelets(cache[url]['resource'], options.containerId);
        } else {
            delete cache[url];
            BigPipe.refresh(url, options.containerId);
        }
    }

    function start() {
        var body = document.getElementsByTagName('body')[0];
        if (!body) {
            console || console.error("no element: 'body'!");
        }

        body.addEventListener('click', proxy, false);
    }

    function onPagerendered(obj) {
        if (cache[obj.url]) {
            cache[obj.url] = {
                resource: obj.resource,
                time: (new Date()).getTime()
            };
        }
        //page render end
        trigger('onPageRenderComplete')
    }

    // -------------------- 事件队列 --------------------
    var SLICE = [].slice;
    var events = {};

    function trigger(type /* args... */) {
        var list = events[type];
        if (!list) {
            return;
        }

        var arg = SLICE.call(arguments, 1);
        for(var i = 0, j = list.length; i < j; i++) {
            var cb = list[i];
            if (cb.f.apply(cb.o, arg) === false) {
                break;
            }
        }
    }

    function on(type, listener, context) {
        var queue = events[type] || (events[type] = []);
        queue.push({f: listener, o: context});
    }

    return {
        init: init,
        start: start,
        redirect: redirect,

        on: on,
        trigger: trigger
    };
}();