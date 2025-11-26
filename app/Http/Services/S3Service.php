<?php

namespace App\Http\Services;

use Aws\Exception\AwsException;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Sdk;
use Illuminate\Support\Collection;

class S3Service
{
    public Sdk $sdk;
    public S3Client $client;
    public string $bucket;

    public function __construct()
    {
        // Create an SDK class used to share configuration across clients.
        $this->sdk = new Sdk(['region' => env('AWS_DEFAULT_REGION')]);
        $this->client = $this->initS3();
        $this->bucket = env('AWS_BUCKET');
    }


    private function initS3(): S3Client
    {
        // Use an Aws\Sdk class to create the S3Client object.
        return $this->sdk->createS3();
    }

    public function getObject(string $fullPath)
    {
        return $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $fullPath, //path from bucket root directory
        ]);
    }

    public function listObjects(): Result
    {
        return $this->client->listObjects(['Bucket' => $this->bucket]);
    }

    public function uploadObject(array $args): string
    {
        try {
            $result = $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $args['key'],
                'Body'   => $args['body'],
                'ACL'    => 'public-read',
            ]);

            return "Objet créé avec succès : " . $result['ObjectURL'] . PHP_EOL;
        } catch (Aws\S3\Exception\S3Exception $e) {
            return "Erreur : " . $e->getMessage() . PHP_EOL;
        }
    }

    public function deleteObject(string $fullPath, array $args = []): void
    {
        $parameters = array_merge(['Bucket' => $this->bucket, 'Key' => $fullPath], $args);
        try {
            $this->client->deleteObject($parameters);
            echo "Deleted the object named: $fullPath from $this->bucket.\n";
        } catch (AwsException $exception) {
            echo "Failed to delete $fullPath from $this->bucket with error: {$exception->getMessage()}\n";
            echo "Please fix error with object deletion before continuing.";
            throw $exception;
        }
    }

    public function getUrlCollection(Collection $urls): array
    {
        $result = [];
        foreach ($urls as $url) {
            $result[] = $url->s3_url;
        }
        return $result;
    }
}
