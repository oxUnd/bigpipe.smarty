!function() {
    var Rules = function() {
        /**
         * [
         *  {
         *      'containerId': <string>,
         *      'pagetets': <array>,
         *      'route': <regexp>,
         *      'pageId': <string>
         *  }
         * ]
         * @type {{}}
         */
        this.rules = {};
    };

    Rules.prototype = {
        init: function (rules) {
            this.rules = rules || {};
        },
        getAll: function() {
            return this.rules;
        },
        addRule: function(reg, pagelets) {
            this.rules[reg] = pagelets;
        },
        clear: function() {
            this.rules = {};
        },
        match: function(url) {
            if (this.rules.length == 0) {
                return false;
            }
            for (var i = 0, len = this.rules.length; i < len; i++) {
                var rule = this.rules[i];
                if (Object.prototype.toString.apply(rule['route']) == '[Object RegExp]'
                    &&  rule['route'].test(url)) {
                    return rule;
                }
            }
            return false;
        }
    };

    SplJs.rules = new Rules();
}();