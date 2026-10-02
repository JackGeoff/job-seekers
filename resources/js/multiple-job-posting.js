const makeSubmissionKey = () => {
    if (crypto.randomUUID) {
        return crypto.randomUUID();
    }

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (character) => {
        const random = Math.floor(Math.random() * 16);
        return (character === 'x' ? random : (random & 0x3) | 0x8).toString(16);
    });
};

export function initializeMultipleJobPosting() {
    const form = document.querySelector('[data-job-posting-form]');
    const entriesContainer = form?.querySelector('[data-job-entries]');
    const addButton = form?.querySelector('[data-add-job]');
    const oldEntriesElement = form?.querySelector('[data-old-job-entries]');
    const firstEntry = entriesContainer?.querySelector('[data-job-entry]');
    const maxForms = Number(entriesContainer?.dataset.maxForms || 1);

    if (!form || !entriesContainer || !addButton || !firstEntry || !oldEntriesElement) {
        return;
    }

    const templateFields = firstEntry.querySelector('[data-job-entry-fields]').innerHTML;
    const defaultStatus = entriesContainer.dataset.defaultStatus || 'draft';
    let oldEntries = [];

    try {
        const parsedEntries = JSON.parse(oldEntriesElement.textContent || '[]');
        oldEntries = Array.isArray(parsedEntries) ? parsedEntries : Object.values(parsedEntries);
    } catch {
        oldEntries = [];
    }

    const setUniqueIds = (entry, index) => {
        const fields = entry.querySelector('[data-job-entry-fields]');
        const replacements = new Map();

        fields.querySelectorAll('[id]').forEach((element) => {
            const oldId = element.id;
            const baseId = element.dataset.jobBaseId || oldId.replace(/-job-\d+$/, '');
            element.dataset.jobBaseId = baseId;
            const newId = index === 0 ? baseId : `${baseId}-job-${index + 1}`;
            replacements.set(oldId, newId);
            element.id = newId;
        });

        fields.querySelectorAll('[for], [aria-controls], [aria-describedby], [aria-labelledby]').forEach((element) => {
            ['for', 'aria-controls', 'aria-describedby', 'aria-labelledby'].forEach((attribute) => {
                const value = element.getAttribute(attribute);

                if (value) {
                    element.setAttribute(attribute, value.split(/\s+/).map((id) => replacements.get(id) || id).join(' '));
                }
            });
        });
    };

    const setFieldNames = (entry, index) => {
        entry.querySelectorAll('[name]').forEach((field) => {
            const baseName = field.dataset.jobFieldName || field.getAttribute('name');
            field.dataset.jobFieldName = baseName;
            field.name = `jobs[${index}][${baseName}]`;
        });
    };

    const resetEntry = (entry) => {
        entry.querySelectorAll('[name]').forEach((field) => {
            const name = field.dataset.jobFieldName;

            if (field.type === 'radio') {
                field.checked = field.value === defaultStatus;
            } else if (field.type === 'hidden' && name === 'submission_key') {
                field.value = makeSubmissionKey();
            } else if (field.tagName === 'SELECT') {
                field.value = name === 'salary_currency' ? 'KES' : '';
            } else if (field.type !== 'checkbox') {
                field.value = '';
            }
        });

        entry.querySelectorAll('[data-quill-editor]').forEach((editor) => {
            editor.dataset.initialHtml = '';
            editor.replaceChildren();
        });
        entry.querySelectorAll('[data-category-status]').forEach((status) => {
            status.textContent = '';
        });
    };

    const restoreEntry = (entry, values) => {
        entry.querySelectorAll('[name]').forEach((field) => {
            const value = values[field.dataset.jobFieldName];

            if (value === undefined || value === null) {
                return;
            }

            if (field.type === 'radio') {
                field.checked = String(value) === field.value;
            } else if (field.type === 'checkbox') {
                field.checked = Boolean(value);
            } else {
                field.value = value;
            }

            if (field.dataset.jobFieldName === 'description') {
                entry.querySelector('[data-quill-editor]').dataset.initialHtml = value;
            }
        });
    };

    const buildEntry = (index, values = null) => {
        const entry = document.createElement('section');
        entry.dataset.jobEntry = '';
        entry.dataset.index = String(index);
        entry.className = 'mb-6 rounded-2xl border border-slate-200 p-4 sm:p-6';

        const heading = document.createElement('div');
        heading.className = 'mb-6 flex items-center justify-between gap-4';

        const title = document.createElement('h2');
        title.dataset.jobNumber = '';
        title.className = 'text-lg font-semibold text-slate-950';
        title.textContent = `Job ${index + 1}`;
        heading.append(title);

        if (index > 0) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.dataset.removeJob = '';
            removeButton.className = 'rounded-lg px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50';
            removeButton.textContent = 'Remove';
            removeButton.setAttribute('aria-label', `Remove job ${index + 1}`);
            removeButton.addEventListener('click', () => {
                entry.dispatchEvent(new CustomEvent('job-entry:removed', { bubbles: true }));
                entry.remove();
                updateEntries();
            });
            heading.append(removeButton);
        }

        const fields = document.createElement('div');
        fields.dataset.jobEntryFields = '';
        fields.innerHTML = templateFields;
        entry.append(heading, fields);
        setUniqueIds(entry, index);
        setFieldNames(entry, index);

        if (values) {
            restoreEntry(entry, values);
        } else if (index > 0) {
            resetEntry(entry);
        }

        return entry;
    };

    const updateEntries = () => {
        const entries = entriesContainer.querySelectorAll('[data-job-entry]');
        entries.forEach((entry, index) => {
            entry.dataset.index = String(index);
            entry.querySelector('[data-job-number]').textContent = `Job ${index + 1}`;
            setUniqueIds(entry, index);
            entry.querySelectorAll('[data-job-field-name]').forEach((field) => {
                field.name = `jobs[${index}][${field.dataset.jobFieldName}]`;
            });
            const removeButton = entry.querySelector('[data-remove-job]');
            if (removeButton) {
                removeButton.setAttribute('aria-label', `Remove job ${index + 1}`);
            }
        });
        addButton.disabled = entries.length >= maxForms;
        addButton.setAttribute('aria-disabled', String(addButton.disabled));
    };

    setUniqueIds(firstEntry, 0);
    setFieldNames(firstEntry, 0);

    oldEntries.slice(1, Math.max(oldEntries.length, 1)).forEach((values, offset) => {
        const entry = buildEntry(offset + 1, values);
        entriesContainer.append(entry);
        document.dispatchEvent(new CustomEvent('job-entry:added', { detail: entry }));
    });

    addButton.addEventListener('click', () => {
        const index = entriesContainer.querySelectorAll('[data-job-entry]').length;

        if (index >= maxForms) {
            return;
        }

        const entry = buildEntry(index);
        entriesContainer.append(entry);
        document.dispatchEvent(new CustomEvent('job-entry:added', { detail: entry }));
        updateEntries();
        entry.querySelector('input[name$="[title]"]')?.focus();
    });

    form.addEventListener('submit', (event) => {
        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        form.dataset.submitting = 'true';
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.disabled = true;
        });
    });

    updateEntries();
}