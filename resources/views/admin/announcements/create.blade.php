@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9 col-12">
            <div class="card mt-5 bg-dark text-white border-0 shadow-lg p-4" style="border-radius: 16px;">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="form-label text-muted small text-uppercase">Başlık</label>
                        <input type="text"
                               class="form-control custom-input @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title') }}"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="content" class="form-label text-muted small text-uppercase">İçerik</label>
                        <textarea class="form-control custom-input @error('content') is-invalid @enderror"
                                  id="content"
                                  name="content"
                                  rows="10"
                                  required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            KAYDET
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-darker {
        background-color: #1a1e24 !important;
    }
    .custom-input {
        background-color: #181c23 !important;
        border: 2px solid #232a36 !important;
        color: #fff !important;
        border-radius: 8px !important;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: border-color 0.2s;
    }
    .custom-input:focus {
        border-color: #10b981 !important;
        background-color: #181c23 !important;
        color: #fff !important;
        box-shadow: 0 0 0 2px rgba(16,185,129,0.15);
    }
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    .btn-primary {
        background-color: #6366f1;
        border-color: #6366f1;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 1.1rem;
        border-radius: 8px;
    }
    .btn-primary:hover {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .card {
        border-radius: 16px !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
        })
        .then(editor => {
            editor.editing.view.change(writer => {
                writer.setStyle('background-color', '#1a1e24', editor.editing.view.document.getRoot());
                writer.setStyle('color', '#fff', editor.editing.view.document.getRoot());
                writer.setStyle('min-height', '300px', editor.editing.view.document.getRoot());
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endpush
@endsection 