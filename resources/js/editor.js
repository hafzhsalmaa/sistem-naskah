document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.editor-donut-chart').forEach((chart) => {
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

    document.querySelectorAll('[data-editor-masuk-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-editor-masuk-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-editor-masuk-filter]'));
        const resetButton = tableScope.querySelector('[data-editor-masuk-reset]');
        const pageSizeInput = tableScope.querySelector('[data-editor-masuk-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-editor-masuk-row]'));
        const emptyRow = tableScope.querySelector('[data-editor-masuk-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-editor-masuk-server-empty]');
        const infoTarget = tableScope.querySelector('[data-editor-masuk-info]');
        const paginationTarget = tableScope.querySelector('[data-editor-masuk-pagination]');
        const paginationBar = tableScope.querySelector('.editor-naskah-pagination-bar');

        if (!rows.length) {
            if (paginationBar) {
                paginationBar.classList.add('is-hidden');
            }

            return;
        }

        let currentPage = 1;
        let filteredRows = [...rows];

        const normalize = (value) => String(value || '').toLowerCase().trim();

        const getPageSize = () => {
            const size = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(size) && size > 0 ? size : 10;
        };

        const getFilterDatasetKey = (name) => {
            return `filter${name.charAt(0).toUpperCase()}${name.slice(1)}`;
        };

        const applyFilters = () => {
            const keyword = normalize(searchInput?.value);

            filteredRows = rows.filter((row) => {
                const searchMatch = !keyword || normalize(row.dataset.search).includes(keyword);
                const filterMatch = filterInputs.every((input) => {
                    const value = normalize(input.value);

                    if (!value) {
                        return true;
                    }

                    const datasetKey = getFilterDatasetKey(input.dataset.editorMasukFilter || '');

                    return normalize(row.dataset[datasetKey]) === value;
                });

                return searchMatch && filterMatch;
            });
        };

        const renderInfo = (pageSize, totalPages) => {
            if (!infoTarget) {
                return;
            }

            if (!filteredRows.length) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                return;
            }

            const start = (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, filteredRows.length);

            infoTarget.textContent = `Menampilkan ${start}-${end} dari ${filteredRows.length} data`;

            if (totalPages <= 1) {
                infoTarget.textContent += ' (1 halaman)';
            }
        };

        const createPaginationButton = (label, page, isActive = false, isDisabled = false) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.disabled = isDisabled;

            if (isActive) {
                button.classList.add('is-active');
                button.setAttribute('aria-current', 'page');
            }

            button.addEventListener('click', () => {
                if (isDisabled || currentPage === page) {
                    return;
                }

                currentPage = page;
                render();
            });

            return button;
        };

        const renderPagination = (totalPages) => {
            if (!paginationTarget) {
                return;
            }

            paginationTarget.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            paginationTarget.appendChild(createPaginationButton('<', Math.max(1, currentPage - 1), false, currentPage === 1));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(createPaginationButton(String(page), page, page === currentPage));
            }

            paginationTarget.appendChild(createPaginationButton('>', Math.min(totalPages, currentPage + 1), false, currentPage === totalPages));
        };

        const render = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

            currentPage = Math.min(currentPage, totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const visibleRows = new Set(filteredRows.slice(startIndex, startIndex + pageSize));

            rows.forEach((row) => {
                row.classList.toggle('hidden', !visibleRows.has(row));
            });

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (serverEmptyRow) {
                serverEmptyRow.classList.add('hidden');
            }

            renderInfo(pageSize, totalPages);
            renderPagination(totalPages);
        };

        const update = () => {
            currentPage = 1;
            applyFilters();
            render();
        };

        searchInput?.addEventListener('input', update);
        filterInputs.forEach((input) => input.addEventListener('change', update));
        pageSizeInput?.addEventListener('change', update);

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

            update();
        });

        applyFilters();
        render();
    });

    document.querySelectorAll('[data-editor-revisi-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-editor-revisi-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-editor-revisi-filter]'));
        const resetButton = tableScope.querySelector('[data-editor-revisi-reset]');
        const pageSizeInput = tableScope.querySelector('[data-editor-revisi-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-editor-revisi-row]'));
        const emptyRow = tableScope.querySelector('[data-editor-revisi-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-editor-revisi-server-empty]');
        const infoTarget = tableScope.querySelector('[data-editor-revisi-info]');
        const paginationTarget = tableScope.querySelector('[data-editor-revisi-pagination]');
        const paginationBar = tableScope.querySelector('.editor-naskah-pagination-bar');

        if (!rows.length) {
            if (paginationBar) {
                paginationBar.classList.add('is-hidden');
            }

            return;
        }

        let currentPage = 1;
        let filteredRows = [...rows];

        const normalize = (value) => String(value || '').toLowerCase().trim();

        const getPageSize = () => {
            const size = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(size) && size > 0 ? size : 10;
        };

        const getFilterDatasetKey = (name) => {
            return `filter${name.charAt(0).toUpperCase()}${name.slice(1)}`;
        };

        const applyFilters = () => {
            const keyword = normalize(searchInput?.value);

            filteredRows = rows.filter((row) => {
                const searchMatch = !keyword || normalize(row.dataset.search).includes(keyword);
                const filterMatch = filterInputs.every((input) => {
                    const value = normalize(input.value);

                    if (!value) {
                        return true;
                    }

                    const datasetKey = getFilterDatasetKey(input.dataset.editorRevisiFilter || '');

                    return normalize(row.dataset[datasetKey]) === value;
                });

                return searchMatch && filterMatch;
            });
        };

        const renderInfo = (pageSize, totalPages) => {
            if (!infoTarget) {
                return;
            }

            if (!filteredRows.length) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                return;
            }

            const start = (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, filteredRows.length);

            infoTarget.textContent = `Menampilkan ${start}-${end} dari ${filteredRows.length} data`;

            if (totalPages <= 1) {
                infoTarget.textContent += ' (1 halaman)';
            }
        };

        const createPaginationButton = (label, page, isActive = false, isDisabled = false) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.disabled = isDisabled;

            if (isActive) {
                button.classList.add('is-active');
                button.setAttribute('aria-current', 'page');
            }

            button.addEventListener('click', () => {
                if (isDisabled || currentPage === page) {
                    return;
                }

                currentPage = page;
                render();
            });

            return button;
        };

        const renderPagination = (totalPages) => {
            if (!paginationTarget) {
                return;
            }

            paginationTarget.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            paginationTarget.appendChild(createPaginationButton('<', Math.max(1, currentPage - 1), false, currentPage === 1));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(createPaginationButton(String(page), page, page === currentPage));
            }

            paginationTarget.appendChild(createPaginationButton('>', Math.min(totalPages, currentPage + 1), false, currentPage === totalPages));
        };

        const render = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

            currentPage = Math.min(currentPage, totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const visibleRows = new Set(filteredRows.slice(startIndex, startIndex + pageSize));

            rows.forEach((row) => {
                row.classList.toggle('hidden', !visibleRows.has(row));
            });

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (serverEmptyRow) {
                serverEmptyRow.classList.add('hidden');
            }

            renderInfo(pageSize, totalPages);
            renderPagination(totalPages);
        };

        const update = () => {
            currentPage = 1;
            applyFilters();
            render();
        };

        searchInput?.addEventListener('input', update);
        filterInputs.forEach((input) => input.addEventListener('change', update));
        pageSizeInput?.addEventListener('change', update);

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

            update();
        });

        applyFilters();
        render();
    });

    document.querySelectorAll('[data-editor-riwayat-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-editor-riwayat-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-editor-riwayat-filter]'));
        const resetButton = tableScope.querySelector('[data-editor-riwayat-reset]');
        const pageSizeInput = tableScope.querySelector('[data-editor-riwayat-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-editor-riwayat-row]'));
        const emptyRow = tableScope.querySelector('[data-editor-riwayat-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-editor-riwayat-server-empty]');
        const infoTarget = tableScope.querySelector('[data-editor-riwayat-info]');
        const paginationTarget = tableScope.querySelector('[data-editor-riwayat-pagination]');
        const paginationBar = tableScope.querySelector('.editor-naskah-pagination-bar');

        if (!rows.length) {
            if (paginationBar) {
                paginationBar.classList.add('is-hidden');
            }

            return;
        }

        let currentPage = 1;
        let filteredRows = [...rows];

        const normalize = (value) => String(value || '').toLowerCase().trim();

        const getPageSize = () => {
            const size = Number.parseInt(pageSizeInput?.value || '10', 10);

            return Number.isFinite(size) && size > 0 ? size : 10;
        };

        const getFilterDatasetKey = (name) => {
            return `filter${name.charAt(0).toUpperCase()}${name.slice(1)}`;
        };

        const applyFilters = () => {
            const keyword = normalize(searchInput?.value);

            filteredRows = rows.filter((row) => {
                const searchMatch = !keyword || normalize(row.dataset.search).includes(keyword);
                const filterMatch = filterInputs.every((input) => {
                    const value = normalize(input.value);

                    if (!value) {
                        return true;
                    }

                    const datasetKey = getFilterDatasetKey(input.dataset.editorRiwayatFilter || '');

                    return normalize(row.dataset[datasetKey]) === value;
                });

                return searchMatch && filterMatch;
            });
        };

        const renderInfo = (pageSize, totalPages) => {
            if (!infoTarget) {
                return;
            }

            if (!filteredRows.length) {
                infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                return;
            }

            const start = (currentPage - 1) * pageSize + 1;
            const end = Math.min(currentPage * pageSize, filteredRows.length);

            infoTarget.textContent = `Menampilkan ${start}-${end} dari ${filteredRows.length} data`;

            if (totalPages <= 1) {
                infoTarget.textContent += ' (1 halaman)';
            }
        };

        const createPaginationButton = (label, page, isActive = false, isDisabled = false) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = label;
            button.disabled = isDisabled;

            if (isActive) {
                button.classList.add('is-active');
                button.setAttribute('aria-current', 'page');
            }

            button.addEventListener('click', () => {
                if (isDisabled || currentPage === page) {
                    return;
                }

                currentPage = page;
                render();
            });

            return button;
        };

        const renderPagination = (totalPages) => {
            if (!paginationTarget) {
                return;
            }

            paginationTarget.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            paginationTarget.appendChild(createPaginationButton('<', Math.max(1, currentPage - 1), false, currentPage === 1));

            for (let page = 1; page <= totalPages; page += 1) {
                paginationTarget.appendChild(createPaginationButton(String(page), page, page === currentPage));
            }

            paginationTarget.appendChild(createPaginationButton('>', Math.min(totalPages, currentPage + 1), false, currentPage === totalPages));
        };

        const render = () => {
            const pageSize = getPageSize();
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

            currentPage = Math.min(currentPage, totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const visibleRows = new Set(filteredRows.slice(startIndex, startIndex + pageSize));

            rows.forEach((row) => {
                row.classList.toggle('hidden', !visibleRows.has(row));
            });

            if (emptyRow) {
                emptyRow.classList.toggle('hidden', filteredRows.length > 0);
            }

            if (serverEmptyRow) {
                serverEmptyRow.classList.add('hidden');
            }

            renderInfo(pageSize, totalPages);
            renderPagination(totalPages);
        };

        const update = () => {
            currentPage = 1;
            applyFilters();
            render();
        };

        searchInput?.addEventListener('input', update);
        filterInputs.forEach((input) => input.addEventListener('change', update));
        pageSizeInput?.addEventListener('change', update);

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

            update();
        });

        applyFilters();
        render();
    });
});
