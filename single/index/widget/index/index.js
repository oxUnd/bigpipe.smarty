console.log("test");

require.async('./index.async.js', function(_) {
    _.echo("This is a test, I'm Async Compenent!");
});