# themes_adapter_tvframework
ALPHA: Adapter to allow tvframework skins in ext:themes in TYPO3

Connects themes with TV Framework from 2010

* https://t3n.de/magazin/neues-framework-vereinfacht-website-entwicklung-225337/2/
* https://extensions.typo3.org/extension/templavoila_framework
* https://www.thomaskieslich.de/blog/31-templavoila-framework-mit-uebersetzung/
* https://typo3blogger.de/templavoila-framework-von-ron-hall-podcast/
* https://de.slideshare.net/busynoggin/t3-con09-dallas-tutorial-1313328

## How it works

A TemplaVoilà Framework skin is an extension with `typoscript/skin_constants.ts`, `skin_typoscript.ts` and
`skin_tsconfig.ts`. EXT:themes offers it as theme, the adapter wraps its TypoScript with the emulated framework
(`Resources/Private/TypoScript/Compat/templavoila_framework/`):

1. constants of the framework (`core_constants.typoscript`, including themes_gridelements) and the generated constants
   below
2. setup of the framework (`core_typoscript_wrapBefore.typoscript`: `core_typoscript.typoscript`, the css_styled_content
   compatibility and themes_gridelements)
3. constants and setup of the skin
4. `core_typoscript_wrapEnd.typoscript`

TemplaVoilà itself is not needed. The skin is selected in the backend module "Themes".

## Generated constants

| Constant | Value |
|---|---|
| `templavoila_framework.skinPath` | `EXT:<skin>`, for properties resolving `EXT:` paths (`includeCSS`, `file`, `fontFile`, …) |
| `templavoila_framework.skinPublicPath` | public URL of `Resources/Public` of the skin (`/_assets/<hash>`), for raw HTML in `TEXT` objects |
| `templavoila_framework.corePath` | `EXT:themes_adapter_tvframework/Resources/Public/` |
| `siteURL` | base path of the site of the template, e.g. `/schule/` (all sites share one domain) |

The framework includes `{$templavoila_framework.skinPath}/Resources/Public/Css/mainstyles.css`, `…/Css/rte.css` and
`…/JavaScript/skin.js` of the skin.

## TYPO3 v12 and later

* Requires EXT:themes from the branch `typo3-12-13-14` (TypoScript via `buildTypoScript()` instead of the removed hook).
* Skins need their CSS, JavaScript and fonts in `Resources/Public` (composer mode serves nothing else).
* The TypoScript parser of v12 keeps a reference (`f1b.preCodeContent =< preCodeContent`) when a new object is
  assigned: skins clear it first (`f1b.preCodeContent >`).
* Not yet migrated: the framework uses `<INCLUDE_TYPOSCRIPT>`, which TYPO3 v13 marks as deprecated and v14 ignores.

## Tests

Functional tests in docker against TYPO3 12 and 13 (gridelements has no release for TYPO3 14 yet), with the clones of
themes and themes-gridelements next to this one (otherwise their branches from GitHub):

    Build/Scripts/runTests.sh -t 12             # or 13
    Build/Scripts/runTests.sh -a                # all supported versions

`Tests/Functional/Fixtures/Extensions/skin_fixture` is a minimal skin. Covered: registration of skins, generated
constants (`skinPath`, `skinPublicPath`, `siteURL` per site), order of framework and skin, and a page rendered through
the framework. On TYPO3 13 the tests report the deprecated `<INCLUDE_TYPOSCRIPT>` of the framework (still to migrate
for TYPO3 14).

