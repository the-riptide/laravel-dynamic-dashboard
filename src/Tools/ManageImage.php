<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use TheRiptide\LaravelDynamicDashboard\Models\DynImage;

class ManageImage {

    public function prepSrcset ($model, $image = 'image') {

        [$path, $name] = $this->createImageString($model, $image);

        return $this->addSrcset($path, $name, $model);
    }

    protected function createImageString($model, $image) {
        
        return [$this->grabPath($model, $image), $this->grabName($model, $image)];
    }

    protected function grabName($model, $image){

        return Str::afterLast($model->$image, '/');
    }

    protected function grabPath($model, $image) {

        return Str::beforeLast($model->$image, '/');
    }

    protected function addSrcset ($path, $name, $model) {

        $string = '';
        foreach ((new DynImage)->grabConfigSizes($model->type()) as $key => $value) {

            $shortPath = $path . "/" . $key . "/"  . $name;
            
            if (Storage::drive('public')->exists($shortPath)) {
                
                $string = Str::of($string)->append(Storage::url($shortPath) . ' ' . $value . "w, ");   
            }
            else {
                break;
            }
        }
        return $string;
    }
}