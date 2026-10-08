<x-layout title="Edit Mata Kuliah">
    <div class="admin-course-form">
        <div class="admin-course-form__card">
            <h1 class="admin-course-form__title">Edit Mata Kuliah</h1>

            <form action="{{ route('admin.courses.update', $course) }}" method="POST">
                @method('PUT')
                @include('admin.courses._form')

                <div class="admin-course-form__actions">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn--ghost">Batal</a>
                    <button type="submit" class="btn btn--primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
