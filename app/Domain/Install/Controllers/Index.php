<?php

namespace Leantime\Domain\Install\Controllers {

    use Leantime\Core\Frontcontroller as FrontcontrollerCore;
    use Leantime\Core\Controller;
    use Leantime\Domain\Install\Repositories\Install as InstallRepository;
    use Illuminate\Http\Exceptions\HttpResponseException;
    use Symfony\Component\HttpFoundation\Response;

    /**
     *
     */
    class Index extends Controller
    {
        private InstallRepository $installRepo;

        /**
         * init - initialize private variables
         *
         * @access public
         * @throws HttpResponseException
         */
        public function init(InstallRepository $installRepo)
        {
            $this->installRepo = $installRepo;

            if ($this->installRepo->checkIfInstalled()) {
                throw new HttpResponseException(FrontcontrollerCore::redirect(BASE_URL));
            }
        }

        /**
         * get - handle get requests
         *
         * @access public
         * @param $params parameters or body of the request
         */
        public function get($params)
        {
            return $this->tpl->display("install.new", "entry");
        }

        /**
         * @param $params
         * @return Response
         */
        public function post($params): Response
        {
            $values = array(
                'email'         => "",
                'password'      => "",
                'firstname'     => "",
                'lastname'      => "",
            );

            if (isset($_POST['install'])) {
                $values = array(
                    'email' => ($params['email']),
                    'password' => $params['password'],
                    'firstname' => ($params['firstname']),
                    'lastname' => ($params['lastname']),
                    'company' => ($params['company']),
                );

                $notificationSet = false; // Track whether a notification has been set

                if (empty($params['email'])) {
                    $this->tpl->setNotification("notification.enter_email", "error");
                    $notificationSet = true;
                }

                if (empty($params['password']) && !$notificationSet) {
                    $this->tpl->setNotification("notification.enter_password", "error");
                    $notificationSet = true;
                }

                if (empty($params['firstname']) && !$notificationSet) {
                    $this->tpl->setNotification("notification.enter_firstname", "error");
                    $notificationSet = true;
                }

                if (empty($params['lastname']) && !$notificationSet) {
                    $this->tpl->setNotification("notification.enter_lastname", "error");
                    $notificationSet = true;
                }

                if (empty($params['company']) && !$notificationSet) {
                    $this->tpl->setNotification("notification.enter_company", "error");
                    $notificationSet = true;
                }

                if (!$notificationSet) {
                    // No notifications were set, all fields are valid
                    if ($this->installRepo->setupDB($values)) {
                        $this->tpl->setNotification(sprintf($this->language->__("notifications.installation_success"), BASE_URL), "success");
                    } else {
                        $this->tpl->setNotification($this->language->__('notification.error_installing'), "error");
                    }
                }
            }

            return FrontcontrollerCore::redirect(BASE_URL . "/install");
        }
    }
}
