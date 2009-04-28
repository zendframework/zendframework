<?php
/**
 * Test Class for class mapping tests.
 *
 */
class Contact
{
  public $_explicitType = 'ContactVO';
  public $id = 0;
  public $firstname = "";
  public $lastname = "";
  public $email = "";
  public $mobile = "";

  public function getASClassName()
  {
      return 'ContactVO';
  }
}
?>