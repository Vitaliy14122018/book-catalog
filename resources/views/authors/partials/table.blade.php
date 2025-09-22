@if($authors->isEmpty())
    <tr>
        <td colspan="5" class="text-center">Для этой страницы нет данных.</td>
    </tr>
@else
    @foreach($authors as $author)
        <tr data-id="{{ $author->id }}">
            <td><a href="{{ route('authors.show', $author->id) }}" class="text-decoration-none">{{ $author->surname }}</a></td>
            <td>{{ $author->name }}</td>
            <td>{{ $author->patronymic }}</td>
            <td>
                <button class="btn btn-sm btn-warning editAuthorBtn" data-id="{{ $author->id }}" data-surname="{{ $author->surname }}" data-name="{{ $author->name }}" data-patronymic="{{ $author->patronymic }}">Редактировать</button>
                <button class="btn btn-sm btn-danger deleteAuthorBtn" data-id="{{ $author->id }}">Удалить</button>
            </td>
        </tr>
    @endforeach
@endif
