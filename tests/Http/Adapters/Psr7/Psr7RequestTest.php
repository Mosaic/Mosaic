<?php
namespace Fresco\Tests\Http\Adapters\Psr7;

use Fresco\Contracts\Http\Request;
use Fresco\Http\Adapters\Psr7\Psr7Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Psr7RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Psr7Request
     */
    private $request;

    /**
     * @var ServerRequestInterface|\Mockery\MockInterface
     */
    private $wrappedMock;

    protected function setUp()
    {
        $this->request = new Psr7Request($this->wrappedMock = \Mockery::mock(ServerRequestInterface::class));
    }

    function test_it_implements_the_fresco_request_interface()
    {
        $this->assertInstanceOf(Request::class, $this->request);
    }

    function test_it_implements_the_psr7_server_request_interface_for_internal_usage()
    {
        $this->assertInstanceOf(ServerRequestInterface::class, $this->request);
    }

    function test_extract_an_existing_and_single_item_header_from_a_request_returns_the_item()
    {
        $this->wrappedMock->shouldReceive('getHeader')->with('accept')->once()->andReturn(['text/html']);

        $this->assertEquals('text/html', $this->request->header('accept'));
    }

    function test_extract_an_existing_and_multi_item_header_from_a_request_returns_all_the_items()
    {
        $this->wrappedMock->shouldReceive('getHeader')->with('accept')->once()->andReturn(['text/html', 'text/plain']);

        $this->assertEquals(['text/html', 'text/plain'], $this->request->header('accept'));
    }

    function test_extract_a_non_existing_header_from_a_request_returns_null()
    {
        $this->wrappedMock->shouldReceive('getHeader')->with('accept')->once()->andReturn([]);

        $this->assertNull($this->request->header('accept'));
    }

    function test_extract_a_non_existing_header_with_a_default_value_from_a_request_returns_the_default_value()
    {
        $this->wrappedMock->shouldReceive('getHeader')->with('accept')->once()->andReturn([]);

        $this->assertEquals('text/css', $this->request->header('accept', 'text/css'));
    }

    function test_can_get_the_request_method_from_it()
    {
        $this->wrappedMock->shouldReceive('getMethod')->once()->andReturn($method = uniqid());

        $this->assertEquals($method, $this->request->method());
    }

    function test_immutability_on_request_target()
    {
        $this->wrappedMock->shouldReceive('withRequestTarget')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withRequestTarget('foo'));
    }

    function test_immutability_on_method()
    {
        $this->wrappedMock->shouldReceive('withMethod')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withMethod('POST'));
    }

    function test_immutability_on_uri()
    {
        $this->wrappedMock->shouldReceive('withUri')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withUri(\Mockery::mock(UriInterface::class)));
    }

    function test_immutability_on_protocol_version()
    {
        $this->wrappedMock->shouldReceive('withProtocolVersion')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withProtocolVersion('2.0'));
    }

    function test_immutability_on_header()
    {
        $this->wrappedMock->shouldReceive('withHeader')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withHeader('cache-control', 'max-age:0'));
    }

    function test_immutability_on_added_header()
    {
        $this->wrappedMock->shouldReceive('withAddedHeader')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withAddedHeader('cache-control', 'max-age:0'));
    }

    function test_immutability_on_without_header()
    {
        $this->wrappedMock->shouldReceive('withoutHeader')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withoutHeader('cache-control'));
    }

    function test_immutability_on_body()
    {
        $this->wrappedMock->shouldReceive('withBody')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withBody(\Mockery::mock(StreamInterface::class)));
    }

    function test_immutability_on_cookie_params()
    {
        $this->wrappedMock->shouldReceive('withCookieParams')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withCookieParams(['cookie' => 'monster']));
    }

    function test_immutability_on_query_params()
    {
        $this->wrappedMock->shouldReceive('withQueryParams')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withQueryParams(['q' => 'Fresco is awesome!']));
    }

    function test_immutability_on_uploaded_files()
    {
        $this->wrappedMock->shouldReceive('withUploadedFiles')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withUploadedFiles(['logo.png' => 'FrescoLogo']));
    }

    function test_immutability_on_parsed_body()
    {
        $this->wrappedMock->shouldReceive('withParsedBody')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withParsedBody(['some' => 'parsed data']));
    }

    function test_immutability_on_attribute()
    {
        $this->wrappedMock->shouldReceive('withAttribute')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withAttribute('some', 'attribute'));
    }

    function test_immutability_on_without_attribute()
    {
        $this->wrappedMock->shouldReceive('withoutAttribute')->andReturn(\Mockery::mock(ServerRequestInterface::class));

        $this->assertNotEquals($this->request, $this->request->withoutAttribute('some'));
    }

    function test_can_get_request_parameters_out_of_a_get_request()
    {
        $this->wrappedMock->shouldReceive('getQueryParams')->andReturn(['foo' => 'bar']);

        $this->assertEquals('bar', $this->request->get('foo'));
    }

    function test_can_get_a_default_null_value_from_a_non_existing_request_parameter_out_of_a_get_request()
    {
        $this->wrappedMock->shouldReceive('getQueryParams')->andReturn(['foo' => 'bar']);

        $this->assertNull($this->request->get('baz'));
    }

    function test_can_get_a_given_default_value_from_a_non_existing_request_parameter_out_of_a_get_request()
    {
        $this->wrappedMock->shouldReceive('getQueryParams')->andReturn(['foo' => 'bar']);

        $this->assertEquals($default = uniqid(), $this->request->get('baz', $default));
    }
}