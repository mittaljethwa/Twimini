<?php

class NotificationsController extends AppController {

    public function shownotifications() {
        $sessionid = $this->Session->read('user');
        $id = $this->request->data('id');
        $this->response->type('json');
        $this->layout = 'ajax';
        if ($id != NULL) {
            if ($sessionid == $id) {
                $data = $this->Notification->findAllNotifications($id);
                $this->set('data', $data);
                $this->success("Notification received");
            } else {
                $this->failure("please login again");
                $this->set('data', NULL);
            }
        } else {
            $this->failure("Parameter missing");
            $this->set('data', NULL);
        }
    }

    public function failure($message) {
        $this->set('message', $message);
        $this->set('status', "FAILURE");
    }

    public function success($message) {
        $this->set('message', $message);
        $this->set('status', "SUCCESS");
    }

}
