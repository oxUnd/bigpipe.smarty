fis.config.merge({
    namespace: 'pagelet',
    roadmap: {
        domain: {
            '**.css': 'http://localhost:8080/'
        }
    },
    pack: {
        '/static/second.css': [
            '/widget/ui/msg/msg.css',
            '/widget/box/box.css'
        ],
        '/static/second.js': [
            '/widget/ui/msg/msg.js'
        ]
    }
});
