<?php
namespace Quizmoo\StatisticBundle\DependencyInjection;
use Zend\Json\Json;
abstract class QuestionImageCreator
{
	abstract protected function createCahrtData($question,$params,$em);
	protected function getImage($data,$imageType,$width,$scale)
	{
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '-1');
		try{
            $type="";
            if($imageType!="pdf")
                $type.="image/".$imageType;
            else
            {
                $type="pdf";
            }
            
			$urltopost = "http://export.highcharts.com/";
			$datatopost = array (
				"content" => "options",
				"options" => $data,
				"type" => $type,
				"width"=>$width,
				"scale"=>$scale,
				"constr"=>"Chart"
			);

			$ch = curl_init ($urltopost);
			curl_setopt ($ch, CURLOPT_POST, true);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $datatopost);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
			$returndata = curl_exec ($ch);
			return $returndata;
		} catch (\Exception $e) {
			return null;
		}
	}
	public function createImage($question,$imageType,$width,$scale,$params,$em)
	{
        $results=array();
		$charts=$this->createCahrtData($question,$params,$em);

        foreach ($charts as $key) {
            $data=$this->toJson($key);
            
            array_push($results, $this->getImage($data,$imageType,"2000","4"));
            
        }
    
		

		return $results;
		
	}
	protected function toJson($bar)
	{
		$chartJS = "{";
        // Colors
        if (!empty($bar->colors)) {
            $chartJS .= "        colors: " . json_encode($bar->colors) . ",\n";
        }

        // Credits
        if (get_object_vars($bar->credits->credits)) {
            $chartJS .= "        credits: " . json_encode($bar->credits->credits) . ",\n";
        }

        // Exporting
        if (get_object_vars($bar->exporting->exporting)) {
            $chartJS .= "        exporting: " .
                Json::encode($bar->exporting->exporting,
                    false,
                    array('enableJsonExprFinder' => true)) . ",\n";
        }

        // Global
        if (get_object_vars($bar->global->global)) {
            $chartJS .= "        global: " . json_encode($bar->global->global) . ",\n";
        }

        // Labels
        // Lang

        // Legend
        if (get_object_vars($bar->legend->legend)) {
            $chartJS .= "        legend: " .
                Json::encode($bar->legend->legend,
                    false,
                    array('enableJsonExprFinder' => true)) . ",\n";
        }

        // Loading
        // Navigation
        // Pane

        // PlotOptions
        if (get_object_vars($bar->plotOptions->plotOptions)) {
            $chartJS .= "        plotOptions: " .
                Json::encode($bar->plotOptions->plotOptions,
                    false,
                    array('enableJsonExprFinder' => true)) . ",\n";
        }

        // Series
        if (!empty($bar->series)) {
            $chartJS .= "        series: " .
                Json::encode($bar->series[0],
                    false,
                    array('enableJsonExprFinder' => true)) . ",\n";
        }

        // Subtitle
        if (get_object_vars($bar->subtitle->subtitle)) {
            $chartJS .= "        subtitle: " . json_encode($bar->subtitle->subtitle) . ",\n";
        }

        // Symbols

        // Title
        if (get_object_vars($bar->title->title)) {
            $chartJS .= "        title: " . json_encode($bar->title->title) . ",\n";
        }

        // Tooltip
        if (get_object_vars($bar->tooltip->tooltip)) {
            $chartJS .= "        tooltip: " .
                Json::encode($bar->tooltip->tooltip,
                    false,
                    array('enableJsonExprFinder' => true)) . ",\n";
        }

        // xAxis
        if (gettype($bar->xAxis) === 'array') {
            if (!empty($bar->xAxis)) {
                $chartJS .= "        xAxis: " .
                    Json::encode($bar->xAxis[0],
                        false,
                        array('enableJsonExprFinder' => true)) . ",\n";
            }
        } elseif (gettype($bar->xAxis) === 'object') {
            if (get_object_vars($bar->xAxis->xAxis)) {
                $chartJS .= "        xAxis: " .
                    Json::encode($bar->xAxis->xAxis,
                        false,
                        array('enableJsonExprFinder' => true)) . ",\n";
            }
        }

        // yAxis
        if (gettype($bar->yAxis) === 'array') {
            if (!empty($bar->yAxis)) {
                $chartJS .= "        yAxis: " .
                    Json::encode($bar->yAxis[0],
                        false,
                        array('enableJsonExprFinder' => true)) . ",\n";
            }
        } elseif (gettype($bar->yAxis) === 'object') {
            if (get_object_vars($bar->yAxis->yAxis)) {
                $chartJS .= "        yAxis: " .
                    Json::encode($bar->yAxis->yAxis,
                        false,
                        array('enableJsonExprFinder' => true)) . ",\n";
            }
        }

        $chartJS.="}";
        return trim($chartJS);
	}
}