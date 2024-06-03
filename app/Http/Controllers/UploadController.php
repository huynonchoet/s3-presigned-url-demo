<?php

namespace App\Http\Controllers;

use Storages;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    protected $storage;

    public function __construct()
    {
        $this->storage = Storages::s3();
    }

    /**
     * Show upload page
     * @return \Illuminate\View\View
     */
    public function index() {
        return view('index');
    }

    /**
     * Create new presigned url to put object
     * @param Request $request
     * @return string
     */
    public function createPresignedUrl(Request $request) {
        $fileName = $request->file_name;

        return $this->storage->postPresignedUrl($fileName);
    }

    /**
     * Create new presigned url to get object
     * @param Request $request
     * @return string
     */
    public function getPresignedUrl(Request $request) {
        $fileName = $request->file_name;
        return $this->storage->getPresignedUrl($fileName);
    }



    /**
     * Get upload id for multipart upload
     * @param Request $request
     * @return string
     */
    public function getUploadId(Request $request) {
        $fileName = data_get($request, 'file_name');

        return $this->storage->s3Client()->getMultiPartUploadId($fileName);
    }

    /**
     * Get presigned url for each part
     * @param Request $request
     * @return string
     */
    public function getPresignedUploadPartUrl(Request $request) {
        $key = data_get($request, 'file_name');
        $uploadId = data_get($request, 'upload_id');
        $partNumber = data_get($request, 'part_number');

        return $this->storage->s3Client()->getPresignedUploadPartUrl($key, $partNumber, $uploadId);
    }

    /**
     * Complete upload multipart
     * @param Request $request
     * @return void
     */
    public function completeUploadPart(Request $request) {
        $key = data_get($request, 'file_name');
        $uploadId = data_get($request, 'upload_id');
        $parts = $this->storage->s3Client()->listParts($key, $uploadId);

        return $this->storage->s3Client()->completeMultipartUpload($key, $uploadId, $parts);
    }
}
