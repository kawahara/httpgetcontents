<?php

use HttpGetContent\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testGet() {
        $client = new Client();
        $response = $client->get('http://labs.bucyou.net/ut/test.php?p=test');

        $this->assertEquals('get', $response->getContents());
    }

    public function testPost() {
        $client = new Client();
        $response = $client->post('http://labs.bucyou.net/ut/test.php', array('p' => 'test'));

        $this->assertEquals('post', $response->getContents());
    }
}
