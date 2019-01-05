<?php

class Controller__Index extends Controller_Default {

    public function index() {

        self::$template->header  = new View_Base('/shared/header.phtml');
        self::$template->header->pageTitle = 'Index';
        self::$template->content = new View_Base('/index.phtml');;
        self::$template->footer  = new View_Base('/shared/footer.phtml');

        return;
    }
}
