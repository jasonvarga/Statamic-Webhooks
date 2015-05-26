<?php

class Hooks_webhooks extends Hooks
{

	/**
	 * GO! Do all the required things.
	 */
	public function webhooks__go()
	{
		// Back out if the API isn't supplied or is incorrect
		if (Request::get('api_key') != $this->fetchConfig('api_key', Helper::getRandomString(), null, false, false)) {
			$app = \Slim\Slim::getInstance();
			$app->halt(403, 'Invalid API key.');
		}

		if ($this->config['clear_cache']) {
			$this->log->info('Statamic cache is set to TRUE');

			// Clear the contents of Statamic's cache directory
			$this->clearStatamicCache();
		}

		if ($this->config['clear_php_opcache'] && function_exists('opcache_reset')) {
			$this->log->info('PHP OPcache is set to TRUE');

			// Clear OpCache PHP cache storage installed as part of PHP5.5.*
			$this->clearOpCache();
		}

		if ($this->config['clear_html_caching']) {
			$this->log->info('Rendered HTML cache is set to TRUE');

			// Clear rendered html cache
			$this->clearHtmlCache();
		}

		if ($this->config['clear_tag_cache']) {
			$this->log->info('Template cache is set to TRUE');

			// Clear {{ cache }} template tag cache
			$this->clearTagCache();
		}

		if ( $this->config['clear_pagespeed_cache']) {
			$this->log->info('Mod_pagespeed cache is set to TRUE');

			// Clear mod_pagespeed cache
			$this->clearPageSpeedCache();
		}
	}


	//---------------------------------------------


	private function clearStatamicCache()
	{
		$app_cache_folder = BASE_PATH . '/_cache/_app/';
		Folder::delete($app_cache_folder, true);
		
		$tag_cache_folder = BASE_PATH . '/_cache/_add-ons/cache/';
		Folder::delete($tag_cache_folder, true);
		
		$this->log->info('Statamic\'s cache has been cleared.');
	}

	private function clearOpCache()
	{
		opcache_reset();
		$this->log->info('OpCache has been cleared.');
	}

	private function clearHtmlCache()
	{
		$cache_folder = BASE_PATH . '/_cache/_add-ons/html_caching/';
		Folder::delete($cache_folder, true);
		$this->log->info('Rendered HTML cache has been cleared.');
	}

	private function clearTagCache()
	{
		$cache_folder = BASE_PATH . '/_cache/_add-ons/cache/';
		Folder::delete($cache_folder, true);
		$this->log->info('Template tag cache has been cleared.');
	}

	private function clearPageSpeedCache()
	{

		$ch = curl_init();
		$cache_url = Config::getSiteURL();
		$cache_query = '/pagespeed_admin/cache?purge=*';

		curl_setopt($ch, CURLOPT_URL, $cache_url . $cache_query);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		curl_exec($ch);
		curl_close($ch);

		$this->log->info($cache_url . $cache_query);
		$this->log->info('mod_pagespeed cache has been cleared.');
	}

}
