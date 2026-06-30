<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return response()->json(['data' => $addresses]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $address = $request->user()->addresses()->create($data);

        if ($address->is_default) {
            $this->makeDefault($request, $address);
        }

        return response()->json(['data' => $address], 201);
    }

    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $data = $this->validateData($request);
        $address->update($data);

        if ($address->is_default) {
            $this->makeDefault($request, $address);
        }

        return response()->json(['data' => $address]);
    }

    public function destroy(Request $request, $id)
    {
        $request->user()->addresses()->findOrFail($id)->delete();

        return response()->json(['message' => 'Adresse supprimée.']);
    }

    public function setDefault(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $this->makeDefault($request, $address);

        return response()->json(['data' => $address->fresh()]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nom' => 'required|string|max:255',
            'telephone' => 'required|string|max:40',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:120',
            'region' => 'nullable|string|max:120',
            'pays' => 'required|string|max:120',
            'code_postal' => 'nullable|string|max:20',
            'is_default' => 'boolean',
        ]);
    }

    private function makeDefault(Request $request, Address $address): void
    {
        $request->user()->addresses()
            ->where('id', '!=', $address->id)
            ->update(['is_default' => false]);
        $address->update(['is_default' => true]);
    }
}
