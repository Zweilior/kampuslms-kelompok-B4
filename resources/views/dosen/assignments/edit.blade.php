<x-layout title="Edit Tugas">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Edit Tugas</h2>
            <form action="#" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium">Judul Tugas</label>
                    <input type="text" name="title" value="Tugas 1: Portofolio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Deadline</label>
                    <input type="datetime-local" name="deadline" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                </div>
                <div class="flex justify-end">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>