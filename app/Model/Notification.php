<?php

class Notification extends AppModel
{
    public function deleteNotification($notifier_id,$notified_id,$tweet_id) {
         try{
            $db = $this->getDataSource();
            $count =$db ->query("DELETE FROM notifications WHERE notifier_id=:notifier_id AND notified_id=:notified_id AND message='like' AND tweet_id=:tweet_id",array('notifier_id'=>$notifier_id,'notified_id'=>$notified_id,'tweet_id'=>$tweet_id));
            if($count){
                return true;
            }
         }
        catch (Exception $e){
            return false;
        }        
    }
    public function findAllNotifications($id) {
        
        try{
            $db = $this->getDataSource();
            $results =$db ->query("SELECT N.id as 'Notification_id', notifier_id, fullname, username, notified_id, message, notify_time, notification_status, tweet_id from notifications N join user_details U on notifier_id=U.id where notified_id=:id",array('id'=>$id));
            return $results;
         }
        catch (Exception $e){
            return null;
        } 
    }
}
