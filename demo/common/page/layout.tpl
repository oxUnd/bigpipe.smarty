{%html framework="common:static/lib/js/mod.js"%}
{%head%}
    {%title%}{%$title%}{%/title%}
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
                        BigPipe.refresh(link + '?pagelets=' + id, id);
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
<div class="content">
    <div class="header">
        <div class="pure-menu pure-menu-open pure-menu-horizontal">
            <ul>
                <li><a data-href="/index/page/index" data-area="pager">Home</a></li>
                <li><a data-href="/index/page/about" data-area="pager">Flickr</a></li>
                <li><a data-href="#">Messenger</a></li>
                <li><a data-href="#">Sports</a></li>
                <li><a data-href="#">Finance</a></li>
            </ul>
        </div>
    </div>

    <div class="splash">
        <div class="pure-g-r">
            <div class="pure-u-1-3">
                <div class="l-box splash-image">
                    <img src="http://placehold.it/500x350"
                         alt="Placeholder image for example.">
                </div>
            </div>

            <div class="pure-u-2-3">
                <div class="l-box splash-text">
                    <h1 class="splash-head">
                        Some big bold text.
                    </h1>

                    <h2 class="splash-subhead">
                        The HTML and CSS for this layout show how you can make a modern, responsive landing page for your next product. Browse through the source to see how we use menus and responsive grids to create this layout. Shrink your browser width and watch the layout transform and play nice with small screens.
                    </h2>

                    <p>
                        <a href="#" class="pure-button primary-button">Get Started</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div id="pager" class="content">
        {%block name="content"%}{%/block%}
    </div>

    <div class="footer">
        View the source of this layout to learn more. Made with love by the YUI Team.
    </div>
</div>
{%/body%}
{%/html%}
