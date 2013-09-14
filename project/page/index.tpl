{%html framework="pagelet:static/mod.js"%}
    {%head%}
        <title>test</title>
        <script type="text/javascript" src="/static/lazyload.js"></script>
        <script type="text/javascript" src="/static/BigPipe.js"></script>
    {%/head%}
    {%body%}
        {%style%}
        body {
            background-color: red;
        }
        {%/style%}
        {%script%}
        alert('test');
        {%/script%}

        {%require name="pagelet:page/index.css"%}
        {%widget name="pagelet:widget/first/first.tpl"%}
        {%widget name="pagelet:widget/box/box.tpl" pagelet_id="second" mode="quickling"%}
        {%widget name="pagelet:widget/third/third.tpl" pagelet_id="third" mode="quickling"%}
    {%/body%}
{%/html%}
