<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\KayStrobach\Themes\Domain\Repository\ThemeRepository::class]['init'][]
	= \KayStrobach\Themes\Adapter\TvFramework\Hooks\ThemeRepositoryHook::class . '->init';
