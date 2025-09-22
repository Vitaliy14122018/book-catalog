@extends('layouts.app')

@section('content')
    <div class="container">

        <!-- Ссылки на страницы книг и авторов -->
        <a href="{{ route('books.index') }}" class="btn btn-primary">Перейти к книгам</a>
        <a href="{{ route('authors.index') }}" class="btn btn-secondary">Перейти к авторам</a>
    </div>
@endsection
