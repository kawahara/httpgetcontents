httpgetcontent
==============

This is simple http client that use filegetcontent().

How to use
----------

    use HttpGetContent\Client;

    $clinet = new Client();
    $response = $client->get('http://labs.bucyou.net/ut/test.php?p=test');

    // 200
    echo $response->getCode();
    // 'get'
    echo $response->getContents();

    $response = $client->post('http://labs.bucyou.net/ut/test.php', array('p' => 'test'));

    // 200
    echo $response->getCode();
    // 'post'
    echo $response->getContents();

And, you can use proxy by Client::setProxy();

Enjoy.
