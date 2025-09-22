@extends('layouts.app')

@section('content')
    <h1 style="font-size: 20px; font-weight:bold; margin-bottom: 15px;">Авторы</h1>
	
	<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#authorCreateModal">Добавить автора</button>
	
	    <!-- Форма фильтрации и сортировки -->
    <form action="{{ route('authors.index') }}" method="GET" class="mb-3">
        <div class="row">
		    <div class="col-md-3">
                <select id="sort" name="sort" class="form-control" onchange="this.form.submit()">
                    <option value="asc" {{ request()->get('sort') == 'asc' ? 'selected' : '' }}>По возрастанию</option>
                    <option value="desc" {{ request()->get('sort') == 'desc' ? 'selected' : '' }}>По убыванию</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="search_surname" placeholder="Фамилия" value="{{ request()->get('search_surname') }}">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="search_name" placeholder="Имя" value="{{ request()->get('search_name') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Применить фильтры</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Действия</th>
            </tr>
        </thead>
		<tbody id="authorsTableBody">
        @include('authors.partials.table', ['authors' => $authors])
        </tbody>
    </table>

    @include('authors.partials.pagination')

    @include('authors.partials.create_modal')
	@include('authors.partials.edit_modal')
	@include('authors.partials.delete_modal')

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

    // Добавление автора
    $('#authorCreateForm').on('submit', function (e) {
        e.preventDefault();
		e.stopPropagation();
		console.log("Форма отправляется через AJAX");
        clearErrors();
        let formData = $(this).serialize(); // Собираем данные формы
		
		// Получаем параметры фильтров и сортировки
		var search_surname = $('input[name="search_surname"]').val();
		var search_name = $('input[name="search_name"]').val();
		var sort = $('#sort').val();
		
		// Собираем параметры запроса (кроме 'page')
		var queryParams = $.param({
			search_surname: search_surname,
			search_name: search_name,
			sort: sort
		});
		
		if(queryParams) {
			queryParams = '?' + queryParams;
		} else {
			queryParams = '';
		}

        $.ajax({
            url: '{{ route('authors.store') }}' + queryParams,
            method: 'POST',
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
                    $('#authorsTableBody').html(response.authorsHtml);
                    $('#pagination').html(response.paginationHtml);
                    $('#authorCreateModal').modal('hide');
                    $('#authorCreateForm')[0].reset();
                    alert(response.message);
                } else {
                    alert('Ошибка при добавлении автора');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {  // Ошибка валидации
                    var errors = xhr.responseJSON.errors;

                    // Пройдем по всем ошибкам и отобразим их
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

	// Редактирование автора
	$(document).on('click', '.editAuthorBtn', function () {
		var authorId = $(this).data('id');

		// Получаем данные автора через AJAX
		$.get('/authors/' + authorId + '/edit', function (response) {
			if (response.status === 'success') {
				// Заполняем форму данными автора
				$('#editAuthorId').val(response.author.id);  // ID автора
				$('#editSurname').val(response.author.surname);  // Фамилия
				$('#editName').val(response.author.name);  // Имя
				$('#editPatronymic').val(response.author.patronymic);  // Отчество

				// Открываем модальное окно редактирования
				$('#authorEditModal').modal('show');
			} else {
				alert('Ошибка при получении данных для редактирования');
			}
		});
	});


    // Отправка формы редактирования через AJAX
    $('#authorEditForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        let formData = $(this).serialize(); // Собираем данные формы
		
		// Получаем параметры фильтров и сортировки
		var search_surname = $('input[name="search_surname"]').val();
		var search_name = $('input[name="search_name"]').val();
		var sort = $('#sort').val();
		
		// Собираем параметры запроса (кроме 'page')
		var queryParams = $.param({
			search_surname: search_surname,
			search_name: search_name,
			sort: sort
		});
		
		if(queryParams) {
			queryParams = '?' + queryParams;
		} else {
			queryParams = '';
		}

        $.ajax({
            url: '/authors/' + $('#editAuthorId').val() + queryParams, // Используем ID автора в URL
            method: 'PUT',  // PUT для обновления данных
            data: formData,
            success: function (response) {
                if (response.status === 'success') {
					
                    // Обновляем информацию в таблице
                    $('#authorsTableBody').html(response.authorsHtml);
					$('#pagination').html(response.paginationHtml);

                    // Закрываем модальное окно и очищаем форму
                    $('#authorEditModal').modal('hide');
                    $('#authorEditForm')[0].reset();

                    alert(response.message);
                } else {
                    alert('Ошибка при обновлении автора');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {  // Ошибка валидации
                    var errors = xhr.responseJSON.errors;

                    // Пройдем по всем ошибкам и отобразим их
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
	
    // Удаление автора
    $(document).on('click', '.deleteAuthorBtn', function () {
        var authorId = $(this).data('id');  // Получаем ID автора из data-id кнопки

        // Устанавливаем ID автора в скрытое поле модального окна
        $('#deleteAuthorId').val(authorId);

        // Открываем модальное окно подтверждения удаления
        $('#authorDeleteModal').modal('show');
    });

    // Когда пользователь подтверждает удаление
    $('#confirmDeleteBtn').on('click', function () {
        var authorId = $('#deleteAuthorId').val();  // Получаем ID автора из скрытого поля
		
	    // Получаем параметры фильтров и сортировки
		var search_surname = $('input[name="search_surname"]').val();
		var search_name = $('input[name="search_name"]').val();
		var sort = $('#sort').val();
		
		// Собираем параметры запроса (кроме 'page')
		var queryParams = $.param({
			search_surname: search_surname,
			search_name: search_name,
			sort: sort
		});
		
		if(queryParams) {
			queryParams = '?' + queryParams;
		} else {
			queryParams = '';
		}

        $.ajax({
            url: '/authors/' + authorId + queryParams,  // Роут для удаления
            method: 'DELETE',
            success: function (response) {
                if (response.status === 'success') {

                    // Закрыть модальное окно
                    $('#authorDeleteModal').modal('hide');

                    // Показать сообщение об успешном удалении
                    alert(response.message);
					
                    // Обновляем информацию в таблице
                    $('#authorsTableBody').html(response.authorsHtml);
					$('#pagination').html(response.paginationHtml);
					
                } else {
                    alert('Ошибка при удалении автора');
                }
            },
            error: function () {
                alert('Произошла ошибка при удалении');
            }
        });
    });
});
</script>
@endsection