<div id='third'>
    <p>
        third screen
    </p>
</div>
{%script%}
require.async('/widget/head/head.js', function(h) {
    h.hello();
});
{%/script%}