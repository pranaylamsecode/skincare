<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'subject',
        'content',
        'variables_hint',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function render(array $data): array
    {
        $subject = $this->subject;
        $content = $this->content;

        foreach ($data as $key => $value) {
            $val = is_scalar($value) ? (string) $value : '';
            $subject = str_replace('{' . $key . '}', $val, $subject);
            $content = str_replace('{' . $key . '}', $val, $content);
        }

        return [
            'subject' => $subject,
            'content' => $content,
        ];
    }
}
