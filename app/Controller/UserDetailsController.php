<?php
App::uses('CakeEmail', 'Network/Email');
class UserDetailsController extends AppController
{
    public $helpers = array('Js' => array('Jquery'));
    public $components = array('RequestHandler');
    
            
public function register()
    {
         
        $this->set('message', "");
        $this->set('status', "");
        //Method 3:
        try
        {
            $this->response->type('json');
            if($this->request->is('post')) //tocheck is form is of post type
            {
                $data=$this->request->data;                
                if(!$this->emptyDataCheck($data))
                {
                    if($this->uniqueUserName($data['username']) && $this->uniqueEmail($data['email']))
                    {
                        if($this->UserDetail->save($data))   
                        {
                            $id=$this->UserDetail->id;
                            $this->Session->write('user', $id);
                            //$this->Session->setFlash('User Saved!');
                            $this->set('message', "inserted successfully ".$data['username']." user");
                            $this->set('status',"SUCCESS");
                        }
                        else
                        {
                            //$this->Session->setFlash('User was not Saved. please try again');
                            $this->set('message',"User was not Saved. please try again");
                            $this->set('status',"FAILURE");
                        }
                    }
                    else
                    {
                        //$this->Session->setFlash('User name not unique');
                        $this->set('message','User name/EMAIL not unique');
                        $this->set('status',"FAILURE");
                    }
                }
                else
                {
                    $this->set('message','Fields are empty');
                    $this->set('status',"FAILURE");
                }
            }
        } 
        catch (Exception $ex) 
        {
            $this->set('message',"Exception in registering user");
            $this->set('status',"FAILURE");
        }       
    }
    public function login()
    {
        $this->response->type('json');
        if($this->request->is('post'))
        {
            
            $username=$this->request->data('usernameemail');
            $password=$this->request->data('loginpassword');
            $type=$this->request->data('type');
            $this->set('dataip',$username." ".$password." ".$type);
            if($type=='email'){
            $result=$this->UserDetail->findByEmailPassword($username,$password);}
            else{
            $result=$this->UserDetail->findByUsernamePassword($username,$password);}
            if(count($result)>0)                        
            {    $id=$result[0]['user_details']['id'];
                $uname=$result[0]['user_details']['username'];
            }
            else
            {    $id = null;}
            $this->set('data',$result);
            if($id != null)
            {
                $this->Session->write('user', $id);
                $this->Session->write('username', $uname);
                $this->set('status',"SUCCESS");
                $this->set('message',"Login successfully");
                
            }
            else{
                $this->set('status',"FAILURE");
                $this->set('message',"Incorrect username and password");
                //$this->redirect (array('action'=>'index'));
            }
             //$this->set('status',"SUCCESS");
        }
        else {
            $this->set('status',"FAILURE");
            $this->set('message',"Improper request");
        }
    }
    public function uniqueUserName($username)
    {
        //$this->set('uniqueusernamestatus', true);
        $exist=count($this->UserDetail->findByUsername($username));
        if($exist>0)
        {   
            return false;
            //$this->set('unique',"false");
        }
        else
        {    
            return true;
            //$this->set('unique',"true");
        }
    }
     public function checkUserName()
    {
         $this->response->type('json');
        //$this->set('uniqueusernamestatus', true);
        $exist=count($this->UserDetail->findByUsername($this->request->data('username')));
        if($exist>0)
        {   
            $this->set("status","SUCCESS");
            $this->set("message","Username not available");
            //$this->set('unique',"false");
        }
        else
        {    
            $this->set("status","FAILURE");
            $this->set("message","Username available");
            //$this->set('unique',"true");
        }
    }
    public function checkEmail()
    {
        $this->response->type('json');
        //$this->set('uniqueusernamestatus', true);
        $exist=count($this->UserDetail->findByEmail($this->request->data('email')));
        if($exist>0)
        {   
            $this->set("status","SUCCESS");
            $this->set("message","Email not available");
            //$this->set('unique',"false");
        }
        else
        {    
            $this->set("status","FAILURE");
            $this->set("message","Email available");
            //$this->set('unique',"true");
        }
    }
    public function uniqueEmail($email)
    {
        //$this->set('uniqueusernamestatus', true);
        $exist=count($this->UserDetail->findByEmail($email));
        if($exist>0)
        {   
            return false;
            //$this->set('unique',"false");
        }
        else
        {    
            return true;
            //$this->set('unique',"true");
        }
    }
    public function emptyDataCheck($data) 
    {
        if($data['username']!=null && $data['email']!=null && $data['password']!=null && $data['fullname']!=null)
        {    
            return false;
        }
        else 
        {
            return true;
        }
    }
    public function homePage($id = null) 
    {
       if($id != null)
       {
           $sessionid=$this->Session->read('user');
           if($sessionid == $id)         
           {    $this->set('id',$id);
                $this->layout= 'ajax';
           }
            else {
                $this->redirect(array('action'=>'index'));
            }
       }
       //$this->Session->destroy();
        
    }
    
