<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 05.06.2018
 * Time: 19:40
 */
namespace Core;
use Symfony\Component\HttpFoundation\RedirectResponse;

require_once('AltmetricMetricsParser.php');


class Utils
{
    public const ALTMETRIC_SRC = 0;
    public const PLUMX_SRC = 1;
    public const PLOS_SRC = 2;
    public const SOURCES_NAMES = [
        self::ALTMETRIC_SRC => 'altmetric.com',
        self::PLUMX_SRC => 'plu.mx',
        self::PLOS_SRC => 'plos.org',
    ];
    private static $config = null;
    /**
     * @param String $msg
     */
    public static function back(String $msg = 'not_doi'){
        (new RedirectResponse("/?error=" . $msg))->send();
    }

    /**
     * @param String $doi
     * @return array
     */
    public static function dataLoading(String $doi): array
    {
        $result = [
            'result' => false,
            'errors' => false,
            'sources' => [],
        ];
        self::readParseData($result, Utils::ALTMETRIC_SRC, AltmetricMetricsParser::loadByDOI($doi));
        self::readParseData($result, Utils::PLUMX_SRC, PlumXMetricsParser::loadByDOI($doi));
        self::readParseData($result, Utils::PLOS_SRC, PLoSMetricsParser::loadByDOI($doi));
        return $result;
    }

    /**
     * @param array $loadedData
     * @return array
     */
    public static function getRenderDataByLoadedData(array $loadedData) : array
    {
        $renderData = [];
        foreach (Utils::SOURCES_NAMES as $srcId => $srcName) {
            if (isset($loadedData['errors'][$srcId])) {
                continue;
            }
            switch ($srcId) {
                case Utils::ALTMETRIC_SRC:
                    if (!isset($renderData['title'])) {
                        $renderData['title'] = $loadedData['sources'][$srcId]['title'];
                    }
                    break;

                case Utils::PLUMX_SRC:
                    if (!isset($renderData['title'])) {
                        $renderData['title'] = $loadedData['sources'][$srcId]['name'];
                    }
                    break;

                case Utils::PLOS_SRC:
                    if (!isset($renderData['title'])) {
                        $renderData['title'] = $loadedData['sources'][$srcId]['data']['title'];
                    }
                    break;
            }
            self::unionRenderData($renderData, $srcId, $loadedData);
        }
        return $renderData;
    }

    /**
     * @param array $output
     * @param int $src
     * @param array $data
     */
    private function readParseData(array &$output, int $src, array $data) : void
    {
        if ($data['status']){
            $output['result'] = true;
            $output['sources'][$src] = $data['data'];
        } else {
            $output['errors'][$src] = $data['message'];
        }
    }

     /**
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config === null) {
            self::$config = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'config/config.json'), true);
        }
        return self::$config;
    }

    /**
     * @param String $field
     * @param array $data
     * @param int $srcId
     * @param bool $url
     * @return mixed
     */
    private static function getValueByConfigPath(String $field, array &$data, int $srcId, $url = false)
    {
        if (strpos($field, '/') === false) {
            if (isset($data['sources'][$srcId][$field])) {
                return ['value' => $data['sources'][$srcId][$field]];
            } else {
                return ['value' => 0];
            }
        } else {
            $fields = explode('/', $field);
            $var = $data['sources'][$srcId];
            foreach ($fields as $v) {
                if (isset($var[$v])) {
                    $var = $var[$v];
                }
            }
            if (!is_array($var)) {
                $var = ['value' => $url ? $var : (int)$var];
            }
            return $var;
        }
    }

    /**
     * @param array $renderData
     * @param int $srcId
     * @param array $loadedData
     */
    private static function unionRenderData(array &$renderData, int $srcId, array $loadedData) : void
    {
        $config = self::getConfig()[self::SOURCES_NAMES[$srcId]];
        foreach ($config['fields'] as $name => $field) {
            $renderData['metrics'][$name][$srcId] = [ self::getValueByConfigPath($field, $loadedData, $srcId)];
        }
        $renderData['urls'][$srcId] = [ self::getValueByConfigPath($config['urls']['detail'], $loadedData, $srcId, true)];
        $renderData['names'] = array_values(self::SOURCES_NAMES);

    }
}