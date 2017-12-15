<?php
use PHPUnit\Framework\TestCase;

class Web2All_Shell_ArgsTest extends TestCase
{
  /**
   * Web2All framework
   *
   * @var Web2All_Manager_Main
   */
  protected static $Web2All;
  
  public static function setUpBeforeClass()
  {
    self::$Web2All = new Web2All_Manager_Main();
  }
  
  /**
   * Test con storage loading
   * 
   * @param array $argv
   * @param array $expected_opts
   * @param array $expected_args
   * @dataProvider argumentProvider
   */
  public function testArgs($argv,$expected_opts,$expected_args)
  {
    $args = self::$Web2All->Factory->Web2All_Shell_Args();
    $opts = $args->parseArguments($argv);
    $this->assertEquals($expected_opts, $opts, 'parsed options and switches');
    $params=$args->getParams();
    $this->assertEquals($expected_args, $params, 'parsed arguments');
  }

  /**
   * Provide tasks
   * 
   * @return array
   */
  public function argumentProvider()
  {
    return array(
//      test.php --test
      array (  array (  'test.php',  '--test' ),  array ( 'test' => true ),  array ( ) ),
//      test.php -t
      array (  array (  'test.php',  '-t' ),  array ( 't' => true ),  array ( ) ),
//      test.php -tp
      array (  array (  'test.php',  '-tp' ),  array ( 'tp' => true ),  array ( ) ),
//      test.php -t --test
      array (  array (  'test.php',  '-t',  '--test' ),  array ( 't' => true, 'test' => true ),  array ( ) ),
//      test.php -a b
      array (  array (  'test.php',  '-a',  'b' ),  array ( 'a' => 'b' ),  array (  'b' ) ),
//      test.php -a=b
      array (  array (  'test.php',  '-a=b' ),  array ( 'a' => 'b' ),  array ( ) ),
//      test.php -a "a b c"
      array (  array (  'test.php',  '-a',  'a b c' ),  array ( 'a' => 'a b c' ),  array (  'a b c' ) ),
//      test.php --anoption "a b c"
      array (  array (  'test.php',  '--anoption',  'a b c' ),  array ( 'anoption' => 'a b c' ),  array (  'a b c' ) ),
//      test.php arg1
      array (  array (  'test.php',  'arg1' ),  array ( ),  array (  'arg1' ) ),
//      test.php arg1 arg2
      array (  array (  'test.php',  'arg1',  'arg2' ),  array ( ),  array (  'arg1',  'arg2' ) ),
//      test.php arg1 arg2 arg3
      array (  array (  'test.php',  'arg1',  'arg2',  'arg3' ),  array ( ),  array (  'arg1',  'arg2',  'arg3' ) ),
//      test.php --bool-opt1 --bool-opt2 --str-opt test --int-opt=1 arg1 arg2 arg3
      array (  array (  'test.php',  '--bool-opt1',  '--bool-opt2',  '--str-opt',  'test',  '--int-opt=1',  'arg1',  'arg2',  'arg3' ),  array ( 'bool-opt1' => true, 'bool-opt2' => true, 'str-opt' => 'test', 'int-opt' => '1' ),  array (  'arg1',  'arg2',  'arg3' ) )
    );
  }
}
?>