<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 06.06.2018
 * Time: 11:15
 */

namespace Core;


class PLoSMetricsParser extends ApiParser
{
    protected static $endpoint = 'http://alm.plos.org/api/v5/articles?ids=';
    protected static $source = Utils::PLOS_SRC;

    /**
     * @param String $doi
     * @return array
     */
    public static function loadByDOI(String $doi): array
    {
        $load = parent::loadByDOI($doi);
        $newList = [];
        foreach ($load['data']['data'][0]['sources'] as $source){
            $newList[$source['display_name']] = [
                'value' => $source['metrics']['total']
            ];
        }
        $load['data']['data'] = $load['data']['data'][0];
        unset($load['data']['data'][0]);
        $load['data']['data']['sources'] = $newList;
        return $load;
    }

}