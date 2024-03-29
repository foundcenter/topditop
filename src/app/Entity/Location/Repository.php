<?php

namespace App\Entity\Location;

use App\Field;
use App\Location;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Repository
{
    /**
     * @param Request $request
     * @return Location
     */
    public function saveNew(Request $request)
    {   
        $locale = App::getLocale();

        $location = new Location();
        $location->key = $request->key;       
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->translateOrNew($locale)->name = $request->name;       
        $location->is_featured = $request->is_featured;
        $location->save();

        return $location;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return Location::withTranslation()->get();
    }

    public function listAll()
    {
        $locations = Location::with('stores')->orderBy('long_name')->get();

        $all = [];

        foreach ($locations as $location) {
            $presenter = [];

            $translate = $location->translate();

            $presenter["id"] = $location->id;
            $presenter["key"] = $location->key;
            $presenter["name"] = $translate['name'];

            $presenter["latitude"] = (float) $location->latitude;
            $presenter["longitude"] = (float) $location->longitude;
            $presenter["is_featured"] = $location->is_featured;

            /** @var Store $store */
            foreach ($location->stores as $store) {

                $presenter["stores"] []= [
                    "store_name" => $store->store_name,
                    "latitude" => $store->getLatitude(),
                    "longitude" => $store->getLongitude(),
                ];
            }
            $all []= $presenter;
        }

        return $all;
    }

    /**
     * @param Location $location
     * @return mixed
     */
    public function get(Location $location)
    {
        return Location::find($location->id);
    }

    /**
     * @param Request $request
     * @param Location $location
     * @return Location
     */
    public function update(Request $request, Location $location)
    {
        $locale = App::getLocale();
        $location->key = $request->key;
        $location->translateOrNew($locale)->name = $request->name;
        $location->longitude = $request->longitude;
        $location->latitude = $request->latitude;
        $location->is_featured = $request->is_featured;

        $location->save();

        return $location;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listEnhancedLocations()
    {
        $locations = Location::with(['stores.profile.fields', 'stores.image', 'translations'])->withCount('stores')->has('stores', '>=', 10)->get();

        $list = [];

        foreach ($locations as $location) {
            $presenter = [];

            $translate = $location->translate();

            $presenter['id'] = $location->id;
            $presenter['key'] = $location->key;
            $presenter['name'] = $translate['name'];
            $presenter['numStores'] = $location->stores_count;

            $presenter['latitude'] = (float) $location->latitude;
            $presenter['longitude'] = (float) $location->longitude;

            /** @var Store $store */
            foreach ($location->stores as $store) {
                $presenter['stores'] [] = [
                    'store_id' => $store->id,
                    'store_name' => $store->store_name,
                    'latitude' => (float) $store->getLatitude(),
                    'longitude' => (float) $store->getLongitude(),
                    'img' => $store->getStoreLogo(),
                    'numproducts' => $store->getNumberOfProducts(),
                    'numreferences' => $store->getNumberOfReferences(),
                ];
            }

            $list [] = $presenter;
        }

        return $list;
    }

    public function getAndCountLocationsWithActiveStores()
    {
        return Location::with('translations')->withCount(['stores' => function ($query) {
            $query->where('status', true);
        }])->get();
    }

    /**
     * @param Location $location
     * @return bool|null
     */
    public function delete(Location $location)
    {
        return $location->delete();
    }
}