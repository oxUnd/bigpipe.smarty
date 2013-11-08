!function(){
    var Path = function() {
    };
    Path.prototype = {
        getCurPageUrl: function() {
            var href = window.location.href;
            return href;
        },
        getUrlWithoutHash: function(url) {
            var href = url;
            if (!href) {
                href = this.getCurPageUrl();
            }
            if (href.indexOf('#') !== -1) {
                href = href.substr(0, href.indexOf('#'));
            }
            return href;
        }
    };
    SplJs.path = new Path;
}();