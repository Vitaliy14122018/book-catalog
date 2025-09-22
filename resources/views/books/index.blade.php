@extends('layouts.app')

@section('content')
    <h1 style="font-size: 20px; font-weight:bold; margin-bottom: 15px;">Книги</h1>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#bookCreateModal">Добавить книгу</button>
	
    <!-- Форма для поиска и сортировки -->
    <div class="mb-3">
        <form method="GET" action="{{ route('books.index') }}">
            <div class="d-flex">
			    <!-- Селект для сортировки -->
                <select name="sort" id="sort" class="form-select mx-2" onchange="this.form.submit()">
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>По названию (по возрастанию)</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>По названию (по убыванию)</option>
                </select>
				
                <!-- Поле для поиска по названию -->
                <input type="text" name="search_title" value="{{ request('search_title') }}" class="form-control" placeholder="Поиск по названию книги" />

                <!-- Поле для поиска по автору -->
                <input type="text" name="search_author" value="{{ request('search_author') }}" class="form-control mx-2" placeholder="Поиск по автору" />

                <!-- Кнопка для поиска -->
                <button type="submit" class="btn btn-primary">Поиск</button>
            </div>
        </form>
    </div>

    <!-- Таблица книг -->
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Название</th>
				<th>Авторы</th>
				<th>Дата публикации</th>
				<th>Изображение</th>
				<th>Действия</th>
			</tr>
		</thead>
		<tbody id="booksTableBody">
   @include('books.partials.table')
		</tbody>
	</table>

    <!-- Здесь пагинация -->
    @include('books.partials.pagination')

    <!-- Модальное окно для создания книги -->
    @include('books.partials.create_modal')

    <!-- Модальное окно для редактирования книги -->
    @include('books.partials.edit_modal')

<script>
$(document).ready(function () {
    // Настройка глобального CSRF токена для всех AJAX-запросов
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Очистка ошибок перед отправкой формы
    function clearErrors() {
        $('.invalid-feedback').remove();
        $('.form-control').removeClass('is-invalid');
    }

    // Добавление книги
    $('#bookCreateForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();  // Очистка предыдущих ошибок

        var formData = new FormData(this);
		
        // Получаем параметры фильтров и сортировки
		var searchTitle = $('input[name="search_title"]').val();
		var searchAuthor = $('input[name="search_author"]').val();
		var sort = $('#sort').val();
		
		// Собираем параметры запроса (кроме 'page')
		var queryParams = $.param({
			search_title: searchTitle,
			search_author: searchAuthor,
			sort: sort
		});
		
		if(queryParams) {
			queryParams = '?' + queryParams;
		} else {
			queryParams = '';
		}
		
        $.ajax({
            url: '{{ route('books.store') }}' + queryParams,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
					alert(response.message);
                    $('#booksTableBody').html(response.booksHtml);
                    $('#pagination').html(response.paginationHtml);
                    $('#bookCreateModal').modal('hide');
                    $('#bookCreateForm')[0].reset();
                } else {
                    alert('Ошибка при добавлении книги');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {  // Ошибка валидации
                    var errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        var inputField = $('[name="' + field + '"]');
                        inputField.addClass('is-invalid');  // Добавляем класс для отображения ошибки
                        inputField.after('<div class="invalid-feedback">' + messages + '</div>');
                    });
                } else {
                    alert('Произошла ошибка при отправке данных');
                }
            }
        });
    });

    // Редактирование книги
    $(document).on('click', '.editBookBtn', function () {
        var bookId = $(this).data('id');

        // Получаем данные книги через AJAX
        $.get('/books/' + bookId + '/edit', function (response) {
            if (response.status === 'success') {
                // Заполняем форму данными книги
                $('#editBookId').val(response.book.id);
                $('#editTitle').val(response.book.title);
                $('#editDescription').val(response.book.description);
                $('#editPublishedAt').val(response.book.published_at);
                // Авторы
                $('#editAuthors').val(response.book.authors.map(a => a.id));  // Устанавливаем выбранных авторов
                $('#editImagePreview').attr('src', response.book.image ? '/storage/' + response.book.image : '');  // Превью изображения

                // Открываем модальное окно редактирования
                $('#bookEditModal').modal('show');
            }
        });
    });

    // Отправка формы редактирования через AJAX
    $('#bookEditForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();  // Очистка предыдущих ошибок

            var formData = new FormData(this);

			// Авторы (нужно вручную добавить массив)
			formData.delete('authors[]'); // убираем дубликаты
			$('#editAuthors option:selected').each(function () {
				formData.append('authors[]', $(this).val());
			});

			// Картинка (добавляем только если есть)
			if ($('#editImage')[0].files.length > 0) {
				formData.set('image', $('#editImage')[0].files[0]);
			} else {
				formData.delete('image'); // вообще убираем, если не выбрано
			}
			
			
			// Получаем параметры фильтров и сортировки
			var searchTitle = $('input[name="search_title"]').val();
			var searchAuthor = $('input[name="search_author"]').val();
			var sort = $('#sort').val();
			
		    // Собираем параметры запроса (кроме 'page')
			var queryParams = $.param({
				search_title: searchTitle,
				search_author: searchAuthor,
				sort: sort
			});
			
			if(queryParams) {
				queryParams = '?' + queryParams;
			} else {
				queryParams = '';
			}
			
			var bookId = $('#editBookId').val();

        $.ajax({
            url: '/books/' + bookId + queryParams,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
			headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-HTTP-Method-Override': 'PUT' // так Laravel примет как PUT
                    },
            success: function (response) {
                if (response.status === 'success') {
                    alert(response.message);
					
					// Обновляем таблицу
                    $('#booksTableBody').html(response.booksHtml);
					$('#pagination').html(response.paginationHtml);

                    $('#bookEditModal').modal('hide');
                    
                } else {
                    alert('Ошибка при обновлении книги');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {  // Ошибка валидации
                    var errors = xhr.responseJSON.errors;

                    $.each(errors, function (field, messages) {
                        var inputField = $('[name="' + field + '"]');
                        inputField.addClass('is-invalid');
                        inputField.after('<div class="invalid-feedback">' + messages + '</div>');
                    });
                } else {
                    alert('Произошла ошибка при отправке данных');
                }
            }
        });
    });

    // Удаление книги
    $(document).on('click', '.deleteBookBtn', function () {
        var bookId = $(this).data('id');
        var row = $(this).closest('tr');
		
		// Получаем текущие значения фильтров и сортировки из формы
		var searchTitle = $('input[name="search_title"]').val();
		var searchAuthor = $('input[name="search_author"]').val();
		var sort = $('#sort').val();

		// Собираем параметры запроса (кроме 'page')
		var queryParams = $.param({
			search_title: searchTitle,
			search_author: searchAuthor,
			sort: sort
		});
		
		if(queryParams) {
			queryParams = '?' + queryParams;
		} else {
			queryParams = '';
		}

        if (confirm('Вы уверены, что хотите удалить эту книгу?')) {
            $.ajax({
                url: '/books/' + bookId + queryParams,
                method: 'DELETE',
                success: function (response) {
                    if (response.status === 'success') {
                        alert(response.message);
						
					// Обновляем таблицу
                    $('#booksTableBody').html(response.booksHtml);
					$('#pagination').html(response.paginationHtml);

				
                    } else {
                        alert('Ошибка при удалении книги');
                    }
                },
                error: function () {
                    alert('Произошла ошибка при удалении книги');
                }
            });
        }
    });
});
</script>
@endsection