@php($c = $course ?? null)
@csrf

<div class="form-group">
    <label class="form-label" for="code">Kode MK</label>
    <input class="form-input" type="text" id="code" name="code" maxlength="20" required
           value="{{ old('code', $c?->code) }}" placeholder="Contoh: IF-2023">
    @error('code')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="name">Nama MK</label>
    <input class="form-input" type="text" id="name" name="name" required
           value="{{ old('name', $c?->name) }}" placeholder="Contoh: Pemrograman Web">
    @error('name')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="description">Deskripsi</label>
    <textarea class="form-input" id="description" name="description" rows="3"
              placeholder="Opsional">{{ old('description', $c?->description) }}</textarea>
    @error('description')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="sks">SKS</label>
    <input class="form-input" type="number" id="sks" name="sks" min="1" max="6" required
           value="{{ old('sks', $c?->sks) }}">
    @error('sks')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="lecturer_id">Dosen Pengampu</label>
    <select class="form-select" id="lecturer_id" name="lecturer_id" required>
        <option value="">-- Pilih dosen --</option>
        @foreach ($lecturers as $lecturer)
            <option value="{{ $lecturer->id }}" @selected(old('lecturer_id', $c?->lecturer_id) == $lecturer->id)>
                {{ $lecturer->name }} ({{ $lecturer->nim_nip }})
            </option>
        @endforeach
    </select>
    @error('lecturer_id')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label class="form-label" for="status">Status</label>
    <select class="form-select" id="status" name="status" required>
        @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'archived' => 'Arsip'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $c?->status ?? 'draft') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('status')<span class="form-error">{{ $message }}</span>@enderror
</div>
