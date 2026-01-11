<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupabaseService
{
    protected Client $client;
    protected string $baseUrl;
    protected string $bucket;
    protected string $serviceRoleKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('SUPABASE_URL', ''), '/');
        // Use SUPABASE_S3_BUCKET for consistency with other config
        $this->bucket = env('SUPABASE_S3_BUCKET', 'img');
        $this->serviceRoleKey = env('SUPABASE_SERVICE_ROLE_KEY', '');

        // Validate required configuration
        if (empty($this->baseUrl) || empty($this->serviceRoleKey)) {
            throw new \Exception('Supabase configuration is missing. Please check SUPABASE_URL and SUPABASE_SERVICE_ROLE_KEY in .env file.');
        }

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'apikey' => $this->serviceRoleKey,
                'Authorization' => 'Bearer ' . $this->serviceRoleKey,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Upload file to Supabase Storage
     *
     * @param string $path Path in bucket (e.g., "banners/image.jpg")
     * @param string|resource $content File content
     * @param string $contentType MIME type
     * @return array|null Returns public URL and path
     * @throws \Exception
     */
    public function upload(string $path, $content, string $contentType): ?array
    {
        try {
            // Create a new client without default Content-Type for this request
            $uploadClient = new Client([
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'apikey' => $this->serviceRoleKey,
                    'Authorization' => 'Bearer ' . $this->serviceRoleKey,
                ],
                'timeout' => 30,
            ]);

            $response = $uploadClient->post("/storage/v1/object/{$this->bucket}/{$path}", [
                'headers' => [
                    'Content-Type' => $contentType,
                    'Content-Length' => strlen(is_resource($content) ? stream_get_contents($content) : $content),
                ],
                'body' => is_resource($content) ? stream_get_contents($content) : $content,
            ]);

            if ($response->getStatusCode() === 200) {
                $publicUrl = $this->getPublicUrl($path);

                return [
                    'path' => $path,
                    'public_url' => $publicUrl,
                    'full_path' => "{$this->bucket}/{$path}",
                ];
            }

            return null;
        } catch (RequestException $e) {
            \Log::error('Supabase upload error: ' . $e->getMessage());
            if ($e->hasResponse()) {
                \Log::error('Response: ' . $e->getResponse()->getBody()->getContents());
            }
            throw new \Exception('Failed to upload file to Supabase: ' . $e->getMessage());
        }
    }

    /**
     * Upload from Laravel uploaded file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder Folder path (e.g., "banners", "profiles", "shops")
     * @return array|null
     * @throws \Exception
     */
    public function uploadFile($file, string $folder): ?array
    {
        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload');
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        $path = "{$folder}/" . date('Y/m/d') . "/{$filename}";

        // Get file content
        $content = file_get_contents($file->getRealPath());
        $contentType = $file->getMimeType();

        return $this->upload($path, $content, $contentType);
    }

    /**
     * Delete file from Supabase Storage
     *
     * @param string $path Path in bucket
     * @return bool
     * @throws \Exception
     */
    public function delete(string $path): bool
    {
        try {
            $response = $this->client->delete("/storage/v1/object/{$this->bucket}/{$path}");

            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            \Log::error('Supabase delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL for a file
     *
     * @param string $path Path in bucket
     * @return string
     */
    public function getPublicUrl(string $path): string
    {
        return "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$path}";
    }

    /**
     * List files in a folder
     *
     * @param string $folder Folder path
     * @return array
     */
    public function listFiles(string $folder = ''): array
    {
        try {
            $response = $this->client->get("/storage/v1/object/{$this->bucket}/{$folder}", [
                'query' => [
                    'limit' => 100,
                    'offset' => 0,
                    'sortBy' => 'created_at',
                    'order' => 'desc',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            \Log::error('Supabase list error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update/replace existing file
     *
     * @param string $path Path in bucket
     * @param string|resource $content File content
     * @param string $contentType MIME type
     * @return array|null
     * @throws \Exception
     */
    public function update(string $path, $content, string $contentType): ?array
    {
        try {
            $response = $this->client->put("/storage/v1/object/{$this->bucket}/{$path}", [
                'headers' => [
                    'Content-Type' => $contentType,
                ],
                'body' => is_resource($content) ? stream_get_contents($content) : $content,
            ]);

            if ($response->getStatusCode() === 200) {
                $publicUrl = $this->getPublicUrl($path);

                return [
                    'path' => $path,
                    'public_url' => $publicUrl,
                    'full_path' => "{$this->bucket}/{$path}",
                ];
            }

            return null;
        } catch (RequestException $e) {
            \Log::error('Supabase update error: ' . $e->getMessage());
            throw new \Exception('Failed to update file in Supabase: ' . $e->getMessage());
        }
    }
}
