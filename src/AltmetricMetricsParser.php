<?php
/**
 * Created by PhpStorm.
 * User: Yura
 * Date: 05.06.2018
 * Time: 19:45
 */

namespace Core;
require_once('ApiParser.php');


class AltmetricMetricsParser extends ApiParser
{
    protected static $endpoint = 'https://api.altmetric.com/v1/doi/';
    protected static $source = Utils::ALTMETRIC_SRC;
}