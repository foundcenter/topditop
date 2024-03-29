<?php

namespace App\Http\Controllers;

use App\Category;
use App\Field;
use App\FieldProfile;
use App\Helpers\ImportRow;
use App\Http\Requests\Stores\FullStoreSetupRequest;
use App\Image;
use App\Location;
use App\Package;
use App\Product;
use App\Profile;
use App\Reference;
use App\Services\ImportService;
use App\Services\PackageService;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoreController extends BaseController
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings()
    {
        $store = $this->current_store;
        $selected_categories = $store->categories;
        $categories = Category::all();
        $numberOfCategories = count($selected_categories);
        $numberOfReferences = Reference::where(['store_id' => $store->id])->count();
        $numberOfProducts = Product::where(['store_id' => $store->id])->count();

        return view('dashboard.settings')
            ->with('numberOfReferences', $numberOfReferences)
            ->with('numberOfProducts', $numberOfProducts)
            ->with('selected_categories', $selected_categories)
            ->with('categories', $categories)
            ->with('numberOfCategories', $numberOfCategories)
            ->with('store', $this->current_store);
    }

    public function settingsImage()
    {
        return view('dashboard.settings-image')->with('store', $this->current_store);
    }

    public function settingsProfile()
    {
        return view('dashboard.settings-password')->with('store', $this->current_store);
    }

    public function settingsEditPass(Request $request)
    {
        $locale = app()->getLocale();

        if (Input::get('newsletter_key_1')) {

            $user = Auth::user();

            $field = Field::where('key', 'newsletter_key_1')->get()->first();
            if (!is_object($field)) {
                $field = new Field();
                $field->key = 'newsletter_key_1';
                $field->save();
            }
            $fieldProfilePIVOT = FieldProfile::where(['field_id' => $field->id, 'profile_id' => $user->store->profile->id])->get()->first();
            if ($fieldProfilePIVOT != null) {
                $fieldProfilePIVOT->translateOrNew($locale)->selected = Input::get('newsletter_key_1');
                $fieldProfilePIVOT->save();
            } else {

                $fieldProfilePIVOT = new FieldProfile();
                $fieldProfilePIVOT->field_id = $field->id;
                $fieldProfilePIVOT->profile_id = $user->store->profile->id;

                /** @var FieldProfile $fieldProfilePIVOT */
                $fieldProfilePIVOT->translateOrNew($locale)->selected = Input::get('newsletter_key_1');
                $fieldProfilePIVOT->save();
            }
        }

        if (Input::get('newsletter_key_2')) {

            $user = Auth::user();

            $field = Field::where('key', 'newsletter_key_2')->get()->first();
            if (!is_object($field)) {
                $field = new Field();
                $field->key = 'newsletter_key_2';
                $field->save();
            }
            $fieldProfilePIVOT = FieldProfile::where(['field_id' => $field->id, 'profile_id' => $user->store->profile->id])->get()->first();
            if ($fieldProfilePIVOT != null) {
                $fieldProfilePIVOT->translateOrNew($locale)->selected = Input::get('newsletter_key_2');
                $fieldProfilePIVOT->save();
            } else {

                $fieldProfilePIVOT = new FieldProfile();
                $fieldProfilePIVOT->field_id = $field->id;
                $fieldProfilePIVOT->profile_id = $user->store->profile->id;

                /** @var FieldProfile $fieldProfilePIVOT */
                $fieldProfilePIVOT->translateOrNew($locale)->selected = Input::get('newsletter_key_2');
                $fieldProfilePIVOT->save();
            }
        }

        if (Input::get('old_password') && Input::get('password')) {

            $user = Auth::user();
            $rules = array(
                'old_password' => '',
                'password' => 'alphaNum|between:6,16'
            );

            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return redirect()->route('dashboard_settings_profile')->with('fail', 'Rules are not met.');
            } else {
                if (!Hash::check(Input::get('old_password'), $user->password)) {
                    return redirect()->route('dashboard_settings_profile')->with('fail', 'Your old password does not match');
                } else {
                    $user->password = bcrypt(Input::get('password'));
                    $user->save();
                    return redirect()->route('dashboard_settings_profile')->with('success', 'Password has been changed');
                }
            }
        } else {
            return redirect()->route('dashboard_settings_profile');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function settingsImageSave(Request $request)
    {
        $store = $this->current_store;
        $file = $request->file('store_image');

        if (isset($file)) {
            // TODO: cleanup old image from disk
            $destinationPath = base_path() . "/images/";
            $fileName = '/store_profile_' . str_random(6). '_' . str_slug($this->current_store->store_name) . '.' . $file->getClientOriginalExtension();

            $file->move($destinationPath, $fileName);

            $image_url_full = $fileName;
            if (!empty($image_url_full)) {
                $image = new Image();
                $image->name = $store->store_name;
                $image->url = $image_url_full;
                $image->title = $store->store_name;
                $image->save();
                $store->image()->associate($image);
            }
        }

        $store->save();
        return redirect()->route('dashboard_settings_image');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = new Store($request->all());

        $package = Package::find($request->package_id);
        $profile = new Profile();

        $user = new User();
        $user->email = $request->user_email;
        $user->name = "Store Owner " . uniqid();
        $user->password = bcrypt("topditop");
        $user->store()->associate($store);

        $response = response('Success', 200)->header('Content-Type', 'text/plain');
        try {
            $store->save();
            $profile->package()->associate($package);
            $profile->store()->associate($store);
            $profile->save();
            $store->user()->save($user);
            $user->store()->associate($store);

        } catch (\Illuminate\Database\QueryException $e) {
            $response = response($e->errorInfo, 400)->header('Content-Type', 'text/plain');
        }

        return $response;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $store = $this->current_store;

        $selected_categories = $store->categories;
        $categories = Category::all();
        $numberOfCategories = count($selected_categories);
        
        if ($store->status == 0) {
            return view('dashboard.home-forbidden')
                ->with('store', $this->current_store);
        } else {
            $numberOfReferences = Reference::where(['store_id' => $store->id])->count();
            $numOfProducts = Product::where(['store_id' => $store->id])->count();
            return view('dashboard.home')
                ->with('store', $this->current_store)
                ->with('numberOfReferences', $numberOfReferences)
                ->with('numOfProducts', $numOfProducts)
                ->with('selected_categories', $selected_categories)
                ->with('categories', $categories)
                ->with('numberOfCategories', $numberOfCategories);
        }
    }

    /**
     * @param Store $store
     * @return Store
     */
    public function show(Store $store)
    {
        $store->user_email = $store->user->email;

        return $store;
    }

    /**
     * @return mixed
     */
    public function inactiveStores()
    {
        $stores = Store::where('status', 0)->get();
        $arrayOfData = array();

        foreach ($stores as $store) {
            if (!empty($store->user->registerfields))
                $registerFields = $store->user->registerfields;

            $user = $store->user;
            $profile = $store->profile;
            if (is_object($profile)) {
                $package = $profile->package;
                $arrayOfData[] =
                    array(
                        "id" => $store->id,
                        "status" => $store->status,
                        "created_at" => $store->created_at->__toString(),
                        "updated_at" => $store->updated_at->__toString(),
                        "user_id" => $user["id"],
                        "user_email" => $user["email"],
                        "profile_id" => $profile["id"],
                        "package_id" => $package["id"],
                        "package_name" => $package["name"],
                        "user" => $store->user,
                        "registerfields" => $registerFields
                    );
            }
        }
        return $arrayOfData;
    }

    /**
     * @return mixed
     */
    public function activeStores()
    {
        $stores = Store::active()->get();
        $arrayOfData = array();

        foreach ($stores as $store) {
            $user = $store->user;
            $profile = $store->profile;
            if (is_object($profile)) {
                $regFields = array();
                foreach ($user->registerfields as $registerfield) {
                    $regFields[$registerfield->name] = $registerfield->pivot->valueEntered;
                }
                $package = $profile->package;
                $arrayOfData[] =
                    array(
                        "id" => $store->id,
                        "status" => $store->status,
                        "created_at" => $store->created_at->__toString(),
                        "updated_at" => $store->updated_at->__toString(),
                        "user_id" => $user["id"],
                        "user_email" => $user["email"],
                        "profile_id" => $profile["id"],
                        "package_id" => $package["id"],
                        "package_name" => $package["name"],
                        "store_name"=> $store->store_name,
                        "registerfields" => $regFields,
                        "cover_url" => $store->cover_url
                    );
            }
        }
        return $arrayOfData;
    }

    /**
     * @param Store $store
     * @return array
     */
    public function inactiveStoreSingle(Store $store)
    {
        $arrayOfData = array();
        $user = $store->user;
        $profile = $store->profile;
        if (is_object($profile)) {
            $regFields = array();
            foreach ($user->registerfields as $registerfield) {
                $regFields[$registerfield->name] = $registerfield->pivot->valueEntered;
            }
            $package = $profile->package;

            if ($store->location != null)
                $location = $store->location->id;
            else
                $location = '';

            $arrayOfData =
                array(
                    "id" => $store->id,
                    "status" => $store->status,
                    "store_name" => $store->store_name,
                    "created_at" => $store->created_at->__toString(),
                    "updated_at" => $store->updated_at->__toString(),
                    "user_id" => $user["id"],
                    "user_email" => $user["email"],
                    "profile_id" => $profile["id"],
                    "package_id" => $package["id"],
                    "package_name" => $package["name"],
                    "user" => $store->user,
                    "location_id" => $location,
                    "registerfields" => $regFields,
                    "cover_url" => $store->cover_url
                );
        }
        return $arrayOfData;
    }

    /**
     * @param Store $store
     * @return Store
     */
    public function activateStore(Store $store, Request $request)
    {
        $store->status = $request->status;
        $store->store_name = $request->store_name;
        $store->location()->associate($request->location_id);

        if (isset($request->base64)) {
            $store = $this->setImage($store, $request->base64, $request->title);
        }

        $store->save();

        $user = $store->user;
        $user->email = $request->user_email;
        $user->save();

        return $store;
    }

    public function setImage(Store $store, $base64_encoded_image, $title)
    {
        if (! $base64_encoded_image) {
            $store->cover_url = '';
            return $store;
        }
        $fileName = 'slide_' . str_random(6) . '_'. str_slug($title) . '.jpg';
        $relativePath = 'full_size/' . $fileName;
        $imageBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64_encoded_image));
        Storage::disk('images')->put($relativePath, $imageBinary);
        $store->cover_url = '/images/' . $relativePath;

        return $store;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function viewAll()
    {
        $stores = Store::with('user', 'profile.package')->get();

        foreach ($stores as $store) {
            $store->user_email = $store->user->email;
            $store->package_name = $store->profile->package->name;
        }

        return $stores;
    }

    public function delete(Store $store)
    {
        if ($store->delete()) {
            return response()->json([], Response::HTTP_NO_CONTENT);
        }
        return response()->json([
            'error' => 'failed to delete store ' . $store->id,
        ]);
    }

    public function upgrade($id, Request $request, PackageService $packageService)
    {
        $store = Store::findOrFail($id);

        if (!$store->isLight()) {
            return response()->json([
                'error' => 'Store must be Light Store',
            ]);
        }

        $package = Package::whereName($request->get('package_name'))->firstOrFail();

        try {
            $upgradedStore = $packageService->upgrade($store, $package);

            return $upgradedStore;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function upgradePackage($entity)
    {
        return view('dashboard.pages.upgrade-package')->with('entity', $entity);
    }

    public function fullSetup(FullStoreSetupRequest $request, ImportService $importService)
    {
        $package = Package::findOrFail($request->get('package_id'));
        $location = Location::findOrFail($request->get('location_id'));

        $row = new ImportRow();
        $row->email = $request->get('email');
        $row->company = $request->get('company');
        $row->location_id = $location->id;
        $row->latitude = $location->latitude;
        $row->longitude = $location->longitude;

        $user = $importService->registerUser($row, $package, false, false);

        return response()->json($user->store)->setStatusCode(Response::HTTP_CREATED);
    }
}
