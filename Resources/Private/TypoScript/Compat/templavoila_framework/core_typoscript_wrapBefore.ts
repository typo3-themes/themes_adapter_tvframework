# elimate and replace obolete stuff:

page.10 >
plugin.tx_templavoila_pi1 >
page.includeJSlibs.tf_jquery >

page.includeJS.tf_core = EXT:themes_adapter_tvframework/Resources/Public/JavaScripts/core.js

<INCLUDE_TYPOSCRIPT: source="FILE:EXT:themes_adapter_tvframework/Resources/Private/TypoScript/Compat/templavoila_framework/core_typoscript.ts">
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:themes_adapter_tvframework/Resources/Private/TypoScript/Compat/css_styled_content/setup.typoscript">
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:themes_gridelements/Configuration/TypoScript/setup.txt">
# @todo move that reference ...
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:themes_gridelements_schulcms/Configuration/TypoScript/setup.txt">

page.bodyTagCObject >
page.bodyTagCObject = COA
page.bodyTagCObject {
  wrap = <body|>
  20 = CASE
  20 {
    key {
      data = levelfield: -2, backend_layout_next_level, slide
      override.field = backend_layout
      split {
        token = pagets__
        cObjNum = 1
        1.current = 1
      }
    }
    Content = TEXT
    Content.value = f1b
    ContentSidebar = TEXT
    ContentSidebar.value = f2b
    ContentSidebarMenu = TEXT
    ContentSidebarMenu.value = f3a
    MenuContentSidebar = TEXT
    MenuContentSidebar.value = f3c
    default = TEXT
    default.value = no-layout
    stdWrap.noTrimWrap = | id="|" |
  }
  30 = TEXT
  30.field = uid
  30.wrap = class="language-{$themes.languages.current.isoCodeShort} pid-|"
}

postCodeHeader = TEXT
postCodeHeader.value =

preCodeFeature = TEXT
preCodeFeature.value =

postCodeFeature = TEXT
postCodeFeature.value =

preCodeContentBlock-2 = TEXT
preCodeContentBlock-2.value =

postCodeContentBlock-2 = TEXT
postCodeContentBlock-2.value =

preCodeContentBlock-3 = TEXT
preCodeContentBlock-3.value =

contentBlock-3 = TEXT
contentBlock-3.value =

preCodeGeneratedContent-1 = TEXT
preCodeGeneratedContent-1.value =

f3a.preCodeGeneratedContent-1 = TEXT
f3a.preCodeGeneratedContent-1.value =

f3a.postCodeGeneratedContent-1 = TEXT
f3a.postCodeGeneratedContent-1.value =

f2b.preCodeGeneratedContent-1 = TEXT
f2b.preCodeGeneratedContent-1.value =

f2b.postCodeGeneratedContent-1 = TEXT
f2b.postCodeGeneratedContent-1.value =

header = TEXT
header.value =
