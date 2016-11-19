<?php

namespace Jasny;

use Jasny\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Jasny\Controller\TestHelper;

/**
 * @covers Jasny\Controller
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;
    
    /**
     * Test running controller
     */
    public function testInvoke()
    {
        $test = $this;
        
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $finalResponse = $this->createMock(ResponseInterface::class);

        $controller = $this->getController();
        $controller->expects($this->once())->method('run')
            ->willReturnCallback(\Closure::bind(function() use ($test, $request, $response, $finalResponse) {
                $test->assertSame($request, $this->getRequest());
                $test->assertSame($response, $this->getResponse());
                
                return $finalResponse;
            }, $controller, Controller::class));

        $result = $controller($request, $response);

        $this->assertEquals($finalResponse, $result);
    }
    
    public function testSetResponse()
    {
        $response = $this->createMock(ResponseInterface::class);
        
        $controller = $this->getController();
        $controller->setResponse($response);
        
        $this->assertSame($response, $controller->getResponse());
    }
    
    /**
     * @expectedException LogicException
     */
    public function testGetRequestUninvoked()
    {
        $this->getController()->getRequest();
    }
    
    /**
     * @expectedException LogicException
     */
    public function testGetResponseUninvoked()
    {
        $this->getController()->getResponse();
    }
    
    
    public function testUseSession()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $controller = $this->getController(['useSession']);
        $controller->expects($this->once())->method('useSession');
        
        $controller($request, $response);
    }
}
