<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Book;

class Author extends Model
{
    use HasFactory;
	
	    // Разрешаем массовое присваивание для этих полей
    protected $fillable = [
        'name',      // Имя автора
        'surname',   // Фамилия автора
        'patronymic', // Дата рождения (если используется)
        // Добавьте другие поля, которые хотите разрешить для массового присваивания
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}

