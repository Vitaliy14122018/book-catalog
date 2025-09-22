<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Author;

class Book extends Model
{
    use HasFactory;

    // Указываем поля, которые могут быть массово присвоены
    protected $fillable = [
        'title',         // Название книги
        'description',   // Описание книги
        'published_at',  // Дата публикации
        'image',         // Изображение
    ];

    // Связь "многие ко многим" с авторами
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    // Преобразование даты для удобства работы
    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->format('d.m.Y') : 'Не указано';
    }
}
