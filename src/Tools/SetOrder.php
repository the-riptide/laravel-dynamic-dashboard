<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Facades\Log;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

class SetOrder 
{
    use UseCache;

    private $posts;

    public function __construct($posts) 
    {
        $this->posts = $posts;
    }

    public function set($begin, $end)
    {
        if ($begin != $end){

            $order = $this->prepareOrderCollection(
                $begin, 
                $end, 
                $this->posts->pluck('dyn_order')->sort()
            );

            $this->adjustOrder($order)
                ->map(fn ($post) => $post->putInCache());

        }
    }

    private function prepareOrderCollection($begin, $end, $order)
    {   
        if ($begin < $end) {

            $order->splice($order->search($end) +1);

            return $order->splice($order->search($begin))->reverse()->values();
        }

        $order->splice($order->search($begin) +1);

        return $order->splice($order->search($end));
    }

    private function adjustOrder($order) 
    {
        $posts = DynHead::wherein('id', $this->posts->wherein('dyn_order', $order)->pluck('id'))->get();

        $used = [];
        for ($x = 0; $x < $order->count(); $x ++) {
            
            $post = $posts->whereNotIn('id', $used)->firstWhere('dyn_order', $order[$x]);
            $used[] = $post->id;

            isset($order[$x +1]) 
                ? $post->update(['dyn_order' => $order[$x + 1]])                        
                : $post->update(['dyn_order' => $order[0]]);
            
        }        

        return $this->posts->wherein('id', $used);
    }
}