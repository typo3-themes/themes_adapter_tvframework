<?php

namespace KayStrobach\Themes\Adapter\TvFramework\Domain\Model;

use TYPO3\CMS\Core\TypoScript\TemplateService;
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
        $this->pathTSConfig = $path . 'skin_tsconfig.ts';

        if (ExtensionManagementUtility::isLoaded('templavoila_framework')) {
            $skinInfo = tx_templavoilaframework_lib::getSkinInfo('EXT:' . $extensionName);
            $this->title = $skinInfo['title'];
            $this->description = $skinInfo['description'];
            $this->previewImage = 'EXT:' . str_replace('../typo3conf/ext/', '', $skinInfo['icon']);
        } else {
            $this->previewImage = 'EXT:' . $extensionName . '/ext_icon.gif';
            if (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.gif')) {
                $this->previewImage = 'EXT:' . $extensionName . '/screenshot.gif';
            } elseif (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.jpg')) {
                $this->previewImage = 'EXT:' . $extensionName . '/screenshot.jpg';
            } elseif (is_file(ExtensionManagementUtility::extPath($extensionName) . 'screenshot.png')) {
                $this->previewImage = 'EXT:' . $extensionName . '/screenshot.png';
            }
        }
    }

    public function getTSConfig()
    {
        $buffer = GeneralUtility::getUrl(ExtensionManagementUtility::extPath('themes_adapter_tvframework') . 'Resources/Private/TypoScript/Compat/templavoila_framework/pagets.ts')
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
    public function addTypoScriptForFe(
        &$params,
        TemplateService &$pObj,
        $extensions = [],
        $features = []
    ) {
        $tvframeworkCompatBasePath = ExtensionManagementUtility::extPath('themes_adapter_tvframework') . 'Resources/Private/TypoScript/Compat/templavoila_framework/';

        // include templavoila wrapper templates, with core templates
        $pObj->processTemplate(
            [
                'constants' =>
                    GeneralUtility::getUrl($tvframeworkCompatBasePath . 'core_constants.ts') .
                    PHP_EOL . 'templavoila_framework.skinPath = EXT:' . $this->getExtensionName() .
                    PHP_EOL . 'templavoila_framework.corePath = EXT:themes_adapter_tvframework/Resources/Public/',
                'config' => GeneralUtility::getUrl($tvframeworkCompatBasePath . 'core_typoscript_wrapBefore.ts'),
                'editorcfg' => '',
                'include_static' => '',
                'include_static_file' => '',
                'title' => 'themes_tvf_wrapBefore',
                'uid' => md5('themes_tvf_wrapBefore')
            ],
            $params['idList'] . ',themes_tvf_wrapBefore',
            $params['pid'],
            'themes_tvf_wrapBefore',
            $params['templateId']
        );

        // include skin ts
        parent::addTypoScriptForFe($params, $pObj);

        // include templavoila core templates

        $pObj->processTemplate(
            array(
                'constants' => '',
                'config' => GeneralUtility::getUrl($tvframeworkCompatBasePath . 'core_typoscript_wrapEnd.ts'),
                'editorcfg' => '',
                'include_static' => '',
                'include_static_file' => '',
                'title' => 'themes_tvf_wrapEnd',
                'uid' => md5('themes_tvf_wrapEnd')
            ),
            $params['idList'] . ',themes_tvf_wrapEnd',
            $params['pid'],
            'themes_tvf_wrapEnd',
            $params['templateId']
        );
    }
}
