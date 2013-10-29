{%extends file="common/page/layout.tpl"%}

{%block name="main"%}
    {%widget name="index:widget/list/list.tpl" pagelet_id="pagelet_list"%}
    {%widget name="index:widget/index/index.tpl" pagelet_id="pagelet_detail"%}
{%/block%}
