<?php

class IndexController extends BaseController
{
    /**
     * Runs before every other function
     */
    public function onInit()
    {

    }

    public function indexAction()
    {

    }

    public function newsAction()
    {
        $this->view->assign("name", "avihay");
        $this->view->render();
    }
}