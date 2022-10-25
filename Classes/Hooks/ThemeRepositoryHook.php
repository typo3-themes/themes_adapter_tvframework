<?php

namespace KayStrobach\Themes\Adapter\TvFramework\Hooks;

use KayStrobach\Themes\Adapter\TvFramework\Domain\Model\Theme;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ThemeRepositoryHook
{
    function init(&$params, $pObj)
    {
        $extensions = ExtensionManagementUtility::getLoadedExtensionListArray();
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        foreach ($extensions as $extension) {
            $path = $packageManager->getPackage($extension)->getPackagePath();
            if (is_dir($path . '/typoscript/')) {
                $pObj->add(new Theme($extension));
            }
        }
    }
}
