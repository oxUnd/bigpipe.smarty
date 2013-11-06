<div class="pure-u-1" id="list">
    {%foreach $messages as $item%}
        {%$message_status="message-item-unread"%}
        {%if $item.message_status == 1%}
            {%$message_status=""%}
        {%/if%}
    <div class="message-item {%$message_status%} {%if $item@index == 0%}message-item-selected{%/if%} pure-g" data-href="/index/page/index?message_id={%$item.message_id%}" data-area="pagelet_detail">
        <div class="pure-u">
            <img class="message-avatar" alt="Tilo Mitra's avatar" src="{%$item.user_avatar%}" height="64" width="64">
        </div>

        <div class="pure-u-3-4">
            <h5 class="message-name">{%$item.username%}</h5>
            <h4 class="message-subject">Hello from Toronto</h4>
            <p class="message-desc">
                {%$item.message_content%}
            </p>
        </div>
    </div>
    {%/foreach%}
    {%script%}
        SplJs.start({targets:['.message-item']});
        $('.message-item').click(function() {
            $('.message-item').removeClass('message-item-selected');
            $(this).addClass('message-item-selected');
            $(this).removeClass('message-item-unread');
        });

    {%/script%}
</div>