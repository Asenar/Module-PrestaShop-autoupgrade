<?php

class Upgrader extends UpgraderCore
{
	/**
	 * link to xml which contains urls for downloading 
	 * (default is http://api.prestashop.com/xml/upgrader.xml ) 
	 * 
	 * @var string
	 */
	public $rss_version_link = 'http://prestadev.marinetti.fr/xml/upgrader.xml';
	/**
	 * root directory which contains all [prestashop version].xml  
	 * (default is http://api.prestashop.com/xml/md5/ ) 
	 * 
	 * @var string
	 */
	public $rss_md5file_link_dir = 'http://prestadev.marinetti.fr/xml/md5/';

}

