<?php
class test extends \PHPUnit\Framework\TestCase
{
	private function _execute(array $params = array()) {
		$_POST = $params;
		ob_start();
		require_once '../login.php';
		return ob_get_clean();
	}


    public function testShouldCheckIfEmailFieldIsEmpty(){
        //require '../login.php';
        //$_GET['url'] = '';
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
            
        $param = array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>'');
        //$session = array('moov_user_registration_success'=>'TRUE');
        //$this->assertNotNull('loginEmailAddress');
        //$this->assertRegExp('/[0-9]/', 'test');
        //$this->assertSame(array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>''), $this->_execute($param));
        //$this->assertTag(array('referrerUrl'=>'', 'loginEmailAddress'=>'fisherlim20@outlook.com', 'loginPassword'=>''), $this->_execute($param));
        //$this->assertEquals($_POST['loginEmailAddress']='fisherlim20@outlook.com', $this->_execute($param));
        //$this->assertClassHasAttribute('');
        $this->assertFileEquals('/var/www/html/moov/unit-test/test.html', $this->_execute($param));
    }
}
?>
