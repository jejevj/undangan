@php
    $groupLabels = [
        'mempelai' => 'Data Mempelai',
        'acara'    => 'Data Acara',
        'tambahan' => 'Informasi Tambahan',
        ''         => 'Lainnya',
        null       => 'Lainnya',
    ];
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
