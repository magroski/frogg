<?php

namespace Frogg;

class CurlInterface
{

    const BASIC = 1;
    const SAFE  = 2;

    private $authType;
    private $authParam;

    public function __construct($authType = false, $authParam = false)
    {
        $this->authType  = $authType;
        $this->authParam = $authParam;
    }

    /**
     * Send a POST request with its data encoded as JSON
     *
     * @param string $url       Url to call
     * @param array  $data      Key-value array to be json_encoded
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function postJson(string $url, $data = [], $dataQuery = [])
    {
        $query = http_build_query($dataQuery);
        $ch    = curl_init($url.'?'.$query);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Send a POST request with its data as PARAMS
     * Ex: example.com/my-route/{param}/{param2}
     *
     * @param string $url       Url to call
     * @param array  $data      Simple array with params to be passed
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function postUrl(string $url, $data = [], $dataQuery = [])
    {
        $params = implode('/', $data);
        $query  = http_build_query($dataQuery);
        $ch     = curl_init($url.$params.'?'.$query);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Send a GET request with its data as PARAMS
     * Ex: example.com/route/{data}
     *
     * @param string $url       Url to call
     * @param array  $data      Simple array with params to be passed
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function getUrl(string $url, $data = [], $dataQuery = [])
    {
        $params = implode('/', $data);
        $query  = http_build_query($dataQuery);
        $ch     = curl_init($url.$params.'?'.$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Send a GET request with its data as QUERY
     * Ex: example.com/route?x=123&y=456
     *
     * @param string $url       Url to call
     * @param array  $data      Key-value array with params
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function getQuery(string $url, $data = [], $dataQuery = [])
    {
        $data = array_merge($data, $dataQuery);
        $ch   = curl_init($url.'?'.http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Send a DELETE request with its data as PARAMS
     *
     * @param string $url       Url to call
     * @param array  $data      Simple array with params to be passed
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function deleteReq(string $url, $data = [], $dataQuery = [])
    {
        $query  = http_build_query($dataQuery);
        $params = implode('/', $data);
        $ch     = curl_init($url.$params.'?'.$query);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Send a PUT request with its data as PARAMS
     *
     * @param string $url       Url to call
     * @param array  $data      Key-value array with params
     * @param array  $dataQuery Key-value array to be json_encoded
     *
     * @return string request result
     */
    public function putReq(string $url, $data = [], $dataQuery = [])
    {
        $query = http_build_query($dataQuery);
        $ch    = curl_init($url.'?'.$query);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($this->authType == self::BASIC) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        } else if ($this->authType == self::SAFE) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
            curl_setopt($ch, CURLOPT_USERPWD, $this->authParam);
        }
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}