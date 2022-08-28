<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderSetterTest extends TestCase
{

    /** @test
     * 
     * @dataProvider arrayProvider
     * 
     * 
     */
    public function can_prepare_array($begin, $end, $expected)
    {

        $order = collect([1,2,3,4,5,6,7,8,9,10]);

        if ($begin != $end){

            if ($begin < $end) {

                $order->splice($order->search($end) +1);
                $order = $order->splice($order->search($begin));
            }
            else  {

                $order->splice($order->search($begin) +1);
                $order = $order->splice($order->search($end))->reverse()->values();
            }

        }


        $this->assertEquals($expected, $order);

    }

    public function arrayProvider() 
    {
        return [
            'first' => [4, 8, collect([4,5,6,7,8,])],
            'second' => [7, 8, collect([7,8,])],
            'third' => [1, 10, collect([1,2,3,4,5,6,7,8,9,10])],
            'fourth' => [8, 4, collect([8, 7, 6, 5, 4])],
            'fifth' => [8, 7, collect([8,7, ])],
            'sixth' => [10, 1, collect([10, 9, 8, 7, 6, 5, 4, 3, 2, 1])],


        ];


    }
}
