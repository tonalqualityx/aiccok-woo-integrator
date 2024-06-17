<?php defined( 'ABSPATH' ) or die( 'Sectumsempra!' );//For enemies

/* Use this class as a sample to get you started.
 * 
 * Please remove it when you no longer need it!
*/

class SampleClass {
  public $ducks;
  protected $quest;

  public function __construct($duck_names = array("Huey", "Dewey", "Louie"), $quest = "Solve the mystery of the lost lamp") {
    $this->ducks = $duck_names;
    $this->quest = $quest;
  }
}
