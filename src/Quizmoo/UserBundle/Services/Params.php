<?php
namespace Quizmoo\UserBundle\Services;
use Symfony\Component\Yaml\Yaml;

class Params
{
    private $file;
    private $old_params = array();

    public function __construct($filename){
        $this->file = __DIR__.'/../Resources/config/'.$filename;
        $this->old_params = $this->read();
    }
    
    private function render($params){
        return Yaml::dump(array('parameters' => $params));
    }

    
    private function read(){
        $old_params = Yaml::parse($this->file);
        if($old_params === false || $old_params === array())
            throw new InvalidArgumentException('File error ! ('.$this->file.')');
        if(isset($old_params['parameters']) && is_array($old_params['parameters']))
            return $old_params['parameters'];
        else
            return array();
    }

    
    private function merge($params){
        if(is_array($params))
            return $params = array_merge($this->old_params, $params);
        else
            return $this->old_params;
    }
    
   
    public function save($params){
        $persist_params = $this->merge($params);
        return file_put_contents($this->file, $this->render($persist_params));
    }
}