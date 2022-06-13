<?php

require(ROOT.'/core/Autoloader.php');

class View
{
    private $_file;
    private $_t;



    public function __construct($action)
    {
        $this->_file = 'views/view' . $action . '.php';
        
    }

    public function render(array $data =  [])
    {   
        $content = $this->generateFile($this->_file, $data);

        if(isset($data['template'])) {
            $view = $this->generateFile('views/template'.$data['template'].'.php', ['t' => $this->_t,'content' => $content, 'data' => $data]);
        }
        else 
        {
            $view = $this->generateFile('views/template.php', ['t' => $this->_t,'data' => $data, 'content' => $content]);
        }

        echo $view;
       


    }

    private function generateFile($file, $data)
    {

        if (file_exists($file)) {
            extract($data);
            ob_start();
            require $file;
            return ob_get_clean();

        } else {
            throw new Exception($file.' not found');
        }


    }
}
