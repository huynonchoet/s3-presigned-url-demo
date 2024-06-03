<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;


class FileUploadSystem
{
    protected $disk;
    protected $s3Client;
    public function s3()
    {
        $this->disk = Storage::disk('s3');
        return $this;
    }

    /**
     * Get presigned url
     * @param string $filename
     * @return string
     */
    public function getPresignedUrl($fileName) : string
    {
        return $this->disk->temporaryUrl(
            $fileName, now()->addMinutes(5)
        );
    }

    /**
     * Put object presigned url
     * @param string $filename
     * @return array
     */
    public function postPresignedUrl($fileName) : Array
    {
        ['url' => $url, 'headers' => $headers] = $this->disk->temporaryUploadUrl(
            $fileName, now()->addMinutes(5)
        );

        return ['url' => $url, 'headers' => $headers];
    }

    /**
     * S3 client
     * @return FileUploadSystem
     */
    public function s3Client() : FileUploadSystem
    {
        $this->s3Client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => '2006-03-01'
        ]);

        return $this;
    }

    /**
     * Get upload id
     * @param string $key
     * @return string
     */
    public function getMultiPartUploadId($key) : string
    {
        $response = $this->s3Client->createMultipartUpload([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key'    => $key
        ]);
        return $response['UploadId'];
    }

    /**
     * Get presigned url for each part
     * @param string $key
     * @param int $partNumber
     * @param string $uploadId
     *
     * @return string
     */
    public function getPresignedUploadPartUrl($key, $partNumber, $uploadId) : string
    {
        $command = $this->s3Client->getCommand('UploadPart', [
            'Bucket'        => config('filesystems.disks.s3.bucket'),
            'Key'           => $key,
            'PartNumber'    => $partNumber,
            'UploadId'      => $uploadId,
            'Body'          => '',
        ]);
        $presignedUrl = $this->s3Client->createPresignedRequest($command, '+20 minutes');

        return (string)$presignedUrl->getUri();
    }

    /**
     * List all parts by object key and upload id
     * @param string $key
     * @param string $uploadId
     */
    public function listParts($key, $uploadId) : array
    {
        $res = $this->s3Client->listParts([
            'Bucket'   => config('filesystems.disks.s3.bucket'),
            'Key'      => $key,
            'UploadId' => $uploadId,
        ]);
        $parts = [];
        foreach($res['Parts'] as $part) {
            $partNumber = $part['PartNumber'];

            $parts['Parts'][$partNumber] = [
                'PartNumber' => $partNumber,
                'ETag' => $part['ETag'],
            ];
        }

        return $parts;
    }

    /**
     * Complete multipart upload
     * @param string $key
     * @param string $uploadId
     * @param array $parts
     *
     * @return /App\Helpers\Aws\Result
     */
    public function completeMultipartUpload($key, $uploadId, $parts)
    {
        return  $this->s3Client->completeMultipartUpload([
            'Bucket'          => config('filesystems.disks.s3.bucket'),
            'Key'             => $key,
            'UploadId'        => $uploadId,
            'MultipartUpload' => $parts,
        ]);
    }
}
