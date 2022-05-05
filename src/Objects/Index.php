<?php 

namespace TheRiptide\LaravelDynamicDashboard\Objects;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class Index extends Base 
{
    private $type;
    public $heads;
    public $canDelete;

    public function __construct(string $type)
    {        
        $this->type = $this->getType($type);
        $this->heads = $this->prepTableHeads();

        $this->canDelete = ! isset($this->type->canDelete) || $this->type->canDelete == true;
    }

    public function setValue($post, $field) : string
    {
        if (isset($this->type->index()[$field]['function']) && $this->type->index()[$field]['function']) 
        {
            $function = 'get' . Str::ucfirst($field);
            return $this->type->$function($post);
        } 

        return $post->$field;
    }

    private function prepTableHeads() : Collection
    {
        return $this->type->index()->map(
            function($item, $key) {

                if (is_array($item) || $item instanceof Collection) return $key;
                
                return $item;
            }
        )->filter();
    }
}