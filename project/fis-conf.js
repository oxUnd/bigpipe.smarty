fis.config.merge({
    namespace: 'pagelet',
    roadmap: {
        domain: {
            '**.css': 'http://localhost:8080/'
        }
    },
    pack: {
        '/static/second.third.css': [
            '/widget/ui/msg/msg.css',
            '/widget/box/box.css',
            '/widget/third/third.css'
        ],
        '/static/second.js': [
            '/widget/ui/msg/msg.js'
        ],
        '/static/all.css': [
            '**.css'
        ],
        '/static/all.js': [
            '**.js'
        ]
    }
});
