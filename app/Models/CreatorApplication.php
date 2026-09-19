<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreatorApplication extends Model
{
    public const STATUSES = [
        'new' => 'جديد',
        'reviewing' => 'قيد المراجعة',
        'approved' => 'مقبول',
        'rejected' => 'مرفوض',
    ];

    protected $fillable = [
        'name',
        'email',
        'country',
        'dial_code',
        'phone',
        'niche',
        'followers',
        'social_links',
        'bio',
        'status',
        'admin_notes',
        'ip_hash',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    public function getFullPhoneAttribute(): string
    {
        return trim(($this->dial_code ?: '').' '.$this->phone);
    }

    /** @return list<string> */
    public function getSocialLinkListAttribute(): array
    {
        return array_values(array_filter((array) ($this->social_links ?? [])));
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
