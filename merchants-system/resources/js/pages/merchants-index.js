function initMerchantsIndexPage() {
    const page = document.querySelector('.merchants-index-page .merchants-index-section');
    if (!page) return;

    const token = page.dataset.csrfToken || '';
    const canSelect = page.dataset.canSelect === '1';
    const canInlineEdit = page.dataset.canInlineEdit === '1';
    const inlineUrlTemplate = page.dataset.inlineUpdateUrlTemplate || '';

    const STORAGE_KEY = 'selected_merchants_ids';
    const STORAGE_MAP_KEY = 'selected_merchants_map';

    const getStoredSelections = () => {
        try {
            const value = JSON.parse(localStorage.getItem(STORAGE_KEY));
            return Array.isArray(value) ? value.map(String) : [];
        } catch {
            return [];
        }
    };

    const setStoredSelections = (ids) => {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(ids.map(String)));
    };

    const getStoredSelectionMap = () => {
        try {
            const value = JSON.parse(localStorage.getItem(STORAGE_MAP_KEY));
            return value && typeof value === 'object' ? value : {};
        } catch {
            return {};
        }
    };

    const setStoredSelectionMap = (map) => {
        localStorage.setItem(STORAGE_MAP_KEY, JSON.stringify(map));
    };

    const getRowCheckboxes = () => {
        return Array.from(document.querySelectorAll('.merchant-checkbox, .row-selector'))
            .filter((el, index, arr) => arr.indexOf(el) === index);
    };

    const getSelectAllCheckbox = () => {
        return document.getElementById('select-all-merchants')
            || document.getElementById('select-all-rows');
    };

    const getSelectedIds = () => getStoredSelections();

    const updateSelectedCount = () => {
        const countElement = document.getElementById('selected-count');
        if (countElement) {
            countElement.textContent = getStoredSelections().length;
        }
    };

    const updateSelectedPreview = () => {
        const ids = getStoredSelections();
        const map = getStoredSelectionMap();

        const box = document.getElementById('selected-preview-box');
        const count = document.getElementById('selected-preview-count');
        const names = document.getElementById('selected-preview-names');

        if (!box || !count || !names) return;

        count.textContent = ids.length;

        if (ids.length === 0) {
            box.style.display = 'none';
            names.innerHTML = 'لا يوجد عناصر محددة';
            return;
        }

        box.style.display = 'block';

        const selectedNames = ids.map((id) => map[id] || `#${id}`);
        const previewNames = selectedNames.slice(0, 10);

        let html = previewNames
            .map((name) => `<span class="merchants-selected-name-badge">${name}</span>`)
            .join(' ');

        if (selectedNames.length > 10) {
            html += `<div class="mt-2 text-muted">و ${selectedNames.length - 10} عنصر إضافي...</div>`;
        }

        names.innerHTML = html;
    };

    const updateSelectAllState = () => {
        const allCheckboxes = getRowCheckboxes();
        const checkedCheckboxes = allCheckboxes.filter((cb) => cb.checked);
        const selectAll = getSelectAllCheckbox();

        if (!selectAll) return;

        if (allCheckboxes.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }

        if (checkedCheckboxes.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else if (checkedCheckboxes.length === allCheckboxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    };

    const updateSelectionUI = () => {
        updateSelectedCount();
        updateSelectedPreview();
        updateSelectAllState();
    };

    const addSelection = (id, name) => {
        const stringId = String(id);
        const ids = getStoredSelections();
        const map = getStoredSelectionMap();

        if (!ids.includes(stringId)) {
            ids.push(stringId);
        }

        map[stringId] = name || `#${stringId}`;

        setStoredSelections(ids);
        setStoredSelectionMap(map);
        updateSelectionUI();
    };

    const removeSelection = (id) => {
        const stringId = String(id);
        let ids = getStoredSelections();
        const map = getStoredSelectionMap();

        ids = ids.filter((item) => item !== stringId);
        delete map[stringId];

        setStoredSelections(ids);
        setStoredSelectionMap(map);
        updateSelectionUI();
    };

    const syncCheckboxesWithStorage = () => {
        const ids = getStoredSelections();

        getRowCheckboxes().forEach((cb) => {
            cb.checked = ids.includes(String(cb.value));
        });

        updateSelectionUI();
    };

    const clearAllSelections = (showMessage = true) => {
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(STORAGE_MAP_KEY);

        getRowCheckboxes().forEach((cb) => {
            cb.checked = false;
        });

        const selectAll = getSelectAllCheckbox();
        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }

        updateSelectionUI();

        if (showMessage) {
            alert('تم إلغاء جميع الاختيارات');
        }
    };

    const fillSelectedInputs = (containerId, selectedIds) => {
        const container = document.getElementById(containerId);
        if (!container) return false;

        container.innerHTML = '';

        selectedIds.forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_ids[]';
            input.value = String(id);
            container.appendChild(input);
        });

        return true;
    };

    const showSelectedOnly = () => {
        const selectedIds = getSelectedIds();

        if (selectedIds.length === 0) {
            alert('لا يوجد تجار محددون');
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.delete('page');
        url.searchParams.delete('selected_ids[]');
        url.searchParams.delete('selected_ids');

        selectedIds.forEach((id) => {
            url.searchParams.append('selected_ids[]', id);
        });

        window.location.href = url.toString();
    };

    const bindSelectionEvents = () => {
        if (!canSelect) return;

        getRowCheckboxes().forEach((cb) => {
            cb.addEventListener('change', function () {
                const name = this.dataset.name || `#${this.value}`;

                if (this.checked) {
                    addSelection(this.value, name);
                } else {
                    removeSelection(this.value);
                }
            });
        });

        const selectAll = getSelectAllCheckbox();
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const shouldCheck = this.checked;

                getRowCheckboxes().forEach((cb) => {
                    cb.checked = shouldCheck;

                    const name = cb.dataset.name || `#${cb.value}`;

                    if (shouldCheck) {
                        const ids = getStoredSelections();
                        const map = getStoredSelectionMap();
                        const stringId = String(cb.value);

                        if (!ids.includes(stringId)) {
                            ids.push(stringId);
                        }

                        map[stringId] = name || `#${stringId}`;
                        setStoredSelections(ids);
                        setStoredSelectionMap(map);
                    } else {
                        let ids = getStoredSelections();
                        const map = getStoredSelectionMap();
                        const stringId = String(cb.value);

                        ids = ids.filter((item) => item !== stringId);
                        delete map[stringId];

                        setStoredSelections(ids);
                        setStoredSelectionMap(map);
                    }
                });

                updateSelectionUI();
            });
        }

        const printBtn = document.getElementById('print-selected-btn');
        if (printBtn) {
            printBtn.addEventListener('click', () => {
                const selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    alert('اختر تاجرًا واحدًا على الأقل للطباعة');
                    return;
                }

                const ok = fillSelectedInputs('print-selected-inputs', selectedIds);
                if (!ok) {
                    alert('تعذر تجهيز نموذج الطباعة');
                    return;
                }

                document.getElementById('print-selected-form')?.submit();
            });
        }

        const exportBtn = document.getElementById('export-selected-btn');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => {
                const selectedIds = getSelectedIds();

                if (selectedIds.length === 0) {
                    alert('اختر تاجرًا واحدًا على الأقل للتصدير');
                    return;
                }

                const ok = fillSelectedInputs('export-selected-inputs', selectedIds);
                if (!ok) {
                    alert('تعذر تجهيز نموذج التصدير');
                    return;
                }

                document.getElementById('export-selected-form')?.submit();
            });
        }

        document.getElementById('show-selected-only-btn')?.addEventListener('click', showSelectedOnly);
        document.getElementById('show-selected-only-btn-top')?.addEventListener('click', showSelectedOnly);
        document.getElementById('clear-selected-btn')?.addEventListener('click', () => clearAllSelections(true));
        document.getElementById('clear-selected-btn-2')?.addEventListener('click', () => clearAllSelections(true));

        syncCheckboxesWithStorage();
    };

    const bindInlineEditEvents = () => {
        if (!canInlineEdit) return;

        document.querySelectorAll('.inline-save-btn').forEach((button) => {
            button.addEventListener('click', async function () {
                const merchantId = this.dataset.id;
                const row = document.getElementById(`merchant-row-${merchantId}`);
                if (!row) return;

                const contactedInput = row.querySelector('.inline-contacted');
                const invitedInput = row.querySelector('.inline-invited');
                const notesInput = row.querySelector('.merchants-inline-notes');
                const statusMsg = document.getElementById(`status-msg-${merchantId}`);

                const payload = {
                    contacted: contactedInput ? (contactedInput.checked ? 1 : 0) : 0,
                    invited: invitedInput ? (invitedInput.checked ? 1 : 0) : 0,
                    notes: notesInput ? notesInput.value : '',
                };

                const originalText = this.textContent;
                this.disabled = true;
                this.textContent = 'جاري الحفظ...';

                try {
                    const response = await fetch(inlineUrlTemplate.replace(':id', merchantId), {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            Accept: 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    let data = {};
                    try {
                        data = await response.json();
                    } catch {
                        data = {};
                    }

                    if (!response.ok) {
                        throw data;
                    }

                    row.classList.add('table-success');

                    if (statusMsg) {
                        statusMsg.textContent = data.message || 'تم الحفظ';
                        statusMsg.classList.remove('d-none');
                    }

                    setTimeout(() => {
                        row.classList.remove('table-success');
                        if (statusMsg) {
                            statusMsg.classList.add('d-none');
                        }
                    }, 1500);
                } catch (error) {
                    alert(error?.message || 'حدث خطأ أثناء الحفظ');
                    console.error(error);
                } finally {
                    this.disabled = false;
                    this.textContent = originalText || 'حفظ';
                }
            });
        });
    };

    const bindFilteredActions = () => {
        const printAllFilteredBtn = document.getElementById('print-all-filtered-btn');
        if (printAllFilteredBtn) {
            printAllFilteredBtn.addEventListener('click', () => {
                document.getElementById('print-all-filtered-form')?.submit();
            });
        }

        const exportAllFilteredBtn = document.getElementById('export-all-filtered-btn');
        if (exportAllFilteredBtn) {
            exportAllFilteredBtn.addEventListener('click', () => {
                document.getElementById('export-all-filtered-form')?.submit();
            });
        }
    };

    bindInlineEditEvents();
    bindSelectionEvents();
    bindFilteredActions();
}

document.addEventListener('DOMContentLoaded', initMerchantsIndexPage);
