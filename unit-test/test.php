<?php
$_SESSION['moov_user_registration_success'] = TRUE;

class test extends \PHPUnit\Framework\TestCase
{
	private function _execute(array $params = array()) {
		$_POST = $params;
		ob_start();
		include '../login.php';
		return ob_get_clean();
	}


    public function testShouldCheckIfEmailFieldIsEmpty(){
        //require '../login.php';
        $_GET['url'] = '';
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
            
        $param = array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>'');
        //$this->assertNotNull('loginEmailAddress');
        //$this->assertRegExp('/[0-9]/', 'test');
        //$this->assertSame(array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>''), $this->_execute($param));
        //$this->assertTag(array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>''), $this->_execute($param));
        $this->assertEquals('{}', $this->_execute($param));
    }
}
?>
