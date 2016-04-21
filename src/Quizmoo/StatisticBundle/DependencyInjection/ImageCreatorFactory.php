<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Symfony\Component\Config\FileLocator;

class ImageCreatorFactory
{
	private $path="/src/StatisticBundle/DependencyInjection";
	public function create($type)
	{
		
		$configDirectories = array(__DIR__);
		$locator = new FileLocator($configDirectories);
		$configfile = $locator->locate('ImageCreatorConfig.json', null, true);
		$content=file_get_contents($configfile);
		$info = json_decode($content);
		$creator=$info->{"creators"};
		$class="";
		foreach ($creator as $key) {
			if($key->{"type"}==$type)
				$class=$key->{"class"};
		}
		if($class!="")
			return new $class;
		else
			throw new \Exception("Insupported type", 1);
			
		
	}

}