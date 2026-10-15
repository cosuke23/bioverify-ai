<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfDocument extends Model
{
    protected $fillable = [
        'title',
        'original_filename',
        'file_path',
        'file_hash',
        'status',
        'page_count',
        'summary',
        'processing_error',
        'processed_at',
    ];

    /**
     * Minutes after which a pending/processing document is treated as
     * stalled (e.g. the queue worker was stopped mid-job). Kept above
     * the IndexPdfDocument job timeout.
     */
    public const STALE_AFTER_MINUTES = 20;

    public function isStale(): bool
    {
        return in_array($this->status, ['pending', 'processing'], true)
            && $this->updated_at?->lt(now()->subMinutes(self::STALE_AFTER_MINUTES));
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed' || $this->isStale();
    }

    protected function casts(): array
    {
        return [
            'page_count' => 'integer',
            'processed_at' => 'datetime',
        ];
    }
}
