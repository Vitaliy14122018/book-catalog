@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Автор: {{ $author->surname }} {{ $author->name }} {{ $author->patronymic ?? '' }}</h1>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Информация о авторе:</h3>
                <ul class="list-unstyled">
                    <li><strong>Фамилия:</strong> {{ $author->surname }}</li>
                    <li><strong>Имя:</strong> {{ $author->name }}</li>
                    <li><strong>Отчество:</strong> {{ $author->patronymic ?? 'Не указано' }}</li>
                </ul>
            </div>
		</div>
	</div>
@endsection