@php
    $categoryOptions = collect($categoryGroups)->flatten()->unique()->values();
    $requestedCategory = is_string($selectedCategory) ? trim($selectedCategory) : '';
    $selectedCategory = $categoryOptions->contains($requestedCategory)
        ? $requestedCategory
        : ($legacyCategory ?? '');
@endphp

<div data-category-dropdown class="relative">
    <label for="category" class="mb-2 block text-sm font-semibold text-slate-800">
        Category
        <span class="text-red-600">*</span>
    </label>

    <div class="relative">
        <input
            id="category"
            name="category"
            data-category-value
            type="text"
            value="{{ $selectedCategory }}"
            readonly
            required
            role="combobox"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-controls="category-options"
            aria-describedby="category-status"
            placeholder="Select a job category"
            class="auth-input h-12 w-full cursor-pointer rounded-xl border bg-white px-4 pr-11 text-left text-slate-950 outline-none transition @error('category') border-red-500 @else border-slate-200 @enderror"
        >
        <svg
            class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
        >
            <path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </svg>
    </div>

    <div
        data-category-menu
        class="absolute left-0 right-0 top-full z-50 mt-2 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
    >
        <div class="border-b border-slate-100 p-3">
            <label for="category-filter" class="sr-only">Search categories</label>
            <input
                id="category-filter"
                data-category-filter
                type="search"
                autocomplete="off"
                placeholder="Search categories"
                class="auth-input h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-950 outline-none"
            >
        </div>

        <ul
            id="category-options"
            data-category-options
            role="listbox"
            aria-label="Job categories"
            class="max-h-60 overflow-y-auto py-1"
        ></ul>

        <p data-category-empty class="hidden px-4 py-3 text-sm text-slate-500">
            No categories found
        </p>
    </div>

    <p id="category-status" data-category-status class="sr-only" role="status" aria-live="polite"></p>
    <script type="application/json" data-category-data>@json($categoryGroups)</script>

    @error('category')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>