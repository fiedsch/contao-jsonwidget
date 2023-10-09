<?php
use Fiedsch\JsonWidgetBundle\Widget\Backend\WidgetJSON;
use Fiedsch\JsonWidgetBundle\Widget\Backend\WidgetYAML;

$GLOBALS['BE_FFL']['jsonWidget'] = WidgetJSON::class;
$GLOBALS['BE_FFL']['yamlWidget'] = WidgetYAML::class;

