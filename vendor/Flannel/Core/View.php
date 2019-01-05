<?php

namespace Flannel\Core;

abstract class View {

    /**
     * @var string
     */
    protected $_basePath;

    /**
     * @var string
     */
    public $template;

    /**
     * @param string $file
     * @param mixed[] $properties
     */
    public function __construct($file, $properties=null) {
        $this->_basePath = \Flannel\Core\Config::get('dir.template.view');

        if(!empty($properties)) {
            foreach($properties as $key=>$value) {
                $this->$key = $value;
            }
        }
        $this->template = $file;
    }

    /**
     * Class propertied can be used in the template as properties
     * (eg, $this->name) or directly as variables (eg, $name)
     *
     * @return string
     */
    public function render() {
        $output = '';

        if(!empty($this->template)) {
            $filename = $this->template;
            unset($this->template);

            ob_start();

            try {
                extract(get_object_vars($this));
                require $this->_basePath . $filename;
            } catch (Exception $e) {
                ob_end_clean();
                throw $e;
            }

            $output = trim(ob_get_clean()) . "\n";
        }

        return $output;
    }

    /**
     * Alias of render() since PHP won't let it throw an Exception
     *
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

}
