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
        '/static/msg.head.js': [
            '/widget/head/head.js'
        ],
        '/static/all.css': [
            '**.css'
        ]
    }
});
