<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('newrelic');
require_once $extensionPath.'Classes/Service.php';
require_once $extensionPath.'Hooks/class.tx_newrelic_hooks.php';



/** @var \AOE\Newrelic\Service $service */
$service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\AOE\Newrelic\Service');
$service->setConfiguredAppName();
$service->setTransactionNameDefault('Base');
$service->addCommonRequestParameters();


if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'CLI Mode') {
    $service->addTransactionNamePostfix('DirectRequest');
}

if (isset($_SERVER['HTTP_X_T3CRAWLER'])) {
    $service->addTransactionNamePostfix('Crawler');
}

if (defined('TYPO3_cliMode') && TYPO3_cliMode) {
    $service->setTransactionName('CliMode');
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $service->addTransactionNamePostfix('FORMSUBMIT');
}
/**** BACKEND ***/
if (defined('TYPO3_MODE') && TYPO3_MODE == 'BE') {
    /** @var \AOE\Newrelic\Service $service */
    $service = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\AOE\Newrelic\Service');
    $service->setConfiguredAppName();
    $service->setTransactionName('Backend');
 }

/**** FRONTEND ***/
if (defined('TYPO3_MODE') && TYPO3_MODE == 'FE') {
    $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest']['newrelic'] = 'EXT:newrelic/Hooks/class.tx_newrelic_hooks.php:tx_newrelic_hooks->frontendPreprocessRequest';
    $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe']['newrelic'] = 'EXT:newrelic/Hooks/class.tx_newrelic_hooks.php:tx_newrelic_hooks->frontendEndOfFrontend';
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('extracache')) {
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance ( 'Tx_Extracache_Configuration_ConfigurationManager' );
        $configurationManager->addContentProcessorDefinition ( '\AOE\Newrelic\ExtraCacheContentProcessor', $extensionPath . 'Classes/ExtraCacheContentProcessor.php' );#

        $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance ( 'Tx_Extracache_System_Event_Dispatcher' );
        $dispatcher->addLazyLoadingHandler ( 'onStaticCacheContext', 'tx_newrelic_hooks', 'handleEventOnStaticCacheContext' );
    }
}