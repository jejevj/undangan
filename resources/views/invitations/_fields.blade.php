@php
    $groupLabels = [
        'mempelai' => 'Data Mempelai',
        'acara'    => 'Data Acara',
        'tambahan' => 'Informasi Tambahan',
        'musik'    => 'Musik Latar',
        ''         => 'Lainnya',
        null       => 'Lainnya',
    ];
    // $accessibleMusic dikirim dari controller
    $musicList = $accessibleMusic ?? collect();
@endphp

@foreach($fieldsByGroup as $group => $fields)
<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title">{{ $groupLabels[$group] ?? ucfirst($group) }}</h4>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($fields as $field)
            @php
                $value = old('fields.' . $field->key, $existingData[$field->key] ?? $field->default_value);
                $inputName = 'fields[' . $field->key . ']';
                $hasError = $errors->has('fields.' . $field->key);
            @endphp
            <div class="col-md-{{ in_array($field->type, ['textarea']) ? '12' : '6' }} form-group mb-3">
                <label>
                    {{ $field->label }}
                    @if($field->required) <span class="text-danger">*</span> @endif
                </label>

                @if($field->type === 'textarea')
                    <textarea name="{{ $inputName }}" class="form-control {{ $hasError ? 'is-invalid' : '' }}"
                        rows="3" {{ $field->required ? 'required' : '' }}
                        placeholder="{{ $field->placeholder }}">{{ $value }}</textarea>

                @elseif($field->type === 'image')
                    @if($value)
                        <div class="mb-1">
                            <img src="{{ asset('storage/' . $value) }}" class="img-thumbnail" style="height:80px">
                        </div>
                    @endif
                    <input type="file" name="{{ $inputName }}" class="form-control {{ $hasError ? 'is-invalid' : '' }}"
                        accept="image/*" {{ $field->required && !$value ? 'required' : '' }}>

                @elseif($field->key === 'music_url')
                    {{-- Dropdown khusus untuk pilih lagu --}}
                    @if($musicList->isEmpty())
                        <div class="alert alert-warning py-2 mb-1">
                            Belum ada lagu tersedia.
                            <a href="{{ route('music.index') }}" target="_blank">Beli atau upload lagu</a>
                        </div>
                    @else
                        <select name="{{ $inputName }}" class="form-control {{ $hasError ? 'is-invalid' : '' }}"
                            id="musicSelect" {{ $field->required ? 'required' : '' }}>
                            <option value="">— Tidak ada musik —</option>
                            @php
                                $userUploads = $musicList->filter(fn($m) => $m->isUserUpload());
                                $systemSongs = $musicList->filter(fn($m) => !$m->isUserUpload());
                            @endphp
                            @if($userUploads->count())
                                <optgroup label="📁 Upload Saya">
                                    @foreach($userUploads as $song)
                                        <option value="{{ $song->audioUrl() }}"
                                            data-title="{{ $song->title }}"
                                            data-artist="{{ $song->artist }}"
                                            data-preview="{{ $song->audioUrl() }}"
                                            {{ $value === $song->audioUrl() ? 'selected' : '' }}>
                                            {{ $song->title }}{{ $song->artist ? ' — ' . $song->artist : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @if($systemSongs->count())
                                <optgroup label="🎵 Lagu Tersedia">
                                    @foreach($systemSongs as $song)
                                        <option value="{{ $song->audioUrl() }}"
                                            data-title="{{ $song->title }}"
                                            data-artist="{{ $song->artist }}"
                                            data-preview="{{ $song->audioUrl() }}"
                                            {{ $value === $song->audioUrl() ? 'selected' : '' }}>
                                            {{ $song->title }}{{ $song->artist ? ' — ' . $song->artist : '' }}
                                            ({{ $song->isFree() ? 'Gratis' : 'Premium' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        {{-- Preview audio --}}
                        <div id="musicPreviewWrap" class="mt-2" style="{{ $value ? '' : 'display:none' }}">
                            <audio id="musicPreview" controls class="w-100" style="height:32px">
                                @if($value)<source src="{{ $value }}" type="audio/mpeg">@endif
                            </audio>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Pilih lagu dari daftar di atas</small>
                            <a href="{{ route('music.index') }}" target="_blank" class="small">
                                + Beli / Upload lagu
                            </a>
                        </div>
                    @endif

                @elseif($field->key === 'music_title' || $field->key === 'music_artist')
                    {{-- Auto-fill dari pilihan dropdown musik --}}
                    <input type="text" name="{{ $inputName }}"
                        class="form-control {{ $hasError ? 'is-invalid' : '' }} music-meta-field"
                        data-meta="{{ $field->key === 'music_title' ? 'title' : 'artist' }}"
                        value="{{ $value }}"
                        placeholder="{{ $field->placeholder }}">

                @elseif($field->type === 'select')
                    <select name="{{ $inputName }}" class="form-control {{ $hasError ? 'is-invalid' : '' }}" {{ $field->required ? 'required' : '' }}>
                        <option value="">-- Pilih --</option>
                        @foreach($field->options ?? [] as $opt)
                            <option value="{{ $opt }}" {{ $value == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>

                @else
                    <input type="{{ $field->type }}" name="{{ $inputName }}"
                        class="form-control {{ $hasError ? 'is-invalid' : '' }}"
                        value="{{ $value }}"
                        placeholder="{{ $field->placeholder }}"
                        {{ $field->required ? 'required' : '' }}>
                @endif

                @error('fields.' . $field->key)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

@once
@push('scripts')
<script>
// Auto-fill judul & artis saat pilih lagu dari dropdown
const musicSelect = document.getElementById('musicSelect');
if (musicSelect) {
    musicSelect.addEventListener('change', function () {
        const opt     = this.options[this.selectedIndex];
        const title   = opt.dataset.title   || '';
        const artist  = opt.dataset.artist  || '';
        const preview = opt.dataset.preview || '';

        // Isi field music_title dan music_artist
        document.querySelectorAll('.music-meta-field').forEach(el => {
            if (el.dataset.meta === 'title')  el.value = title;
            if (el.dataset.meta === 'artist') el.value = artist;
        });

        // Update preview audio
        const wrap    = document.getElementById('musicPreviewWrap');
        const audioEl = document.getElementById('musicPreview');
        if (preview && audioEl) {
            audioEl.src = preview;
            audioEl.load();
            wrap.style.display = '';
        } else if (wrap) {
            wrap.style.display = 'none';
        }
    });
}
</script>
@endpush
@endonce
