<?php

namespace App\Http\Controllers;

use App\BrandReference;
use App\Category;
use App\Http\Requests\BrandReferenceStoreRequest;
use App\Manufacturer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class BrandReferenceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        return response()->json($manufacturer->brandReferences);
    }


    /**
     * Store a newly created resource in storage.
     * @throws
     * @param  int $id
     * @param BrandReferenceStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store($id, BrandReferenceStoreRequest $request)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $categoryId = $request->get('category_id');
        $category = Category::find($categoryId);

        $reference = new BrandReference();
        $reference->title = $request->get('title');
        $reference->description = $request->get('description');
        $reference->manufacturer_id = $manufacturer->id;
        $reference->category_id = null;
        if ($category instanceof Category) {
            $reference->category_id = $category->id;
        }
        $reference->save();

        $image = $request->file('image');
        $binary = file_get_contents($image->getRealPath());
        $reference->saveOriginal($binary);
        $reference->generateThumbnails();
        $reference->save();

        $manufacturer->brandreferences_count = count($manufacturer->brandreferences);
        $manufacturer->save();

        return response()->json($reference, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  int  $referenceId
     * @return \Illuminate\Http\Response
     */
    public function show($id, $referenceId)
    {
        $manufacturer = Manufacturer::find($id);

        if (!$manufacturer instanceof  Manufacturer) {
            throw new ModelNotFoundException();
        }

        $reference = $manufacturer->brandReferences->find($referenceId);

        if (!$reference instanceof BrandReference) {
            throw new ModelNotFoundException();
        }

        return response()->json($reference);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $referenceId
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request, $referenceId)
    {
        // TODO: handle replacing old image
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  int  $referenceId
     * @param  \Illuminate\Http\Request  $request
     * @throws
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $referenceId, Request $request)
    {
        $manufacturer = Manufacturer::find($id);

        if (!$manufacturer instanceof Manufacturer) {
            throw new ModelNotFoundException();
        }

        $brandreference = $manufacturer->brandReferences->find($referenceId);

        if (!$brandreference instanceof BrandReference) {
            throw new ModelNotFoundException();
        }

        $brandreferenceDirectory = '/full_size/brandreferences/'. $brandreference->id;

        if ($brandreference->delete()) {
            Storage::disk('images')->deleteDirectory($brandreferenceDirectory);
            return response()->json([], 204);
        } else {
            return response()->json([
                'error' => 'failed to delete BrandReference'
            ]);
        }
    }
}
