<?php

declare(strict_types=1);

namespace KayStrobach\Themes\Adapter\TvFramework\Tests\Functional;

use KayStrobach\Themes\Adapter\TvFramework\Domain\Model\Theme;
use KayStrobach\Themes\Domain\Repository\ThemeRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * A TemplaVoilà Framework skin as theme: the TypoScript of the skin wrapped by the emulated framework.
 */
class SkinTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['install', 'form'];

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/gridelements',
        'typo3conf/ext/themes',
        'typo3conf/ext/themes_gridelements',
        'typo3conf/ext/themes_adapter_tvframework',
        __DIR__ . '/Fixtures/Extensions/skin_fixture',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/pages.csv');
        // site with a base path, all sites of SchulCMS share one domain
        $siteFolder = Environment::getConfigPath() . '/sites/school';
        GeneralUtility::mkdir_deep($siteFolder);
        copy(__DIR__ . '/Fixtures/sites/school/config.yaml', $siteFolder . '/config.yaml');
        $this->get(CacheManager::class)->getCache('core')->flush();
    }

    private function buildFor(int $pid): Theme
    {
        $theme = new Theme('skin_fixture');
        $theme->buildTypoScript(['uid' => 1, 'pid' => $pid, 'root' => 1, 'clear' => 3, 'tx_themes_skin' => 'skin_fixture']);
        return $theme;
    }

    public function testSkinsAreRegisteredAsThemes(): void
    {
        self::assertInstanceOf(Theme::class, $this->get(ThemeRepository::class)->findByUid('skin_fixture'));
    }

    public function testConstantsForTheSkinAreGenerated(): void
    {
        $constants = $this->buildFor(1)->getConstants();
        $publicPath = '/' . trim(PathUtility::getPublicResourceWebPath('EXT:skin_fixture/Resources/Public/'), '/');
        self::assertStringContainsString("templavoila_framework.skinPath = EXT:skin_fixture\n", $constants);
        self::assertStringContainsString("templavoila_framework.skinPublicPath = {$publicPath}\n", $constants);
        self::assertStringContainsString("siteURL = /school/\n", $constants, 'base path of the site, not "/" (another site)');
        self::assertStringContainsString('skinFixtureColor = #123456', $constants);
    }

    public function testSiteUrlWithoutSiteIsTheRoot(): void
    {
        self::assertStringContainsString("siteURL = /\n", $this->buildFor(10)->getConstants());
    }

    public function testFrameworkWrapsTheSkin(): void
    {
        $constants = $this->buildFor(1)->getConstants();
        $setup = $this->buildFor(1)->getSetup();
        // constants: framework, generated constants, skin
        self::assertLessThan(strpos($constants, '# skin fixture: constants'), strpos($constants, 'templavoila_framework.skinPath'));
        // setup: framework (wrapBefore), skin, wrapEnd
        $skinPosition = strpos($setup, '# skin fixture: setup');
        self::assertNotFalse($skinPosition);
        self::assertLessThan($skinPosition, strpos($setup, 'core_typoscript.typoscript'));
        self::assertGreaterThan($skinPosition, strrpos($setup, "feature = COA\n"), 'core_typoscript_wrapEnd.typoscript');
    }

    public function testStylesheetAndJavaScriptOfTheSkinArePublic(): void
    {
        // the framework includes them from Resources/Public (composer mode serves nothing else)
        $setup = (string)file_get_contents(GeneralUtility::getFileAbsFileName(
            'EXT:themes_adapter_tvframework/Resources/Private/TypoScript/Compat/templavoila_framework/core_typoscript.typoscript'
        ));
        self::assertStringContainsString('{$templavoila_framework.skinPath}/Resources/Public/Css/mainstyles.css', $setup);
        self::assertStringContainsString('{$templavoila_framework.skinPath}/Resources/Public/JavaScript/skin.js', $setup);
        self::assertFileExists(GeneralUtility::getFileAbsFileName('EXT:skin_fixture/Resources/Public/Css/mainstyles.css'));
    }

    public function testPageIsRenderedWithTheSkin(): void
    {
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://example.com/school/welcome'));
        $html = (string)$response->getBody();
        self::assertSame(200, $response->getStatusCode(), substr(strip_tags($html), 0, 500));
        self::assertStringContainsString('Css/mainstyles.css', $html);
        self::assertStringContainsString('JavaScript/skin.js', $html);
    }
}
