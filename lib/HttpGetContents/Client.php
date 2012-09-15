<?php

namespace HttpGetContents;

class Client
{
    protected
        $currentResponse = null,
        $timeout = 10,
        $proxy = null;

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($time) {
        $this->timeout = $time;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setProxy($proxy) {
        $this->proxy = $proxy;
    }

    protected function getStreamContext($url, $parameter, $method)
    {
        $httpConf = array(
            'method' => $method,
            'request_fulluri' => true,
            'ignore_errors'   => true,
            'timeout'         => $this->timeout
        );

        $headers = array();

        if ($method === 'POST') {
            if (count($parameter) >= 1) {
                $contents = http_build_query($parameter);
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $headers[] = 'Content-Length: '.strlen($contents);
                $httpConf['content'] = $contents;
            }
        }

        $httpConf['header'] = implode("\r\n", $headers)."\r\n";

        if ($this->proxy !== null) {
            $httpConf['proxy'] = $httpConf['proxy'] = preg_replace(
                array('/^http:/', '/^https:/'),
                array('tcp:', 'ssl:'),
                $this->proxy
            );
        }

        return stream_context_create(array('http' => $httpConf));
    }

    protected function parseHeader($responseHeader)
    {
        $code = null;
        $headers = array();
        if (isset($responseHeader) && is_array($responseHeader)) {
            foreach ($responseHeader as $header) {
                $token = explode(' ', $header);
                if (0 === strpos($token[0], 'HTTP/')) {
                    $headers = array();
                    $code = $token[1];
                }

                $headers[] = $header;
            }
        }

        if ($code === null) {

            throw new \RuntimeException("couldn't accept correct http response");
        }

        return array('code' =>  $code, 'headers' => $headers);
    }

    public function get($url, $parameter = array())
    {
        $context = $this->getStreamContext($url, $parameter, 'GET');

        $requestUrl  = $url;
        if (count($parameter)) {
            $requestUrl .= false === strpos($requestUrl, '?') ? '?' : '&';
            $requestUrl .= http_build_query($parameter);
        }

        $contents = @file_get_contents($requestUrl, false, $context);
        $headerInfo = $this->parseHeader($http_response_header);

        $this->currentResponse = new Response(
            $url,
            $parameter,
            $headerInfo['code'],
            $headerInfo['headers'],
            $contents
        );

        return $this->currentResponse;
    }

    public function post($url, $parameter = array())
    {
        $context = $this->getStreamContext($url, $parameter, 'POST');
        $contents = @file_get_contents($url, false, $context);
        $headerInfo = $this->parseHeader($http_response_header);

        $this->currentResponse = new Response(
            $url,
            $parameter,
            $headerInfo['code'],
            $headerInfo['headers'],
            $contents
        );

        return $this->currentResponse;
    }
}
