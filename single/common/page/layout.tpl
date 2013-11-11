{%html framework="common:static/lib/js/mod.js"%}
{%head%}
    {%title%}{%$title%}{%/title%}
    <meta charset="utf-8" />

    {%require name="common:static/lib/css/base.css"%}
    {%require name="common:static/lib/js/lib.js"%}
    {%require name="common:static/lib/js/BigPipe.js"%}
    {%require name="common:static/css/layout.css"%}
    {%require name="common:static/lib/js/spljs/page.js"%}
    {%script%}
        App.init({
            enableProxy: function(event) {
                var cls = ['message-item'];
                var tags = ['A'];
                function matchClassName(elm) {
                    for (var i = 0, len = cls.length; i < len; i++) {
                        if (elm.className && elm.className.indexOf(cls[i]) != -1) {
                            return true;
                        }
                    }
                    return false;
                }

                function matchTagName(elm) {
                    for (var i = 0, len = tags.length; i < len; i++) {
                        if (elm.tagName == tags[i]) {
                            return true;
                        }
                    }
                    return false;
                }
                return matchTagName(event.target) || matchClassName(event.target);
            }
        });
        App.start();
    {%/script%}
{%/head%}

{%body%}

    <div class="pure-g-r content" id="layout">
        <div class="pure-u" id="nav">
            <a href="#" class="nav-menu-button">Menu</a>

            <div class="nav-inner">
                <button class="pure-button primary-button">Compose</button>

                <div class="pure-menu pure-menu-open">
                    <ul>
                        <li><a data-href="/index/page/index" data-area="pager">Inbox <span class="email-count">(2)</span></a></li>
                        <li><a href="#">Important</a></li>
                        <li><a href="#">Sent</a></li>
                        <li><a href="#">Drafts</a></li>
                        <li><a href="#">Trash</a></li>
                        <li class="pure-menu-heading">Labels</li>
                        <li><a href="#"><span class="email-label-personal"></span>Personal</a></li>
                        <li><a href="#"><span class="email-label-work"></span>Work</a></li>
                        <li><a href="#"><span class="email-label-travel"></span>Travel</a></li>
                    </ul>
                </div>
            </div>
        </div>
        {%widget_block pagelet_id="pager"%}
            {%block name="main"%}{%/block%}
        {%/widget_block%}
    </div>
{%/body%}
{%/html%}
