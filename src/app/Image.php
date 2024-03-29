<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Image
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Reference[] $references
 * @property-read \App\Store $store
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Thumbnail[] $thumbnails
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Image whereUrl($value)
 * @mixin \Eloquent
 */
class Image extends Model
{
    public static $rules = [
        'file' => 'required|mimes:png,gif,jpeg,jpg,bmp'
    ];

    public static $messages = [
        'file.mimes' => 'Uploaded file is not in image format',
        'file.required' => 'Image is required'
    ];

    /**
     * Get the store that owns the image.
     */
    public function store()
    {
        return $this->hasOne('App\Store');
    }

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }

    public function references()
    {
        return $this->belongsToMany('App\Reference');
    }

    public function thumbnails()
    {
        return $this->belongsToMany('App\Thumbnail')->withTimestamps();
    }

    public function mainThumbImage()
    {
        return str_replace('full_size', 'icon_size', $this->getImageUrl());
    }

    public function getImageByThumb($slug)
    {
        return str_replace('full_size', 'icon_size/' . $slug, $this->getImageUrl());
    }

    public function getReferenceId()
    {
        return $this->references()->first()->id;
    }

    public function getProductId()
    {
        return $this->products()->first()->id;
    }

    public function getImageUrl()
    {
        if (! $this->url) {
            return 'http://placehold.it/350x180';
        }
        return url('images' . $this->url);
    }

    private function cutAbsolutePath()
    {
        $messyRelativePath = parse_url($this->url, PHP_URL_PATH);
        $cleanRelativePath = str_replace('/topditop/images/', '/images/', $messyRelativePath);
        $cleanRelativePath = str_replace('/images/', '/', $cleanRelativePath);
        $this->url = $cleanRelativePath;
        $this->save();
    }

    static function replaceAllImagesPath()
    {
        $images = Image::all();

        foreach ($images as $image) {
            $image->cutAbsolutePath();
        }
    }
}