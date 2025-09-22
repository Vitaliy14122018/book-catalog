<!-- resources/views/books/show.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $book->title }}</h1>
        
        <!-- Отображаем изображение книги, если оно есть -->
        @if ($book->image)
            <div>
                <img src="{{ asset('storage/' . $book->image) }}" alt="Изображение книги" class="img-fluid">
            </div>
        @else
            <p><strong>Изображение книги отсутствует.</strong></p>
        @endif

        <p><strong>Описание:</strong> {{ $book->description ?? 'Нет описания' }}</p>
        <p><strong>Дата публикации:</strong> {{ $book->published_at ? \Carbon\Carbon::parse($book->published_at)->format('d-m-Y') : 'Не указано' }}</p>

        <h3>Авторы:</h3>
        <ul>
            @foreach ($authors as $author)
                <li>{{ $author->surname }} {{ $author->name }}{{ $author->patronymic ? ' ' . $author->patronymic : '' }}</li>
            @endforeach
        </ul>

        <a href="{{ route('books.index') }}" class="btn btn-primary">Назад к списку книг</a>
    </div>
@endsection