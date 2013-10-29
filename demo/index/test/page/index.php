<?php

$fis_data = array(
    'title' => 'INDEX',
    'messages' => array(
        array(
            'username' => 'Eric Ferraiuolo 0',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 1,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 1',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 2,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 1
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
        array(
            'username' => 'Eric Ferraiuolo 2',
            'user_avatar' => 'http://tp4.sinaimg.cn/1664362031/50/5661865127/1',
            'message_id' => 3,
            'message_content' => 'Hey, I had some feedback for pull request #51. We should center the menu so it looks better on mobile.',
            'message_status' => 0
        ),
    )
);

$message_data = array(
    '知道的你不知道的事情',
    '这只是一个测试的文字',
    '测试测试测试测试测试测试测试测试测试。。。',
    '假设这块是一些消息'
);

$message_id = isset($_GET['message_id']) ? $_GET['message_id'] : 1;

$current = array(
    'message_content' => $message_data[$message_id]
);

$fis_data['current'] = $current;
