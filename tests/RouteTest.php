<?php


class RouteTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testHomeRoute()
    {
        $this->visit('/')
             ->see('Amtgard');
    }
}
