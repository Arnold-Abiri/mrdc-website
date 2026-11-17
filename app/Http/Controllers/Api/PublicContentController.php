<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicContentResource;
use App\Support\PublicApiCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicContentController extends Controller
{
    public function index(Request $request, string $resource): AnonymousResourceCollection
    {
        $this->validateQuery($request);
        $query = PublicApiCatalog::query($resource);
        $order = $resource === 'departments' ? 'public_display_order' : ($resource === 'bank-accounts' || $resource === 'faqs' ? 'display_order' : 'id');

        return PublicContentResource::collection($query->orderBy($order)->orderBy('id')->paginate((int) $request->query('per_page', 15))->withQueryString());
    }

    public function show(Request $request, string $resource, string $identifier): PublicContentResource
    {
        $this->validateQuery($request);
        $key = PublicApiCatalog::all()[$resource]['key'];
        abort_if($key === 'id' && ! ctype_digit($identifier), 404);

        return new PublicContentResource(PublicApiCatalog::query($resource)->where($key, $identifier)->firstOrFail());
    }

    private function validateQuery(Request $request): void
    {
        $request->validate([
            'locale' => ['sometimes', 'required', 'string', 'in:en,sn,nd'],
            'page' => ['sometimes', 'required', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'required', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
