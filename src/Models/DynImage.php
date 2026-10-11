<?php

namespace TheRiptide\LaravelDynamicDashboard\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use TheRiptide\LaravelDynamicDashboard\Models\DynModel;
use TheRiptide\LaravelDynamicDashboard\Factories\DynImageFactory;

class DynImage extends DynModel
{
    protected $table = 'dyn_strings';
    public $component = 'dashcomp::input.image';
 
    /** allow_svg: Laravel 12 dropped SVG from the image rule; uploads accepted it before. */
    public $rules = ['required|image:allow_svg', 'nullable'];


    protected static function newFactory()
    {
        return DynImageFactory::new();
    }

    public function setContent($content, $type) {

        if (is_string($content) | $content == null) $this->content = $content; 

        else {
            $sizes = $this->grabConfigSizes($type);

            if ($this->content) $this->deleteCurrentImage($sizes, 'dynImages');

            $this->content = $content->store('/dynImages', 'public');
            if ($content->extension() !== 'svg') 
                $this->createImageFormats($sizes, 'dynImages');
        }    
    }

    public function livewireRemoveImage($type)
    {
        $this->deleteCurrentImage($this->grabConfigSizes($type), 'dynImages');
    }

    private function deleteCurrentImage($sizes, $drive)
    {
        $path = storage_path() . '/app/public/';
        $image = $this->imageName();

        foreach ($sizes as $sizeName => $width) {

            $subPath = $path . $drive . '/' . $sizeName . '/';

            if (File::exists($subPath . $image)) File::delete($subPath. $image);
        }

        if (File::exists($path . $drive . '/' . $image)) File::delete($path . $drive . '/' . $image);
    }

    private function createImageFormats($sizes, $drive) {

        $path = storage_path() . '/app/public/';
        $fullPath = $path  . $this->content;
        $imageSize = getimagesize($fullPath);

        $image = $this->imageName();
        
        foreach ($sizes as $sizeName => $width) {

            $subPath = $path . $drive . '/' . $sizeName . '/';

            if ($imageSize[0] > $width) {
                
                if (!File::exists($subPath)){

                    File::makeDirectory($subPath);
                }
        
                // As with Intervention 2: JPEG quality 90, no EXIF auto-orientation.
                ImageManager::usingDriver(Driver::class, autoOrientation: false)
                    ->decodePath($fullPath)
                    ->scale(width: $width)
                    ->save($subPath . $image, quality: 90);
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

    private function imageName()
    {
        return Str::of($this->content)->afterLast('/');
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
