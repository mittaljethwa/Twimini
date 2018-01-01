<?php

class UploadsController extends AppController {

    // public $components = array('Image'); 
    public $uses = array();
    public $helpers = array('Html', 'Form');
    public $components = array('RequestHandler');

    public function uploadProfilePicture() {

        $this->Image->set_paths(WWW_ROOT . 'img/upload/', WWW_ROOT . 'img/thumb/');
        $file = $this->request->data;
        $filepath = $this->Upload->uploadingPicture($file);
        if ($filepath !== false) {
            $this->set('message', "Upload successful");
            $this->set('data', $filepath);
            $this->set('status', "FAILURE");
        } else {
            $this->set('message', "Upload failed");
            $this->set('status', "FAILURE");
        }
    }

    public function index() {
        
        if ($this->request->is('post')) {
//            $this->request->data['Upload']['myImage'] = array(
//            'name' => 'conference_schedule.pdf',
//            'type' => 'application/pdf',
//            'tmp_name' => 'C:/WINDOWS/TEMP/php1EE.tmp',
//            'error' => 0,
//            'size' => 41737,
//);
            $this->response->type('json');
            $username=$this->Session->read('username');
            //print_r($this->request->data);
            $name = $this->request->data['Upload']['myimage']['name'];
            $location = WWW_ROOT . "img/upload/".$username.".jpg";
            if (move_uploaded_file($this->request->data['Upload']['myimage']['tmp_name'], $location))
            {
                $res = "File " . $name . " was successfully uploaded and stored";
                $this->set('status','SUCCESS');
            }
            else
            {
                $res = "Could not move " . $this->request->data['Upload']['myimage']['tmp_name'] . " to " . $location;
                $this->set('status','FAILURE');
            }
            $this->set('message',$res);
        }
    }

}

?>