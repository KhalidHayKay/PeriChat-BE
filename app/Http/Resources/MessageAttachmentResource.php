<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageAttachmentResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'messageId' => $this->message_id,
            'name'      => $this->name,
            'mime'      => $this->normalizedMime(),
            'size'      => $this->size,
            'url'       => Storage::url($this->path),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    public function normalizedMime(): string
    {
        $mime = $this->mime;

        $map = [
            // Word
            'application/msword'                                                        => 'application/word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'application/word',

            // Excel
            'application/vnd.ms-excel'                                                  => 'application/excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'application/excel',

            // PowerPoint
            'application/vnd.ms-powerpoint'                                             => 'application/powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'application/powerpoint',

            // PDF
            'application/pdf'                                                           => 'application/pdf',

            // Text
            'text/plain'                                                                => 'text/plain',
            'text/csv'                                                                  => 'text/csv',

            // Images (remain unchanged but return 'image' label)
            'image/jpeg'                                                                => 'image/jpeg',
            'image/png'                                                                 => 'image/png',
            'image/gif'                                                                 => 'image/gif',
            'image/webp'                                                                => 'image/webp',
            'image/svg+xml'                                                             => 'image/svg+xml',

            // Video (remain unchanged but return 'video' label)
            'video/mp4'                                                                 => 'video/mp4',
            'video/quicktime'                                                           => 'video/quicktime',
            'video/x-msvideo'                                                           => 'video/x-msvideo',

            // Audio (remain unchanged but return 'audio' label)
            'audio/mpeg'                                                                => 'audio/mpeg',
            'audio/wav'                                                                 => 'audio/wav',
            'audio/ogg'                                                                 => 'audio/ogg',

            // Archives
            'application/zip'                                                           => 'application/zip',
            'application/x-rar-compressed'                                              => 'application/zip',
            'application/x-7z-compressed'                                               => 'application/zip',
            'application/x-tar'                                                         => 'application/zip',
        ];

        return $map[$mime] ?? $mime; // fallback to original if not mapped
    }

}
