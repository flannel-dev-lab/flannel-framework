<?php

class Controller_Example_Index extends Controller_Default {

    public function index() {

        self::$template->header  = new View_Base('/shared/header.phtml');
        self::$template->header->pageTitle = 'Example';
        self::$template->content = new View_Base('/example/index.phtml');
        self::$template->footer  = new View_Base('/shared/footer.phtml');

        return;
    }

}
