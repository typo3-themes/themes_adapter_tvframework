<?php

namespace KayStrobach\Themes\Adapter\TvFramework\Domain\Model;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class Theme extends \KayStrobach\Themes\Domain\Model\Theme
{
    /**
     * Constructs a new Skin
     *
     * @api
     */
    public function __construct($extensionName)
    {
        parent::__construct($extensionName);

        /**
         * set needed path variables
         */
        $path = ExtensionManagementUtility::extPath($this->getExtensionName()) . 'typoscript/';
        $this->pathTyposcript = $path . 'skin_typoscript.ts';
        $this->pathTyposcriptConstants = $path . 'skin_constants.ts';
        $this->pathTsConfig = $path . 'skin_tsconfig.ts';

        if (ExtensionManagementUtility::isLoaded('templavoila_framework')) {
            $skinInfo = tx_templavoilaframework_lib::getSkinInfo('EXT:' . $extensionName);
            $this->title = $skinInfo['title'];
            $this->description = $skinInfo['description'];
            $this->previewImage = 'EXT:' . str_replace('../typo3conf/ext/', '', $skinInfo['icon']);
        } else {
            $screenshotsFolder = ExtensionManagementUtility::extPath($this->getExtensionName()) . 'Resources/Public/Screenshots/';
            if (!is_dir($screenshotsFolder)) {
                GeneralUtility::mkdir_deep($screenshotsFolder);

                $this->previewImage = 'EXT:' . $extensionName . '/ext_icon.gif';

                if (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.gif')) {
                    copy(
                        ExtensionManagementUtility::extPath($this->getExtensionName() ). 'screenshot.gif',
                        $screenshotsFolder . 'screenshot.gif'
                    );
                }
                if (is_file(ExtensionManagementUtility::extPath($extensionName) . 'ext_icon.gif')) {
                    copy(
                        ExtensionManagementUtility::extPath($this->getExtensionName() ). 'ext_icon.gif',
                        $screenshotsFolder . 'screenshot.gif'
                    );
                }
                if (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.jpg')) {
                    rename(
                        ExtensionManagementUtility::extPath($this->getExtensionName()) . 'screenshot.jpg',
                        $screenshotsFolder . 'screenshot.jpg'
                    );
                }
                if (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.png')) {
                    rename(
                        ExtensionManagementUtility::extPath($this->getExtensionName()) . 'screenshot.png',
                        $screenshotsFolder . 'screenshot.png'
                    );
                }
            }


            if (is_file($screenshotsFolder  . 'screenshot.gif')) {
                $this->previewImage = 'EXT:' . $this->getExtensionName() . '/Resources/Public/Screenshots/screenshot.gif';
            }
            if (is_file($screenshotsFolder . 'screenshot.jpg')) {
                $this->previewImage = 'EXT:' . $this->getExtensionName() . '/Resources/Public/Screenshots/screenshot.jpg';
            }
            if (is_file($screenshotsFolder . 'screenshot.png')) {
                $this->previewImage = 'EXT:' . $this->getExtensionName() . '/Resources/Public/Screenshots/screenshot.png';
            }

        }
    }

    public function getTSConfig()
    {
        $buffer = @file_get_contents(ExtensionManagementUtility::extPath('themes_adapter_tvframework') . 'Resources/Private/TypoScript/Compat/templavoila_framework/pagets.ts')
            . "\n\n"
            . parent::getTSConfig();
        return $buffer;
    }

    /**
     * Includes static template records (from static_template table) and static template files (from extensions) for the input template record row.
     *
     * @param object        Reference back to parent object, t3lib_tstemplate or one of its subclasses.
     * @param TemplateService $pObj
     * @param array $extensions
     * @param array $features
     * @return    void
     */
    /**
     * TYPO3 v12 and later: EXT:themes builds the TypoScript of the theme with buildTypoScript() and puts it
     * into the template row (AfterTemplatesHaveBeenDeterminedEvent). The templavoila_framework emulation
     * wraps it like the old hook addTypoScriptForFe() did: core constants and setup before, the rest after.
     */
    public function buildTypoScript(array $row): void
    {
        $alreadyBuilt = $this->constants !== '' || $this->setup !== '';
        parent::buildTypoScript($row);
        if ($alreadyBuilt) {
            return;
        }
        $compatPath = ExtensionManagementUtility::extPath('themes_adapter_tvframework') . 'Resources/Private/TypoScript/Compat/templavoila_framework/';
        $this->constants = (string)@file_get_contents($compatPath . 'core_constants.typoscript')
            . PHP_EOL . 'templavoila_framework.skinPath = EXT:' . $this->getExtensionName()
            . PHP_EOL . 'templavoila_framework.corePath = EXT:themes_adapter_tvframework/Resources/Public/'
            // public URL of Resources/Public for raw HTML in TEXT objects, where EXT: paths are not resolved
            . PHP_EOL . 'templavoila_framework.skinPublicPath = /' . trim(PathUtility::getPublicResourceWebPath('EXT:' . $this->getExtensionName() . '/Resources/Public/'), '/')
            . PHP_EOL . 'siteURL = ' . $this->getSiteBasePath((int)($row['pid'] ?? 0))
            . PHP_EOL . $this->constants;
        $this->setup = (string)@file_get_contents($compatPath . 'core_typoscript_wrapBefore.typoscript')
            . PHP_EOL . $this->setup
            . PHP_EOL . (string)@file_get_contents($compatPath . 'core_typoscript_wrapEnd.typoscript');
    }

    /**
     * Base path of the site the template belongs to (e.g. "/schule/"), used for the constant "siteURL" (logo links).
     * All sites share one domain, the old default "/" ({$themes.configuration.baseurl}) links to another site.
     */
    private function getSiteBasePath(int $pageId): string
    {
        if ($pageId <= 0) {
            return '/';
        }
        try {
            $path = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageId)->getBase()->getPath();
        } catch (SiteNotFoundException) {
            return '/';
        }
        return '/' . trim($path, '/') . ($path === '' || trim($path, '/') === '' ? '' : '/');
    }
}
