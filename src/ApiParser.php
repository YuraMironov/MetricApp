<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 05.06.2018
 * Time: 19:52
 */

namespace Core;
require_once ('DoiMetricable.php');

abstract class ApiParser implements DoiMetricable
{
    protected static $endpoint = '';
    protected static $source;

    /**
     * @param String $doi
     * @return array
     */
    public static function loadByDOI(String $doi): array
    {
        $data = file_get_contents(self::getApiUrlByDOI($doi));
        if ($data === false || isset(json_decode($data, true)['error_code'])) {
            $data = [
                'status' => false,
                'message' => 'doi_not_exist',
                'source' => static::$source,
            ];
        } else {
            $data = [
                'status' => true,
                'data' => json_decode($data, true)
            ];
        }
        return $data;
    }

    /**
     * @param String $doi
     * @return String
     */
    protected static function getApiUrlByDOI(String $doi): String
    {
        return static::$endpoint . $doi;
    }

}