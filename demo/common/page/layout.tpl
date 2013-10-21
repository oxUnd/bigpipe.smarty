{%html framework="common:static/lib/js/mod.js"%}
{%head%}
    {%title%}{%$title%}{%/title%}
    <meta charset="utf-8" />
    <script type="text/javascript">
        window.onload = function() {
            var elms = document.getElementsByClassName('g_fis_bigrender');
            for (var i = 0, len = elms.length; i < len; i++) {
                window['eval'] && window['eval'](elms[i].innerHTML);
            }
            var elms = document.getElementsByTagName('a');
            for (var i = 0, len = elms.length; i < len; i++) {
                var elm = elms[i];
                if (elm.getAttribute('data-href')) {
                    elm.onclick = function() {
                        var id = this.getAttribute('data-area');
                        var link = this.getAttribute('data-href');
                        console.log([link, id]);
                        BigPipe.refresh(link, id);
                    };
                }
            }
        };
    </script>

    {%require name="common:static/lib/css/base.css"%}
    {%require name="common:static/lib/js/lib.js"%}
    {%require name="common:static/css/layout.css"%}

{%/head%}

{%body%}

    <div class="pure-g-r content" id="layout">
        <div class="pure-u" id="nav">
            <a href="#" class="nav-menu-button">Menu</a>

            <div class="nav-inner">
                <button class="pure-button primary-button">Compose</button>

                <div class="pure-menu pure-menu-open">
                    <ul>
                        <li><a data-href="/index/page/index?pagelets[]=pagelet_list&pagelets[]=pagelet_detail" data-area="pager">Inbox <span class="email-count">(2)</span></a></li>
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
        <div id="pager">
            {%block name="main"%}{%/block%}
        </div>
    </div>
{%/body%}
{%/html%}
