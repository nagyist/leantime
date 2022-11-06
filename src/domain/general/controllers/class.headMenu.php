<?php
namespace leantime\domain\controllers {

    use leantime\core;
    use leantime\base\controller;
    use leantime\domain\services;

    class headMenu extends controller
    {

        private $tpl;
        private $timesheets;

        public function __construct()
        {

            $this->timesheets = new services\timesheets();

        }

        public function run()
        {

            $this->tpl->assign('current', explode(".", core\frontcontroller::getCurrentRoute()));
            $this->tpl->assign("onTheClock", $this->timesheets->isClocked($_SESSION["userdata"]["id"]));
            $this->tpl->displayPartial("general.headMenu");

        }

    }

}
