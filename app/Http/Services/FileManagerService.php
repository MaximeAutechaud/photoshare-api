<?php

namespace App\Http\Services;

use App\MediaTypeEnum;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileManagerService
{
    public function store(Request $request): JsonResponse
    {
        $service = new ExtractMetadatasService();
        $receiver = new FileReceiver($request->file, $request, ResumableJSUploadHandler::class);
        if (!$receiver->isUploaded()) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        $save = $receiver->receive();
        if ($save->isFinished()) {
            $file = $save->getFile(); // Fichier final assemblé
            $metadatas = $service->extract($file); // Récupération des métadonnées (taken_at, height, width)
            // Nom et chemin de destination sur S3
            $fileName = $file->getClientOriginalName();
            $path = Auth::id() . '/' . $fileName;

            // Stockage sur S3 (public ou privé selon config)
            $stream = fopen($file->getPathname(), 'r+');
            Storage::disk('library')->put($path, $stream, 'public');
            fclose($stream);

            // Renvoyer l’URL
            $url = Storage::disk('library')->url($path);

            Media::create([
                'name' => $fileName,
                'user_id' => Auth::id(),
                'mime_type' => MediaTypeEnum::tryFrom($file->getMimeType())?->value,
                's3_url' => $url,
                'size' => $file->getSize(),
                'taken_at' => $metadatas['taken_at'] ?? null,
                'height' => $metadatas['height'] ?? null,
                'width' => $metadatas['width'] ?? null,
            ]);

            // Supprimer fichier temporaire
            unlink($file->getPathname());

            return response()->json(['success' => true, 'file' => $file]);
        }
        $handler = $save->handler();
        return response()->json([
            'progress' => $handler->getPercentageDone(),
        ]);
    }

    public function deleteFile(string $id):JsonResponse
    {
        $media = Media::query()->findOrFail($id);
        if ($media && $media->user_id === Auth::id()) {
            DB::transaction(function () use ($media) {
                $service = new S3Service();
                $service->deleteObject($media->s3_url);
                DB::table('medias')->where('id', $media->id)->delete();
            });
            $res = ['success' => true];
        }
        return response()->json($res ?? ['success' => false]);

    }
}
