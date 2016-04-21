<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Symfony\Component\Config\FileLocator;

class ImageCreatorFactory
{
	private $path="/src/StatisticBundle/DependencyInjection";
	public function create($type)
	{
		$path="/src/StatisticBundle/DependencyInjection";
		$configDirectories = array(__DIR__.$path);
		$locator = new FileLocator($configDirectories);
		$yamlUserFiles = $locator->locate('ImageCreatorConfig.json', null, true);
		print_r($yamlUserFiles);
		die();
	}

}