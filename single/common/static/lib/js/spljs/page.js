SplJs.init = function(rules) {
    SplJs.rules.init(rules);
    window.addEventListener('popstate', function(e){
        var state = e.state;
        if (state) {
            state.forword = false;
            SplJs.redirect(state.referer, state);
        }
    }, false);
};

/**
 * options = {
 *  pagelets: []
 *  containerId: '',
 *  referer: <url>,
 *  forword: true or false
 * }
 * @param url
 * @param options
 */
SplJs.redirect = function(url, options) {
    var default_options = {
        pagelets: [],
        containerId: null,
        referer: SplJs.path.getCurPageUrl(),
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

    BigPipe.refresh(url, options.containerId);
};

SplJs.start = function(params) {
    var default_params = {
        targets: ['a']
    };

    params = merge(default_params, params);

    //不支持pushState，直接跳过
    if (!window.history.pushState) return;
    for (var k = 0, target_count = params.targets.length; k < target_count; k++) {
        var links = $(params.targets[k]);
        for (var i = 0, len = links.length; i < len; i++) {
            var link = $(links[i]);
            if (!link.attr('onclicked')) {
                link.attr('onclicked', 'true');
                link.click(function(e) {
                    if (this.hasAttribute('data-href')) {
                        var v = this.getAttribute('data-area');
                        SplJs.redirect(this.getAttribute('data-href'), {
                            containerId: v,
                            pagelets: [v]
                        });
                        e.preventDefault();
                    } else if (this.hasAttribute('href')) {
                        var url = this.getAttribute('href');
                        var options = SplJs.rules.match(url);
                        if (options) {
                            SplJs.redirect(url, {
                                containerId: options.containerId,
                                pagelets: options.pagelets
                            });
                            e.preventDefault();
                        }
                    }
                });
            }
        }
    }
};

