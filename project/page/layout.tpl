{%html framework="pagelet:static/mod.js"%}
{%head%}
<title>{%$title%}</title>
<script type="text/javascript" src="/static/lazyload.js"></script>
<script type="text/javascript" src="/static/BigPipe.js"></script>
<script type="text/javascript">
    window.onload = function() {
        var elms = document.getElementsByClassName('g_fis_bigrender');
        for (var i = 0, len = elms.length; i < len; i++) {
            window['eval'] && window['eval'](elms[i].innerHTML);
        }
        var elms = document.getElementsByTagName('a');
        for (var i = 0, len = elms.length; i < len; i++) {
            var elm = elms[i];
            console.log(elm.getAttribute('data-href'));
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
{%/head%}
{%body%}
    <div>
        <ul>
            <li><a data-href="/pagelet/page/index" data-area="pager">INDEX</a></li>
            <li><a data-href="/pagelet/page/about" data-area="pager">ABOUT</a></li>
        </ul>
    </div>
    <div id="pager">
        {%block name="body"%}{%/block%}
    </div>
{%/body%}
{%/html%}
