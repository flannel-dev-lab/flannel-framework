<?php

class Controller_Admin_Index extends Controller_Default {

    public function __construct() {
        parent::__construct();      

        // TODO: Add authentication lock here  
    }

    public function index() {

        self::$template->header  = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Admin';
        self::$template->content = new View_Base('/admin/index.phtml');
        self::$template->footer  = new View_Base('/admin/shared/footer.phtml');

        return;
    }

}
