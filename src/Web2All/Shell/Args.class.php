<?php
/**
 * Shell arguments handling
 * 
 * This class manages arguments passed to a shell script
 * Its behavior is a littlebit vague where script arguments are concerned, so
 * everything not being an option. 
 * This because this class doesn't know anything about the *expected* options
 * and params. So it doesn't know if something is an argument or a value for the last
 * option.
 * Todo: add option hinting, so this class knows what to expect
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2007-2015 Web2All B.V.
 * @since 2007-07-20
 */
class Web2All_Shell_Args {
  
  /**
   * The parsed arguments, array with key the argument name and value
   * a string or true if valueless.
   * 
   * These are actually (long)options/switches and not arguments
   * 
   * @var array
   */
  public $args=array();
  
  /**
   * These are the script params folowing the (optional) options
   * 
   * @var array
   */
  protected $params=array();
  
  /**
   * The scriptname (as it was called)
   * 
   * @var string
   */
  protected  $scriptname;
  
  public function __construct($web2all=null) {
    // does not implement Plugin because this needs to be used before
    // Web2All_Manager object is constructed.
    if(array_key_exists('argv',$_SERVER)){
      $this->parseArguments($_SERVER['argv']);
    }
  }
  
  /**
   * Parse argv into an array with key-value pairs
   * 
   * @param array $argv
   * @return array
   */
  public function parseArguments($argv) {
    $this->args = array();
    $this->params=array();
    $prev_arg='';
    $first=true;
    foreach ($argv as $arg) {
      // the first arg is the scriptname
      if ($first){
        $first=false;
        $this->scriptname=$arg;
      } elseif (preg_match('/^--?([^=]+)=([^\s]*)$/',$arg,$reg)) {
        // ok, we have something like --option=value
        if($prev_arg && count($this->params)>0){
          // if we have params, append them to the last (previous) option
          if($this->args[$prev_arg]===true){
            $this->args[$prev_arg]=implode(' ', $this->params);
          }else{
            $this->args[$prev_arg].=' '.implode(' ', $this->params);
          }
        }
        $this->args[$reg[1]] = $reg[2];
        $prev_arg=$reg[1];
        // as we still have options, reset the params as we expect them to be after the options
        $this->params=array();
      } elseif(preg_match('/^--?([a-zA-Z0-9_+-]+)$/',$arg,$reg)) {
        // we have something like --option or -option or --opt-ion+more
        if($prev_arg && count($this->params)>0){
          // if we have params, append them to the last (previous) option
          if($this->args[$prev_arg]===true){
            $this->args[$prev_arg]=implode(' ', $this->params);
          }else{
            $this->args[$prev_arg].=' '.implode(' ', $this->params);
          }
        }
        $this->args[$reg[1]] = true;
        $prev_arg=$reg[1];
        // as we still have options, reset the params as we expect them to be after the options
        $this->params=array();
      } else {
        // not a param, so its a value (belonging to the last param) or an argument
        if($prev_arg && $this->args[$prev_arg]===true){
          // okay, we had a previous option, and it was true, so it didn't have a value yet, 
          // so this value might be for that option
          // But we will delay adding it, if another option follows, we will add this (and
          // possibly more) as value to this option. Or we will do so when parsing ends.
          
          // because we do not know for sure if the option actually accepts values, this could also 
          // be a script param. So add it.
          $this->params[]=$arg;
        } elseif($prev_arg) {
          // okay, we had a previous option, but it was not true...this means it already 
          // has a value.
          // if there are no more other options, then the chance is high this is actually a script
          // param, so add it there. It will be reset if we find another option.
          $this->params[]=$arg;
        }else{
          // there was no previous option, so it must be a script param
          // if we get an option after this then thats weird and this param will be dropped.
          $this->params[]=$arg;
        }
      }
    
    }
    // ok we parsed all arguments, lets finish up
    if($prev_arg && count($this->params)>0 && $this->args[$prev_arg]===true){
      // if we have params and options and the last option doesn't have a value yet, 
      // set its value to the first argument. But keep the first argument because we do not 
      // actually know if its an argument or belongs to the last option.
      $this->args[$prev_arg]=$this->params[0];
    }
    
    return $this->args;
  }
  
  /**
   * Get a longopt value by name
   * 
   * It would be better if this method was named getOpt, but its too late to change that
   * 
   * @param string $name  longopt name
   * @return mixed  false if the param is not present, true or string if it is present
   */
  public function getArg($name) {
    if(array_key_exists($name,$this->args)){
      return $this->args[$name];
    }else{
      return false;
    }
  }
  
  /**
   * Get all script arguments/params
   * 
   * Basically this are all params which appear after the last option. 
   * Possibly it includes the value of the last option.
   * 
   * @return string[]
   */
  public function getParams() {
    return $this->params;
  }
  
  /**
  * Get the scriptname
  * 
  * @return string
  */
  public function getScriptname() {
    return $this->scriptname;
  }
  
}
?>