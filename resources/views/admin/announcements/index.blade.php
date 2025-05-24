@extends('layouts.admin')

@section('content')
<div class="admin-announcements-page py-5">
    <div class="container-xl">
        {{-- Sayfa Başlığı ve Yeni Ekle Butonu --}}
        <div class="page-header-container mb-5 d-flex justify-content-between align-items-center">
            <h1 class="page-main-title mb-0">Duyuru Yönetimi</h1>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-add-new">
                <i class="fas fa-plus me-2"></i> YENİ DUYURU EKLE
            </a>
        </div>

        {{-- Başarı Mesajları --}}
        @if(session('success'))
            <div class="alert alert-success custom-alert-styling alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle alert-icon me-2"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Duyuru Listesi --}}
        @if($announcements->count() > 0)
            <div class="announcement-list-grid">
                @foreach($announcements as $announcement)
                    <div class="announcement-item-card">
                        <div class="card-content">
                            <div class="item-header mb-3">
                                <h3 class="item-title">{{ $announcement->title }}</h3>
                                <span class="item-status-badge status-{{ $announcement->status }}">
                                    {{ $announcement->status === 'active' ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>
                            <p class="item-excerpt mb-4">
                                {{ Str::limit(strip_tags($announcement->content), 160) }}
                            </p>
                            <div class="item-footer">
                                <small class="item-date">{{ $announcement->created_at->translatedFormat('j F Y, H:i') }}</small>
                                <div class="item-actions">
                                    <a href="{{ route('admin.announcements.edit', $announcement) }}" 
                                       class="btn btn-action btn-edit">
                                        <i class="fas fa-pencil-alt me-1"></i> Düzenle
                                    </a>
                                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Bu duyuruyu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="fas fa-trash-alt me-1"></i> Sil
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-items-card">
                <i class="fas fa-info-circle icon-no-items mb-3"></i>
                <h4 class="title-no-items">Henüz Duyuru Yok</h4>
                <p class="subtitle-no-items">İlk duyurunuzu ekleyerek başlayabilirsiniz.</p>
                <a href="{{ route('admin.announcements.create') }}" class="btn btn-add-new mt-3">
                    <i class="fas fa-plus me-2"></i> İLK DUYURUYU EKLE
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Genel Sayfa Stilleri */
    .admin-announcements-page {
        background-color: #111827; /* Koyu Ana Arkaplan (Sepet referansına yakın) */
        color: #d1d5db; /* Açık gri metin */
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .page-header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1rem;
        border-bottom: 1px solid #374151;
        max-width: 95%;
        margin: 0 auto;
    }
    .page-main-title {
        font-size: 1.875rem; /* 30px */
        font-weight: 700;
        color: #f9fafb; /* Neredeyse beyaz */
        margin-bottom: 0.25rem;
    }
    .page-main-subtitle {
        font-size: 0.875rem; /* 14px */
        color: #9ca3af;
    }
    .btn-add-new {
        background-color: #10b981; /* Sepetteki yeşil */
        color: #ffffff;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem; /* 6px */
        transition: background-color 0.2s ease-in-out, transform 0.1s ease;
    }
    .btn-add-new:hover {
        background-color: #059669;
        color: #ffffff;
        transform: scale(1.02);
    }

    /* Başarı Mesajı */
    .custom-alert-styling {
        background-color: rgba(16, 185, 129, 0.1);
        color: #a7f3d0;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 0.375rem;
    }
    .custom-alert-styling .alert-icon {
        color: #10b981;
    }
    .custom-alert-styling .btn-close {
        filter: invert(80%) sepia(30%) saturate(400%) hue-rotate(100deg) brightness(120%) contrast(80%);
    }

    /* Duyuru Listesi ve Kartları */
    .announcement-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem; /* 24px */
    }
    .announcement-item-card {
        background-color: #1f2937; /* Kart arkaplanı (Sepet referansına yakın) */
        border-radius: 0.5rem; /* 8px */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
    }
    .announcement-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -4px rgba(0, 0, 0, 0.15);
    }
    .announcement-item-card .card-content {
        padding: 1.5rem; /* 24px */
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .item-title {
        font-size: 1.25rem; /* 20px */
        font-weight: 600;
        color: #e5e7eb;
        margin-bottom: 0;
        line-height: 1.4;
    }
    .item-status-badge {
        font-size: 0.75rem; /* 12px */
        font-weight: 600;
        padding: 0.25em 0.6em;
        border-radius: 0.25rem;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .status-active {
        background-color: rgba(16, 185, 129, 0.2); /* Yeşilimsi arkaplan */
        color: #6ee7b7; /* Yeşil metin */
    }
    .status-inactive {
        background-color: rgba(107, 114, 128, 0.2); /* Gri arkaplan */
        color: #9ca3af; /* Gri metin */
    }
    .item-excerpt {
        font-size: 0.875rem; /* 14px */
        color: #9ca3af;
        line-height: 1.6;
        flex-grow: 1;
    }
    .item-footer {
        margin-top: auto; /* En alta yaslar */
        padding-top: 1rem;
        border-top: 1px solid #374151;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .item-date {
        font-size: 0.75rem; /* 12px */
        color: #6b7280;
    }
    .item-actions {
        display: flex;
        gap: 0.75rem;
    }
    .item-actions .btn-action {
        background-color: transparent;
        border: 1px solid #4b5563;
        color: #9ca3af;
        padding: 0.3rem 0.6rem;
        border-radius: 0.25rem;
        margin-left: 0;
        transition: all 0.2s ease;
    }
    .item-actions .btn-edit:hover {
        background-color: #4b5563;
        color: #e5e7eb;
        border-color: #6b7280;
    }
    .item-actions .btn-delete:hover {
        background-color: #ef4444; /* Kırmızı hover */
        color: #ffffff;
        border-color: #ef4444;
    }

    /* Duyuru Yoksa Kartı */
    .no-items-card {
        background-color: #1f2937;
        padding: 2.5rem;
        border-radius: 0.5rem;
        text-align: center;
        color: #9ca3af;
    }
    .icon-no-items {
        font-size: 3rem;
        color: #4b5563;
    }
    .title-no-items {
        font-size: 1.5rem;
        font-weight: 600;
        color: #e5e7eb;
        margin-bottom: 0.5rem;
    }
    .subtitle-no-items {
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush
@endsection 