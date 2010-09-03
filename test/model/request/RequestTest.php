<?php


/**
 * Description of RequestTest
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class RequestTest extends PHPUnit_Framework_TestCase {

    public function testBasicRequest() {
        $request = new Request("/my-request");
        $this->assertEquals($request->getRequestedResource(), "my-request");
    }

    public function testBasicRequest2() {
        $request = new Request("/");
        $this->assertEquals($request->getRequestedResource(), "home");
    }

    public function testParamStrip() {
        $request = new Request("/my-request?foo=bar");
        $this->assertEquals($request->getRequestedResource(), "my-request");
    }

    public function testParamStore() {
        $request = new Request("/my-request?foo=bar", array("foo" => "bar"));
        $params = $request->getMappedParameters();
        $this->assertEquals($params["foo"], "bar");
    }

    public function testParamSplit() {
        $request = new Request("/my-request/param1/param2");
        $params = $request->getParameters();
        $this->assertEquals($params[0], "param1");
        $this->assertEquals($params[1], "param2");
    }

    public function testArrayMap() {
        $request = new Request("/my-request/param1/param2");
        $request->applyParameterMap(array("foo","bar"));
        $params = $request->getMappedParameters();
        $this->assertEquals($params["foo"], "param1");
        $this->assertEquals($params["bar"], "param2");

    }
}

