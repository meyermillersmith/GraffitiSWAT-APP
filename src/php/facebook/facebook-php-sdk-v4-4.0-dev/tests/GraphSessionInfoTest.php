<?php

use Facebook\GraphSessionInfo;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;

class GraphSessionInfoTest extends PHPUnit_Framework_TestCase
{

  public function testSessionInfo()
  {
    $params = array(
      'input_token' => FacebookTestHelper::$testSession->getToken()
    );
    $request = new FacebookRequest(
      new FacebookSession(FacebookTestHelper::getAppToken()),
      'GET',
      '/debug_token',
      $params
    );
	$response = $request->execute()->getGraphObject(GraphSessionInfo::className());
    $this->assertTrue($response instanceof GraphSessionInfo);
    $this->assertNotNull($response->getAppId());
    $this->assertTrue($response->isValid());
  }

}
