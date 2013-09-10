{%html%}
  {%head%}
  {%/head%}
  {%body%}

    {%*first screen start*%}

      {%widget name="demo:widget/head.tpl"%}
      {%style%}
        @import url('/widget/head.css?__inline');
      {%/style%}
      {%script%}
        __inline('/widget/head.js?__inline');
      {%/script%}

    {%*first screen end*%}

    {%pagelet id="section_1" method="bigrender"%}
         <div class="section_1">
            test
            test
            test
        </div>
        {%widget name="demo:widget/section_1.tpl"%}
    {%/pagelet%}

    {%pagelet id="section_2" method="bigrender"%}
        <div class="section_2">
            test
            test
            test
        </div>
        {%widget name="demo:widget/section_2.tpl"%}
    {%/pagelet%}

    {%widget name="demo:widget/foot.tpl"%}

  {%/body%}
{%/html%}
