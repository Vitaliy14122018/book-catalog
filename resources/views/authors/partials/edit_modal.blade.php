<!-- resources/views/authors/partials/edit_modal.blade.php -->
<div class="modal fade" id="authorEditModal" tabindex="-1" aria-labelledby="authorEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authorEditModalLabel">Редактировать автора</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="authorEditForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editAuthorId" name="author_id">

                    <div class="mb-3">
                        <label for="editSurname" class="form-label">Фамилия</label>
                        <input type="text" class="form-control" id="editSurname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="editName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPatronymic" class="form-label">Отчество</label>
                        <input type="text" class="form-control" id="editPatronymic" name="patronymic">
                    </div>
                    <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>
    </div>
</div>