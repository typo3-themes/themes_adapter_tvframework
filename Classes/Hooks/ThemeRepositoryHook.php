<?php

namespace KayStrobach\Themes\Adapter\TvFramework\Hooks;

use KayStrobach\Themes\Adapter\TvFramework\Domain\Model\Theme;

class ThemeRepositoryHook
{
    function init(&$params, $pObj)
    {
        if (array_key_exists('templavoila_framework',
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']) && array_key_exists('skins',
                $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila_framework'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila_framework']['skins'] as $skin) {
                $pObj->add(new Theme($skin));
            }
        }
    }
}
