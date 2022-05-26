<?php 

namespace TheRiptide\LaravelDynamicDashboard\Tools;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class ManageImage {



    public function prepSrcset ($model, $image = 'url') {

        $array = $this->createImageString($model, $image);

        return $this->addSrcset($array['path'], $array['name'], $model);
    }

    public function grabSize($model, $size, $image) {

        $path = $this->grabPath($model, $image);
        $name = $this->grabName($model, $image);

        return $path. '/' . $size . '/' . $name;
    }

    protected function setImages($post, $drive, $files) {

        foreach ($files as $file) {

            $this->setImage($post, $drive, $file);
        }
    }

    public function setImage($post, $drive, $file = 'image') {
                
        if (request()->file($file)->extension() !== 'svg') {
            
            $size = $this->grabConfigSizes($post);
            $this->createImageFormats($size, $drive, $this->grabName($post, $file), request()->file($file));
        }
    }

    protected function createImageFormatsFromString($sizes, $drive, $name, $file) {

        $size = getimagesizefromstring($file);
        
        foreach ($sizes as $type => $width) {

            if ($size[0] > $width) {

                $this->widenImage($width, $drive, $file, $name, $type);

            }
        }
    }

    public function widenImage($width, $drive, $file, $name, $type) {

        $path = storage_path() . '/app/public/'. $drive . '/' . $type . '/';

        if (!File::exists($path)){

            File::makeDirectory($path);
        }

        (new ImageManager)
            ->make($file)
            ->widen($width)
            ->save($path . $name);
    }

    protected function createImageString($model, $image) {
        
        $path  =  $this->grabPath($model, $image);
        $name = $this->grabName($model, $image);
        
        return ['path' => $path, 'name' => $name];
    }

    protected function addSrcset ($path, $name, $model) {

        $sizes = $this->grabConfigSizes($model);

        $string = '';
        foreach ($sizes as $key => $value) {

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

    protected function grabName($model, $image){

        return Str::afterLast($model->$image, '/');
    }

    protected function grabPath($model, $image) {

        return Str::beforeLast($model->$image, '/');
    }

    private function grabConfigSizes($post) {


        return config('image.sizes.' . Str::lower(class_basename($post))) ?? config('image.sizes.standard'); 
    }

}