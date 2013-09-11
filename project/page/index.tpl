{%html%}
    {%head%}
        <title>test</title>
    {%/head%}
    {%body%}
        {%script%}
        alert('test');
        {%/script%}
        {%require name="pagelet:page/index.css"%}
        {%widget name="pagelet:widget/first/first.tpl"%}
        {%widget name="pagelet:widget/box/box.tpl" pagelet_id="second" mode="quickling"%}
        {%widget name="pagelet:widget/third/third.tpl" pagelet_id="third" mode="quickling"%}
    {%/body%}
{%/html%}
