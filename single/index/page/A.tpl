{%extends file="common/page/layout_simple.tpl"%}
{%block name="main"%}

<script>
function fetch() {
    BigPipe.fetch('/index/page/B?pagelets[]=b', "B");
}
</script>

<p>
    我是A页面，你可以点击按钮，获取B页面的局部页面，填充到红色区域。<button type="button" onclick="fetch();">获取B页面局部</button>
</p>

<div id="B" style="background-color: red; width: 400px; height: 200px;"></div>

{%/block%}