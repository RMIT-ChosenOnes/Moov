<?php
class LoginTest extends \PHPUnit\Framework\TestCase
{

   public function testShouldCheckIfEmailFieldIsEmpty(){
       require '../login.php';
        $this->assertEquals("", emailAddress($email) );
    }
}
?>