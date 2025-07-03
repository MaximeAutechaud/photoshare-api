<?php

namespace App\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use PHPExif\Reader\Reader;

class ExtractMetadatasService
{
    public function extract(UploadedFile $file): array
    {
        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $data = $reader->read($file->getRealPath());
        $date = $this->extractDate($file);
        //$height = $data->getHeight();
        //$width = $data->getWidth();

        return [
            'taken_at' => $date,
            //'height' => $height,
            //'width' => $width,
        ];
    }

    private function extractDate(UploadedFile $file): ?Carbon
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return $this->extractImageDate($file);
        }

        if (str_starts_with($mime, 'video/')) {
            return $this->extractVideoDate($file);
        }

        return null;
    }

    private function extractImageDate(UploadedFile $file): ?Carbon
    {
        try {
            $reader = Reader::factory(Reader::TYPE_NATIVE);
            $data = $reader->read($file->getRealPath());

            if ($data && $data->getCreationDate()) {
                return Carbon::instance($data->getCreationDate());
            }
        } catch (\Throwable $e) {
            // Log ou ignore selon besoin
        }

        return null;
    }

    private function extractVideoDate(UploadedFile $file): ?Carbon
    {
        $path = $file->getRealPath();
        $cmd = "ffprobe -v quiet -print_format json -show_format " . escapeshellarg($path);

        try {
            $output = shell_exec($cmd);
            $data = json_decode($output, true);

            $creation = $data['format']['tags']['creation_time'] ?? null;

            if ($creation) {
                return Carbon::parse($creation)->setTimezone(config('app.timezone'));
            }
        } catch (\Throwable $e) {
            // Log ou ignore selon besoin
        }

        return null;
    }
}
