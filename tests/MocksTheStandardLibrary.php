<?php

namespace Fresco\Tests;

trait MocksTheStandardLibrary
{
    /**
     * @before
     */
    protected function initializeStdMocks()
    {
        StdMocks::setUp();
    }

    /**
     * @after
     */
    protected function cleanUpStdMocks()
    {
        StdMocks::tearDown();
    }
}
