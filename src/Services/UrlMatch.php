<?php
namespace App\Services;

class UrlMatch{

function areUrlsTheSame($url1, $url2):Bool
{
    $mustMatch = array_flip(['host', 'port', 'path']);
    $defaults = ['path' => '/']; // if not present, assume these (consistency)
    $url1 = array_intersect_key(parse_url($url1), $mustMatch);
    $url2 = array_intersect_key(parse_url($url2), $mustMatch);

    return $url1 === $url2;
}

}