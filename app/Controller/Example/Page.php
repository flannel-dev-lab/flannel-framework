<?php

class Controller_Example_Page extends Controller_Default {

    public function index() {

        self::$template->header  = new View_Base('/shared/header.phtml');
        self::$template->header->pageTitle = 'Example Page';
        self::$template->content = new View_Base('/example/page.phtml');
        self::$template->footer  = new View_Base('/shared/footer.phtml');

        return;
    }

    public function custom() {

        self::$template->header  = new View_Base('/shared/header.phtml');
        self::$template->header->pageTitle = 'Example Page Custom';
        self::$template->content = new View_Base('/example/page/custom.phtml');
        self::$template->footer  = new View_Base('/shared/footer.phtml');

        return;
    }

}
