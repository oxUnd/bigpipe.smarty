var Cache = function() {
    /**
     * {
     *  <url>: {
     *      handle: <dom tree>,
     *      times: 0
     *  }
     * }
     * @type {{}}
     */
    this.collection = {};
};

Cache.prototype = {
    get: function (url) {
        return this.collection[url];
    },
    list: function () {
        return this.collection;
    },
    clear: function () {
        this.collection = {};
        return 0;
    },
    unset: function(url) {
        delete this.collection[url];
    },
    updateTimes: function(url) {
        var cache = this.get(url);
        if (cache) {
            cache.times ++;
            this.collection[url] = cache;
        }
    },
    save: function(url, obj) {
        var cache = {
            times: 1,
            handle: obj
        };
        this.collection[url] = cache;
    }
};

SplJs.cache = new Cache();