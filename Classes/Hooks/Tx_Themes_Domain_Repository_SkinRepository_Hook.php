<?php

class Tx_ThemesAdapterTemplavoilaframework_Hooks_Tx_Themes_Domain_Repository_SkinRepository_Hook {
	function init(&$params, $pObj) {
		if(array_key_exists('templavoila_framework', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']) && array_key_exists('skins', $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila_framework'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['templavoila_framework']['skins'] as $skin) {
				$pObj->add(new Tx_ThemesAdapterTemplavoilaframework_Domain_Model_Skin($skin));
			}
		}
	}
}