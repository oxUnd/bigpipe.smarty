{%extends file="common/page/layout.tpl"%}

{%block name="main"%}
    {%widget name="index:widget/list/list.tpl" pagelet_id="pagelet_list"%}
    {%widget name="index:widget/index/index.tpl" pagelet_id="pagelet_detail"%}
    {%widget name="index:widget/asyncload/asyncload.tpl" mode="quickling" pagelet_id="getit1" group="a"%}
    {%widget name="index:widget/asyncload1/asyncload1.tpl" mode="quickling" pagelet_id="getit2" group="a"%}
    {%script%}
    var elms = document.getElementsByClassName('g_fis_bigrender');
        for (var i = 0, len = elms.length; i < len; i++) {
        window['eval'] && window['eval'](elms[i].innerHTML);
    }
    {%/script%}
{%/block%}
