!function() {
    var Rules = function() {
        /**
         * {
         *  <reg string>: {
         *      'containerId': <string>,
         *      'pagetets': <array>
         *  }
         * }
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
            for (var i in this.rules) {
                if (this.rules.hasOwnProperty(i)) {
                    var reg = new RegExp(i, 'i');
                    if (reg.test(url)) {
                        return this.rules[i];
                    }
                }
            }
            return false;
        }
    };

    SplJs.rules = new Rules();
}();