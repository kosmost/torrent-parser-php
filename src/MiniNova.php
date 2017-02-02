<?php

namespace pxgamer\TorrentParser;

class MiniNova
{
    const BASE_URL = 'http://www.mininova.org';

    public static function search($search_query)
    {
        $search_query = urlencode($search_query);

        return self::get('/rss/'.$search_query);
    }

    public static function latest()
    {
        return self::get('/rss.xml');
    }

    public static function user($username)
    {
        $username = urlencode($username);

        return self::get('/rss.xml?user='.$username);
    }

    private static function get($endpoint = '/rss.xml')
    {
        $cu = curl_init();
        curl_setopt_array(
            $cu,
            [
                CURLOPT_URL => self::BASE_URL.$endpoint,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
            ]
        );
        $response = curl_exec($cu);
        $response = str_replace('&nbsp;', ' ', $response);
        $xml = simplexml_load_string($response);

        return self::xml2array($xml)['channel'][0]['item'];
    }

    private static function xml2array($xml)
    {
        $arr = array();

        foreach ($xml->children() as $r) {
            $t = array();
            if (count($r->children()) == 0) {
                $arr[$r->getName()] = strval($r);
            } else {
                $arr[$r->getName()][] = self::xml2array($r);
            }
        }

        return $arr;
    }
}