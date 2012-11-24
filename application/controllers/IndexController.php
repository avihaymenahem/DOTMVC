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
        $this->view->render();
    }

    public function newsAction()
    {
        $this->view->assign("name", "avihay");
        $this->view->render();
    }
}