    public function logout(){
        $id=$this->Session->read('user');
        $this->response->type('json');
        if($id != null)
        {
            $this->Session->destroy();
            //$this->redirect(array('action'=>'index'));
            $this->set('status',"SUCCESS");
            $this->set('message',"logged out successfully");
        }
        else
        {
            $this->set('status',"FAILURE");
            $this->set('message',"cannot logout");
        }
        
    }
    
    public function editBio()
    {
        $bio=$this->request->data['bio'];
        $id= $this->Session->read('user');
        $this->response->type('json');
        if(!empty($this->request->data))
        {
            $this->layout = 'ajax';
            $this->UserDetail->id = $id;
            $this->data=$this->UserDetail->saveField('bio',$bio);
            $this->set('status',"SUCCESS");
            $this->set('message',"Bio updated");
        }
        else{
            $this->set('status',"FAILURE");
            $this->set('message',"Could not update bio");
        }
    }
    
    public function getProfile()
    {
        $id=  $this->request->data['id'];
        $this->response->type('json');
        $this->layout = 'ajax';
        if($id!=NULL)
        {
            $data=  $this->UserDetail->read(Null,$id);
            $id = $data['UserDetail']['id'];
            $data['location'] = "../img/upload/".$id.".jpg";
            $this->set('data',$data);
            $this->set('status',"SUCCESS");
            $this->set('message',"Profile returned successfully");
        }
        else{
            $this->set('data',NULL);
            $this->set('status',"FAILURE");
            $this->set('message',"couldnt fetch profile");
        }
    }
    
	public function getBio()
    {
        $id=$this->Session->read('user');
        $this->response->type('json');
        $this->layout = 'ajax';
        if($id!=NULL)
        {
            $data=  $this->UserDetail->readBio($id);
            $this->set('data',$data);
            $this->set('status',"SUCCESS");
            $this->set('message',"Bio returned successfully");
        }
        else{
            $this->set('data',NULL);
            $this->set('status',"FAILURE");
            $this->set('message',"couldnt fetch bio");
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
}
    
    public function forgotpasswordemail() {
        $this->layout="ajax";
        $this->response->type('json');
        $email=$this->request->data('email');
        if($email===NULL)
            {
            $this->set('status','FAILURE');
            $this->set('message','Email address does not exists');
        }
        else{
            $data=  $this->UserDetail->findByemail($email);
            if(count($data)!=1)
            {
                $this->set('status','FAILURE');
                $this->set('message','Email address does not exists');
            }else{
                $userid=$data['UserDetail']['id'];
                $fullname=$data['UserDetail']['fullname'];
                $password=  $this->generateRandomString();
                $this->UserDetail->id=$userid;
                if($this->UserDetail->saveField('password',$password) && $this->UserDetail->saveField('resetpassword',1))
                {
                    try{
                      $Email = new CakeEmail('gmail');
                      $Email->to($email);
                      $Email->from("amit.mittal.twimini@gmail.com");
                      $Email->subject('Forgot Password');
                      $Email->send('Hello '.$fullname.', your new twimini password is '.$password.'.Kindly change your password immediately');
                      $this->set('status','SUCCESS');
                      $this->set('message','New password has been sent on email');
                    }catch(Exception $e){

                          $this->set('status','FAILURE');
                          $this->set('message','Could not sent email');   
                    }
                }
                else{
                    $this->set('status','FAILURE');
                    $this->set('message','Could not sent email');            
                }
            }
        }
    }
    public function resetpassword() {
        $this->layout="ajax";
        $this->response->type('json');
        $password=  $this->request->data('password');
        if($password===null){
            $this->set('status','FAILURE');
            $this->set('message','Parameter missing');            
        }
        else
        {
            $this->UserDetail->id=$this->Session->read('user');
            if($this->UserDetail->saveField('password',$password) && $this->UserDetail->saveField('resetpassword',0))
            {
                $this->set('status','SUCCESS');
                $this->set('message','Password saved');
            }
            else{
                $this->set('status','FAILURE');
                $this->set('message','Could not save new password'); 
            }
        }
        
    }
	public function suggest()
    {
        $this->response->type('json');
        $id=$this->Session->read('user');
        $limit=$this->request->data['limit'];
        $present_id = $this->request->data['id'];
        if($id!==null)
        {
            $data=$this->UserDetail->getSuggestions($id,$limit,$present_id);
            if($data!==null)
            {
                $this->set('data',$data);
                $this->set('status','SUCCESS');
                $this->set('message','Suggestions fetched successfully');
            }
            else
            {
                $this->set('status','FAILURE');
                $this->set('data',null);
                $this->set('message','No suggestions to display');
            }
        }
    }
    
    public function search() {
        $this->response->type('json');
        $id=$this->Session->read('user');
        $keyword=$this->request->data['keyword'];
        $display=$this->request->data['display'];
        if($keyword != null)
        {
            $searchResult=$this->UserDetail->search($id,$keyword,$display);
            if($searchResult!==null)
            {
                $this->set('data',$searchResult);
                $this->set('status','SUCCESS');
                $this->set('message','Searched successfully');
            }
            else
            {
                $this->set('status','FAILURE');
                $this->set('data',null);
                $this->set('message','No results found ');
            }
        }
    }
}