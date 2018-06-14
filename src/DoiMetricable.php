<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 05.06.2018
 * Time: 19:53
 */

namespace Core;


interface DoiMetricable
{
    /**
     * @param String $doi
     * @return array
     */
    public static function loadByDOI(String $doi): array;

}