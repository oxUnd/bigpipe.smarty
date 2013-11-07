<div class="pure-u-1" id="main">
    <div class="message-content">
        <div class="message-content-header pure-g">
            <div class="pure-u-1-2">
                <h1 class="message-content-title">Hello from Toronto</h1>
                <p class="message-content-subtitle">
                    From <a>Tilo Mitra</a> at <span>3:56pm, April 3, 2012</span>
                </p>
            </div>

            <div class="pure-u-1-2 message-content-controls">
                <button class="pure-button secondary-button">Reply</button>
                <button class="pure-button secondary-button">Forward</button>
                <button class="pure-button secondary-button">Move to</button>
            </div>
        </div>

        <div id="detail" class="message-content-body">
            {%$current.message_content%}
            {%widget name="index:widget/asyncload/asyncload.tpl" mode="quickling" pagelet_id="getit"%}
        </div>
    </div>
</div>
{%script%}
var elms = document.getElementsByClassName('g_fis_bigrender');
    for (var i = 0, len = elms.length; i < len; i++) {
    window['eval'] && window['eval'](elms[i].innerHTML);
}
{%/script%}
