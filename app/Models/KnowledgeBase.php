<?php

namespace App\Models;

use Database\Factories\KnowledgeBaseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    /** @use HasFactory<KnowledgeBaseFactory> */
    use HasFactory;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'category',
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
