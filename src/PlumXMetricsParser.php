<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 06.06.2018
 * Time: 10:08
 */

namespace Core;


class PlumXMetricsParser extends ApiParser
{
    public static $endpoint = 'https://api.plu.mx/widget/other/artifact?type=doi&id=';
    protected static $source = Utils::PLUMX_SRC;
    public const API_KEY = 'f50d091a-dc7b-ca0f-0552-24e7-04e208ae0a6c';

    /**
     * @param String $doi
     * @return String
     */
    public static function getApiUrlByDOI(String $doi): String
    {
        if (strpos('pageToken', self::$endpoint) === false) {
            self::$endpoint .= $doi . '&pageToken=' . self::API_KEY;
        }
        return parent::getApiUrlByDOI($doi);
    }

    /**
     * @param String $doi
     * @return array
     */
    public static function loadByDOI(String $doi): array
    {
        $load = parent::loadByDOI($doi);

        $replacer = function (array $sources) {
            $newList = [];
            foreach ($sources as $i => $source) {
                if (!isset($newList[$source['source']])) {
                    $newList[$source['source']] = [
                        'value' => $source['count'],
                        'label' => $source['label'],
                    ];
                    if (isset($source['link'])) {
                        unset($newList[$source['source']]['link']);
                        unset($newList[$source['source']]['hasLink']);
                        $newList[$source['source']]['links'][] = $source['link'];
                    }
                    continue;
                }
                $newList[$source['source']]['value'] += $source['count'];
                if (strpos($newList[$source['source']]['label'], $source['label']) === false) {
                    $newList[$source['source']]['label'] .= ' ' . $source['label'];
                }
                if (isset($source['link'])) {
                    $newList[$source['source']]['links'][] = $source['link'];
                }
            }
            return $newList;
        };
        $newList = [];
        foreach ($load['data']['statistics'] as $category => $sources) {
            $newList[$category] = $replacer($sources);
        }
        $load['data']['statistics'] = $newList;
        return $load;
    }
}