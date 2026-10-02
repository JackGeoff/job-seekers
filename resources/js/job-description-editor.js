import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const editorOptions = {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ header: [2, 3, 4, false] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link'],
            ['clean'],
        ],
    },
};

export function initializeJobDescriptionEditors(root = document) {
    const wrappers = root.matches?.('[data-job-description-editor]')
        ? [root]
        : root.querySelectorAll('[data-job-description-editor]');

    wrappers.forEach((wrapper) => {
    if (wrapper.dataset.editorInitialized === 'true') {
        return;
    }

    const editorElement = wrapper.querySelector('[data-quill-editor]');
    const source = wrapper.querySelector('[data-job-description-source]')
        || wrapper.querySelector('textarea[name="description"], textarea[name$="[description]"]');
    const form = wrapper.closest('form');

    if (!editorElement || !source || !form) {
        return;
    }

    wrapper.dataset.editorInitialized = 'true';

    const editor = new Quill(editorElement, {
        ...editorOptions,
        placeholder: source.placeholder,
        bounds: wrapper,
    });

    wrapper.classList.add('job-description-quill');
    editor.root.setAttribute('aria-labelledby', 'description-label');
    editor.root.setAttribute('aria-required', 'true');

    const initialHtml = editorElement.dataset.initialHtml;

    if (initialHtml) {
        editor.clipboard.dangerouslyPasteHTML(initialHtml, 'silent');
    }

    const syncDescription = () => {
        source.value = editor.root.innerHTML;
    };

    editor.on('text-change', syncDescription);
    form.addEventListener('submit', syncDescription);

    editorElement.hidden = false;
    source.hidden = true;
    source.required = false;
    syncDescription();
});
}