<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;

class tx_newrelic_hooks {

    /**
     * Handles and dispatches the shutdown of the current process.
     *
     * @return void
     */
    public function frontendPreprocessRequest() {
        /** @var \AOE\Newrelic\Service $service */
        $service = GeneralUtility::makeInstance('\AOE\Newrelic\Service');
        $service->setConfiguredAppName();
        $service->setTransactionNameDefault('Frontend-Pre');
    }


    /**
     * Handles and dispatches the shutdown of the current frontend process.
     *
     * @return void
     */
    public function frontendEndOfFrontend() {
        /** @var \AOE\Newrelic\Service $service */
        $service = GeneralUtility::makeInstance('\AOE\Newrelic\Service');

        if ($temp_extId = GeneralUtility::_GP('eID'))        {
            $service->setTransactionNameImmutable('eId_'.$temp_extId);
        }
        $service->setTransactionName('Frontend');
        $service->addMemoryUsageCustomMetric();
        $service->addTslibFeCustomParameters();
    }

    /**
     * @param Tx_Extracache_System_Event_Events_EventOnStaticCacheContext $event
     */
    public function handleEventOnStaticCacheContext(Tx_Extracache_System_Event_Events_EventOnStaticCacheContext $event) {
        $GLOBALS['NEWRELIC_STATICCACHECONTEXT'] = $event->getStaticCacheContext();
    }
}