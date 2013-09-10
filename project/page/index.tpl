{%html%}
    {%head%}
        <title>test</title>
    {%/head%}
    {%body%}
        
        {%require name="pagelet:page/index.css"%}
        {%widget name="pagelet:widget/first/first.tpl"%}
        {%widget name="pagelet:widget/box/box.tpl" pagelet_id="second"%}
        {%widget name="pagelet:widget/third/third.tpl" pagelet_id="third"%}
    {%/body%}
{%/html%}
