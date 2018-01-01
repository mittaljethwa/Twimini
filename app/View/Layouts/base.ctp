<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <title>Twimini</title>
        <link rel="shortcut icon" href="http://www.isp.msu.edu/connect/logos/twitter_logo.jpg" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php echo $this->Html->css('bootstrap.min'); ?>
        <?php echo $this->Html->css('base'); ?>

        <?php echo $this->Html->script('../app/webroot/js/jquery'); ?>
        <?php echo $this->Html->script('../app/webroot/js/bootstrap.min'); ?>
        <?php echo $this->Html->script('../app/webroot/js/jquery-ui'); ?>
        <?php echo $this->Html->script('../app/webroot/js/BaseScript'); ?>
        <?php echo $this->element('baseurl'); ?>
    </head>



    <div class="container">
        <?php
            echo $this->element('Login');
            //echo $this->element('ExtraProfileDetails');
        ?>

    </div>
         <?php
            echo $this->element('Register'); 
                
            echo $this->element('ForgotPassword'); 
        
            echo $this->element('ProfileDetails'); 
        ?>
    

</body>
</html>
