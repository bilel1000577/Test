<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Quizmoo\StatisticBundle\DependencyInjection\ImageCreatorFactory;
class ImageCreatorService
{
	public function createImage($type,$question,$width,$scale,$imageType,$params,$em)
	{
		$factory=new ImageCreatorFactory();
		try {
			$imageCreator=$factory->create($type);
			return $imageCreator->createImage($question,$imageType,$width,$scale,$params,$em);
		} catch (\Exception $e) {
			return null;
		}
		

	}
}