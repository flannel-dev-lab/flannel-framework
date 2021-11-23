<?php

class Controller_Admin_Theme extends Controller_Default
{

    public function header()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Theme Header Editor';
        self::$template->content = new View_Base('/admin/theme/header.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function footer()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Theme Footer Editor';
        self::$template->content = new View_Base('/admin/theme/footer.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function mainindex()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Main Index Editor';
        self::$template->content = new View_Base('/admin/theme/mainindex.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function singlepost()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Single Post Editor';
        self::$template->content = new View_Base('/admin/theme/singlepost.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function notfound()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = '404 (Not Found) Editor';
        self::$template->content = new View_Base('/admin/theme/notfound.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function stylesheet()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'Stylesheet Editor';
        self::$template->content = new View_Base('/admin/theme/stylesheet.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

    public function javascript()
    {

        self::$template->header = new View_Base('/admin/shared/header.phtml');
        self::$template->header->pageTitle = 'JavaScript Editor';
        self::$template->content = new View_Base('/admin/theme/javascript.phtml');
        self::$template->footer = new View_Base('/admin/shared/footer.phtml');

        return;
    }

}
