var BigPipe = function() {

    function ajax(url, cb, data) {
        var xhr = new XMLHttpRequest;
        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                cb(this.responseText);
            }
        };
        xhr.open(data?'POST':'GET', url, true);
        if (data) xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(data);
    }


    function renderPagelet(obj, pageletsMap, rendered) {
        if (obj.id in rendered) {
            return;
        }
        rendered[obj.id] = true;

        if (obj.parent_id) {
            renderPagelet(
                pageletsMap[obj.parent_id], pageletsMap, rendered);
        }

        //
        // 将pagelet填充到对应的DOM里
        //
        var dom = document.getElementById(obj.id);
        if (!dom) {
            dom = document.createElement('div');
            dom.id = obj.id;
            document.body.appendChild(dom);
        }
        dom.innerHTML = obj.html;
    }


    function render(pagelets) {
        var i, n = pagelets.length;
        var pageletsMap = {};
        var rendered = {};

        //
        // 初始化 pagelet.id => pagelet 映射表
        //
        for(i = 0; i < n; i++) {
            var obj = pagelets[i];
            pageletsMap[obj.id] = obj;
        }

        for(i = 0; i < n; i++) {
            renderPagelet(pagelets[i], pageletsMap, rendered);
        }
    }


    function process(data) {
        var rm = data.resource_map;

        if (rm.async) {
            require.resourceMap(rm.async);
        }
        if (rm.js) {
            LazyLoad.js(rm.js, function() {
                rm.script && window.eval(rm.script);
            });
        }
        else {
            rm.script && window.eval(rm.script);
        }

        if (rm.css) {
            LazyLoad.css(rm.css);
        }

        if (rm.style) {
            var dom = document.createElement('style');
            dom.innerHTML = rm.style;
            document.getElementsByTagName('head')[0].appendChild(dom);
        }

        render(data.pagelets);
    }


    function asyncLoad(arg) {
        if (!(arg instanceof Array)) {
            arg = [arg];
        }
        var obj, arr = [];
        for (var i = arg.length - 1; i >= 0; i--) {
            obj = arg[i];
            if (!obj.id) {
                throw new Error('missing pagelet id');
            }
            arr.push('pagelets[]=' + obj.id);
        }

        var url = location.href.split('#')[0] + (location.search? '&' : '?') + arr.join('&');

        //test ajax no debug's `mode=`
        url=url.replace(/mode=\d*&/, '');

        ajax(url, function(res) {
            var data = window.JSON?
                JSON.parse(res) :
                eval('(' + res + ')');

            process(data);
        });
    }

    return {
        asyncLoad: asyncLoad
    }
}();


//test +lazyload.js
//document.write('<script src="lazyload.js"></script>');

//test 执行textarea的内容
window.onload = function() {
    var els = document.getElementsByTagName('textarea');
    for(var i = 0; i < els.length; i++) {
        window.eval(els[i].value);
    }
}
