document.addEventListener('DOMContentLoaded', () => {
    const mapelByKategori = {
        Umum: ['Matematika', 'IPA', 'IPS', 'Sejarah', 'PPKn', 'TIK'],
        Bahasa: ['Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Jawa', 'Bahasa Arab'],
        Agama: ['Fikih', 'Akidah Akhlak', "Al-Qur'an Hadis", 'Sejarah Kebudayaan Islam'],
    };

    document.querySelectorAll('[data-mapel-category-select]').forEach((kategoriSelect) => {
        const form = kategoriSelect.closest('form');
        const mapelSelect = form?.querySelector('[data-mapel-select]');

        if (!mapelSelect) {
            return;
        }

        const appendOption = (value, label) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            mapelSelect.appendChild(option);

            return option;
        };

        const renderMapelOptions = () => {
            const kategori = kategoriSelect.value;
            const mapelOptions = mapelByKategori[kategori] || [];
            const selectedMapel = mapelSelect.dataset.selectedMapel || '';

            mapelSelect.innerHTML = '';

            if (!mapelOptions.length) {
                appendOption('', 'Pilih kategori mapel terlebih dahulu');
                mapelSelect.disabled = true;
                return;
            }

            appendOption('', 'Pilih mata pelajaran');
            mapelOptions.forEach((mapel) => {
                const option = appendOption(mapel, mapel);

                if (mapel === selectedMapel) {
                    option.selected = true;
                }
            });

            mapelSelect.disabled = false;
        };

        kategoriSelect.addEventListener('change', () => {
            mapelSelect.dataset.selectedMapel = '';
            renderMapelOptions();
        });

        renderMapelOptions();
    });

    const initAdminTableSelection = (tableSection, getSelectableRows) => {
        const selectAllInput = tableSection.querySelector('[data-admin-select-all]');
        const rowCheckboxes = Array.from(tableSection.querySelectorAll('[data-admin-row-checkbox]'));
        const bulkBar = tableSection.querySelector('[data-admin-bulk-bar]');
        const selectedCountTarget = tableSection.querySelector('[data-admin-selected-count]');
        const bulkEmailOpenButton = tableSection.querySelector('[data-admin-bulk-email-open]');
        const bulkEmailModal = tableSection.querySelector('[data-admin-bulk-email-modal]');
        const bulkEmailCountTarget = tableSection.querySelector('[data-admin-bulk-email-count]');
        const bulkEmailMessageInput = tableSection.querySelector('[data-admin-bulk-email-message]');
        const bulkEmailForm = tableSection.querySelector('[data-admin-bulk-email-form]');
        const bulkEmailIdsTarget = tableSection.querySelector('[data-admin-bulk-email-ids]');
        const bulkEmailSubmitButton = tableSection.querySelector('[data-admin-bulk-email-submit]');
        const bulkEmailCloseButtons = Array.from(tableSection.querySelectorAll('[data-admin-bulk-email-close]'));

        const getVisibleCheckboxes = () => getSelectableRows()
            .map((row) => row.querySelector('[data-admin-row-checkbox]'))
            .filter(Boolean);

        const getCheckedCheckboxes = () => rowCheckboxes.filter((input) => input.checked);

        const syncBulkEmailIds = () => {
            if (!bulkEmailIdsTarget) {
                return;
            }

            bulkEmailIdsTarget.innerHTML = '';

            getCheckedCheckboxes().forEach((checkbox) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                bulkEmailIdsTarget.appendChild(input);
            });
        };

        const closeBulkEmailModal = () => {
            if (bulkEmailModal) {
                bulkEmailModal.hidden = true;
            }
        };

        const openBulkEmailModal = () => {
            if (!bulkEmailModal || getCheckedCheckboxes().length === 0) {
                return;
            }

            syncBulkEmailIds();
            bulkEmailModal.hidden = false;
            bulkEmailMessageInput?.focus();
        };

        const update = () => {
            const checkedCount = getCheckedCheckboxes().length;

            if (bulkBar) {
                bulkBar.hidden = checkedCount === 0;
            }

            if (selectedCountTarget) {
                selectedCountTarget.textContent = checkedCount.toString();
            }

            if (bulkEmailCountTarget) {
                bulkEmailCountTarget.textContent = checkedCount.toString();
            }

            syncBulkEmailIds();

            if (checkedCount === 0) {
                closeBulkEmailModal();
            }

            if (!selectAllInput) {
                return;
            }

            const visibleCheckboxes = getVisibleCheckboxes();
            const visibleCheckedCount = visibleCheckboxes.filter((input) => input.checked).length;

            selectAllInput.checked = visibleCheckboxes.length > 0 && visibleCheckedCount === visibleCheckboxes.length;
            selectAllInput.indeterminate = visibleCheckedCount > 0 && visibleCheckedCount < visibleCheckboxes.length;
        };

        selectAllInput?.addEventListener('change', () => {
            getVisibleCheckboxes().forEach((checkbox) => {
                checkbox.checked = selectAllInput.checked;
            });

            update();
        });

        rowCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', update);
        });
        bulkEmailOpenButton?.addEventListener('click', openBulkEmailModal);
        bulkEmailCloseButtons.forEach((button) => {
            button.addEventListener('click', closeBulkEmailModal);
        });
        bulkEmailForm?.addEventListener('submit', (event) => {
            if (getCheckedCheckboxes().length === 0) {
                event.preventDefault();
                closeBulkEmailModal();
                return;
            }

            syncBulkEmailIds();
        });
        bulkEmailSubmitButton?.addEventListener('click', () => {
            if (!bulkEmailForm || getCheckedCheckboxes().length === 0) {
                closeBulkEmailModal();
                return;
            }

            syncBulkEmailIds();

            if (typeof bulkEmailForm.requestSubmit === 'function') {
                bulkEmailForm.requestSubmit();
                return;
            }

            bulkEmailForm.submit();
        });

        update();

        return { update };
    };

    document.querySelectorAll('.admin-donut-chart').forEach((chart) => {
        const values = (chart.dataset.values || '')
            .split(',')
            .map((value) => Number.parseFloat(value.trim()))
            .filter((value) => Number.isFinite(value) && value >= 0);
        const colors = (chart.dataset.colors || '')
            .split(',')
            .map((value) => value.trim())
            .filter(Boolean);

        if (!values.length || values.length !== colors.length) {
            return;
        }

        const total = values.reduce((sum, value) => sum + value, 0);

        if (total <= 0) {
            return;
        }

        let currentAngle = 0;

        const gradientStops = values.map((value, index) => {
            const percentage = (value / total) * 100;
            const start = currentAngle;
            const end = currentAngle + percentage;

            currentAngle = end;

            return `${colors[index]} ${start}% ${end}%`;
        });

        chart.style.backgroundImage = `conic-gradient(${gradientStops.join(', ')})`;
    });

    document.querySelectorAll('[data-password-toggle]').forEach((field) => {
        const input = field.querySelector('[data-password-input]');
        const toggleButton = field.querySelector('.admin-password-toggle');
        const showIcon = field.querySelector('[data-password-icon-show]');
        const hideIcon = field.querySelector('[data-password-icon-hide]');

        if (!input || !toggleButton) {
            return;
        }

        toggleButton.addEventListener('click', () => {
            const shouldShow = input.type === 'password';

            input.type = shouldShow ? 'text' : 'password';
            toggleButton.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
            toggleButton.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
            showIcon?.classList.toggle('is-hidden', !shouldShow);
            hideIcon?.classList.toggle('is-hidden', shouldShow);
        });
    });

    document.querySelectorAll('[data-admin-naskah-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-naskah-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-naskah-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-naskah-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-naskah-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-naskah-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-naskah-empty]');
        const countTarget = tableSection.querySelector('[data-admin-naskah-count]');
        const infoTarget = tableSection.querySelector('[data-admin-naskah-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-naskah-pagination]');
        let currentPage = 1;
        let filteredRows = rows;

        if (!rows.length) {
            if (infoTarget) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
            }

            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (countTarget) {
                countTarget.textContent = filteredRows.length.toString();
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminNaskahFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });

        renderRows();
    });

    document.querySelectorAll('[data-admin-penulis-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-penulis-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-penulis-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-penulis-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-penulis-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-penulis-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-penulis-empty]');
        const infoTarget = tableSection.querySelector('[data-admin-penulis-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-penulis-pagination]');
        let currentPage = 1;
        let filteredRows = rows;
        const tableSelection = initAdminTableSelection(tableSection, () => filteredRows.filter((row) => !row.classList.contains('hidden')));

        if (!rows.length) {
            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
            tableSelection.update();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminPenulisFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });
        renderRows();
    });

    document.querySelectorAll('[data-admin-editor-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-editor-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-editor-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-editor-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-editor-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-editor-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-editor-empty]');
        const infoTarget = tableSection.querySelector('[data-admin-editor-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-editor-pagination]');
        let currentPage = 1;
        let filteredRows = rows;
        const tableSelection = initAdminTableSelection(tableSection, () => filteredRows.filter((row) => !row.classList.contains('hidden')));

        if (!rows.length) {
            if (infoTarget) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
            }

            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
            tableSelection.update();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminEditorFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });

        renderRows();
    });

    document.querySelectorAll('[data-admin-layouter-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-layouter-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-layouter-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-layouter-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-layouter-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-layouter-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-layouter-empty]');
        const infoTarget = tableSection.querySelector('[data-admin-layouter-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-layouter-pagination]');
        let currentPage = 1;
        let filteredRows = rows;
        const tableSelection = initAdminTableSelection(tableSection, () => filteredRows.filter((row) => !row.classList.contains('hidden')));

        if (!rows.length) {
            if (infoTarget) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
            }

            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
            tableSelection.update();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminLayouterFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });

        renderRows();
    });

    document.querySelectorAll('[data-admin-jadwal-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-jadwal-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-jadwal-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-jadwal-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-jadwal-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-jadwal-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-jadwal-empty]');
        const infoTarget = tableSection.querySelector('[data-admin-jadwal-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-jadwal-pagination]');
        let currentPage = 1;
        let filteredRows = rows;

        if (!rows.length) {
            if (infoTarget) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
            }

            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminJadwalFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });

        renderRows();
    });

    document.querySelectorAll('[data-admin-riwayat-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-admin-riwayat-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-admin-riwayat-filter]'));
        const resetButton = tableSection.querySelector('[data-admin-riwayat-reset]');
        const pageSizeInput = tableSection.querySelector('[data-admin-riwayat-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-admin-riwayat-row]'));
        const emptyRow = tableSection.querySelector('[data-admin-riwayat-empty]');
        const infoTarget = tableSection.querySelector('[data-admin-riwayat-info]');
        const paginationTarget = tableSection.querySelector('[data-admin-riwayat-pagination]');
        let currentPage = 1;
        let filteredRows = rows;

        if (!rows.length) {
            if (infoTarget) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
            }

            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const getPageSize = () => {
            const value = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(value) && value > 0 ? value : 10;
        };

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            paginationTarget.innerHTML = '';

            const makeButton = (label, page, options = {}) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = Boolean(options.disabled);
                button.classList.toggle('is-active', Boolean(options.active));
                button.addEventListener('click', () => {
                    currentPage = page;
                    renderRows();
                });

                return button;
            };

            paginationTarget.appendChild(makeButton('<', Math.max(1, currentPage - 1), {
                disabled: currentPage === 1,
            }));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(makeButton(page.toString(), page, {
                    active: page === currentPage,
                }));
            }

            paginationTarget.appendChild(makeButton('>', Math.min(totalPages, currentPage + 1), {
                disabled: currentPage === totalPages,
            }));
        };

        const renderRows = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            renderPagination();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.adminRiwayatFilter || ''),
                    value: normalize(input.value),
                }))
                .filter((filter) => filter.value !== '');

            filteredRows = rows.filter((row) => {
                const matchesSearch = !query || normalize(row.dataset.search).includes(query);
                const matchesFilters = activeFilters.every((filter) => normalize(row.dataset[filter.key]) === filter.value);

                return matchesSearch && matchesFilters;
            });

            currentPage = 1;
            renderRows();
        };

        searchInput?.addEventListener('input', applyFilters);
        filterInputs.forEach((input) => input.addEventListener('change', applyFilters));
        pageSizeInput?.addEventListener('change', () => {
            currentPage = 1;
            renderRows();
        });
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            if (pageSizeInput) {
                pageSizeInput.value = '10';
            }

            applyFilters();
        });

        renderRows();
    });

});
