@if($books->isEmpty())
    <tr>
        <td colspan="5" class="text-center">Для этой страницы нет данных.</td>
    </tr>
@else
	@foreach($books as $book)
		<tr data-id="{{ $book->id }}">
			<a href="{{ route('books.show', $book->id) }}" class="text-decoration-none">{{ $book->title }}</a>
			<td>{{ $book->authors->isNotEmpty() ? $book->authors->pluck('surname')->join(', ') : 'Нет авторов' }}</td>
			<td>{{ $book->published_at ? $book->published_at->format('d.m.Y') : 'Дата не указана' }}</td>
			<td>
				@if($book->image)
					<a href="{{ asset('storage/' . $book->image) }}" target="_blank">
						<img src="{{ asset('storage/' . $book->image) }}" alt="{{ $book->title }}" width="60" height="80" style="object-fit:cover;">
					</a>
				@else
					<span class="text-muted">Нет изображения</span>
				@endif
			</td>
			<td>
				<button class="btn btn-sm btn-warning editBookBtn" data-id="{{ $book->id }}">Редактировать</button>
				<button class="btn btn-sm btn-danger deleteBookBtn" data-id="{{ $book->id }}">Удалить</button>
			</td>
		</tr>
	@endforeach
@endif