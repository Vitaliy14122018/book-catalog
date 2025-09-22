<!-- resources/views/authors/partials/delete_modal.blade.php -->
<div class="modal fade" id="authorDeleteModal" tabindex="-1" aria-labelledby="authorDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authorDeleteModalLabel">Удаление автора</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить этого автора?</p>
                <!-- Скрытое поле для ID -->
                <input type="hidden" id="deleteAuthorId" name="author_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>
</div>
