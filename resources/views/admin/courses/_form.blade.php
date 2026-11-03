@php($c = $course ?? null)
@csrf

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="code">Kode MK</label>
    <input class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" type="text" id="code" name="code" maxlength="20" required
           value="{{ old('code', $c?->code) }}" placeholder="Contoh: IF-2023">
    @error('code')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="name">Nama MK</label>
    <input class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" type="text" id="name" name="name" required
           value="{{ old('name', $c?->name) }}" placeholder="Contoh: Pemrograman Web">
    @error('name')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="description">Deskripsi</label>
    <textarea class="form-input w-full resize-y rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" id="description" name="description" rows="3"
              placeholder="Opsional">{{ old('description', $c?->description) }}</textarea>
    @error('description')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="sks">SKS</label>
    <input class="form-input w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" type="number" id="sks" name="sks" min="1" max="6" required
           value="{{ old('sks', $c?->sks) }}">
    @error('sks')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="lecturer_id">Dosen Pengampu</label>
    <select class="form-select w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" id="lecturer_id" name="lecturer_id" required>
        <option value="">-- Pilih dosen --</option>
        @foreach ($lecturers as $lecturer)
            <option value="{{ $lecturer->id }}" @selected(old('lecturer_id', $c?->lecturer_id) == $lecturer->id)>
                {{ $lecturer->name }} ({{ $lecturer->nim_nip }})
            </option>
        @endforeach
    </select>
    @error('lecturer_id')<span class="form-error">{{ $message }}</span>@enderror
</div>

<div class="form-group mb-5 flex flex-col gap-2">
    <label class="form-label inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.05em] text-[var(--text-secondary)]" for="status">Status</label>
    <select class="form-select w-full rounded-[var(--radius-md)] border border-[var(--border-hairline)] bg-[var(--bg-base)] px-4 py-3 text-sm text-[var(--text-primary)] outline-none focus:border-[var(--primary)] focus:ring-4 focus:ring-[rgba(139,174,102,0.15)]" id="status" name="status" required>
        @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'archived' => 'Arsip'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $c?->status ?? 'draft') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('status')<span class="form-error">{{ $message }}</span>@enderror
</div>
