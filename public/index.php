<?php

require_once(dirname(dirname(__FILE__)) . '/lib/Bootstrap.php');

/**
 * To add extra config file, use:
 * $boot = Bootstrap::getInstance();
 * $boot->addConfig('default');
 */
Bootstrap::getInstance();