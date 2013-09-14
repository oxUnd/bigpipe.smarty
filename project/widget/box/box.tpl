<div id="box">
    This is a test
    {%widget name="pagelet:widget/box/box_child/box_child.tpl"%}
</div>
{%script%}
require('/widget/ui/msg/msg.js');
require.async('/widget/head/head.js', function(h) {
    h.hello();
});
{%/script%}
