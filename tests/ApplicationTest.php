<?php

namespace Fresco\Tests;

use Fresco\Application;
use Fresco\Contracts\Container\Container;
use Fresco\Contracts\Exceptions\ExceptionRunner;
use Fresco\Contracts\Http\Request;
use Fresco\Definitions\DiactorosDefinition;
use Fresco\Foundation\Bootstrap\Bootstrapper;
use Fresco\Foundation\Bootstrap\HandleExceptions;
use Fresco\Foundation\Bootstrap\RegisterDefinitions;
use Fresco\Foundation\Components\Definition;
use Fresco\Foundation\Components\Registry;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    use ClosesMockeryOnTearDown;

    public function test_it_inits_a_registry_when_application_is_first_created()
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf(Registry::class, $app->getRegistry());
    }

    public function test_it_defines_a_default_container_when_application_is_first_created()
    {
        $app = new Application(__DIR__);

        $this->assertInstanceOf(Container::class, $app->getContainer());
    }

    public function test_can_set_a_custom_container_definition()
    {
        $app = new Application(__DIR__, ContainerDefinitionStub::class);

        $this->assertInstanceOf(Container::class, $app->getContainer());
        $this->assertEquals(ContainerDefinitionStub::$mockInstance, $app->getContainer());
    }

    public function test_can_define_a_custom_container_definition()
    {
        $app = new Application(__DIR__);
        $app->defineContainer(ContainerDefinitionStub::class);

        $this->assertInstanceOf(Container::class, $app->getContainer());
        $this->assertEquals(ContainerDefinitionStub::$mockInstance, $app->getContainer());
    }

    public function test_application_can_define_some_component()
    {
        $app = new Application(__DIR__);
        $app->define(new ContainerDefinitionStub);

        $this->assertEquals(ContainerDefinitionStub::$mockInstance, $app->getRegistry()->getDefinitions()[Container::class]);
    }

    public function test_can_define_multiple_definitions_at_onces()
    {
        $app = new Application(__DIR__);
        $app->definitions([
            ContainerDefinitionStub::class
        ]);

        $this->assertEquals(ContainerDefinitionStub::$mockInstance, $app->getRegistry()->getDefinitions()[Container::class]);
    }

    public function test_can_define_definition_groups()
    {
        $app = new Application(__DIR__);
        $app->definitions([
            DiactorosDefinition::class
        ]);

        $this->assertInstanceOf(\Fresco\Http\Adapters\Psr7\Request::class, $app->getRegistry()->getDefinitions()[Request::class]);
    }

    public function test_will_delegate_on_all_configured_bootstrappers_to_bootstrap()
    {
        $app = new Application(__DIR__, ContainerDefinitionStub::class);

        $mockBootstrapper = \Mockery::mock(Bootstrapper::class);
        $mockBootstrapper->shouldReceive('bootstrap')->twice();

        ContainerDefinitionStub::$mockInstance->shouldReceive('make')->with(RegisterDefinitions::class)
            ->once()->andReturn($mockBootstrapper);
        ContainerDefinitionStub::$mockInstance->shouldReceive('make')->with(HandleExceptions::class)
            ->once()->andReturn($mockBootstrapper);

        $app->bootstrap();
    }

    public function test_it_is_local()
    {
        // @todo change when this gets implemented?
        $this->assertTrue((new Application(__DIR__))->isLocal());
    }

    public function test_can_bind_an_exception_runner()
    {
        $app = new Application(__DIR__);

        $app->setExceptionRunner(
            $runner = \Mockery::mock(ExceptionRunner::class)
        );

        $this->assertEquals($runner, $app->getExceptionRunner());
    }

    public function tearDown()
    {
        parent::tearDown();

        Registry::flush();
    }
}

class ContainerDefinitionStub implements Definition
{
    /**
     * @var MockInterface
     */
    public static $mockInstance;

    /**
     * @return Container
     */
    public function define()
    {
        self::$mockInstance = \Mockery::mock(Container::class);
        self::$mockInstance->shouldReceive('instance');

        return self::$mockInstance;
    }

    /**
     * @return string
     */
    public function defineAs() : string
    {
        return Container::class;
    }
}

class ERunner extends \Fresco\Exceptions\Runner
{
}