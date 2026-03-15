<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\UploadMediaToDriveJob;
use App\Models\Vistoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VistoriaFotoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'vistoria_id' => 'required|exists:vistorias,id',
            'foto' => 'required|image|max:10240',
            'descricao' => 'nullable|string|max:255',
        ]);

        $vistoria = Vistoria::findOrFail($request->vistoria_id);

        $media = $vistoria->addMedia($request->file('foto'))
            ->usingName($request->file('foto')->getClientOriginalName())
            ->toMediaCollection('fotos');

        if (config('services.google_drive.client_id')) {
            UploadMediaToDriveJob::dispatch($media->id);
        }

        $thumb = $media->hasGeneratedConversion('thumb')
            ? $media->getUrl('thumb')
            : $media->getUrl();

        return response()->json([
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb' => $thumb,
        ], 201);
    }

    public function status(Vistoria $vistoria): JsonResponse
    {
        $fotos = $vistoria->getMedia('fotos')->map(fn ($media) => [
            'id' => $media->id,
            'url' => $media->getUrl(),
            'thumb' => $media->getUrl('thumb'),
            'cloud_status' => $media->cloud_status ?? 'pending',
            'name' => $media->name,
        ]);

        return response()->json(['fotos' => $fotos]);
    }
}
