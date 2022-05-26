<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use TheRiptide\LaravelDynamicDashboard\Models\DynModel;

class DynImage extends DynModel
{
    protected $table = 'dyn_strings';
    public $component = 'image';
 
    public $rules = ['required|image', 'nullable'];    

    public function setContent($content, $type) {

        if (is_string($content)) $this->content = $content; 
        else {

            $this->content = $content->store('/dynImages', 'public');
            $this->createImageFormats($this->grabConfigSizes($type), 'dynImages', $this->content);
        }    
    }

    private function createImageFormats($sizes, $drive, $file) {

        $path = storage_path() . '/app/public/';
        $fullPath = $path  . $file;
        $imageSize = getimagesize($fullPath);

        $image = Str::of($file)->afterLast('/');
        
        foreach ($sizes as $sizeName => $width) {

            $subPath = $path . $drive . '/' . $sizeName . '/';

            if ($imageSize[0] > $width) {
                
                if (!File::exists($subPath)){

                    File::makeDirectory($subPath);
                }
        
                (new ImageManager)
                    ->make($fullPath)
                    ->widen($width)
                    ->save($subPath . $image);
            }
        }
    }

    public function grabConfigSizes($type) {

        if (!null == config('dyndash.images.sizes.' . $type))
        {
            return !null == config('dyndash.images.sizes.' . $type. '.' . $this->name)
                ? config('dyndash.images.sizes.' . $type. '.' . $this->name)
                : config('dyndash.images.sizes.' . $type);
        }
        else return $this->standardSizes();
    }

    private function standardSizes()
    {
        return [
            'smallest' => 500,
            'small' => 800,
            'smallish' => 1080,
            'medium' => 1600,
            'largish' => 2000,
            'large' => 2600,
            'largest' => 3200
        ];
    }
}
