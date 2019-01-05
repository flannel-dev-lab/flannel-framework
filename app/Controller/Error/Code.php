<?php

class Controller_Error_Code extends Controller_Default {

    protected $_isPublic = true;

    public function __call($name, $arguments) {
        switch($name) {
            case '403':
                $this->forbidden();
            case '404':
                $this->notFound();
            case '503':
                $this->serviceUnavailable();
            default:
                $this->notFound();
        }
    }

}
