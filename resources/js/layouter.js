// Layouter-specific frontend behavior.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.layouter-donut-chart').forEach((chart) => {
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

    document.querySelectorAll('[data-layouter-masuk-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-layouter-masuk-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-layouter-masuk-filter]'));
        const resetButton = tableScope.querySelector('[data-layouter-masuk-reset]');
        const pageSizeInput = tableScope.querySelector('[data-layouter-masuk-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-layouter-masuk-row]'));
        const emptyRow = tableScope.querySelector('[data-layouter-masuk-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-layouter-masuk-server-empty]');
        const infoTarget = tableScope.querySelector('[data-layouter-masuk-info]');
        const paginationTarget = tableScope.querySelector('[data-layouter-masuk-pagination]');
        const paginationBar = tableScope.querySelector('.layouter-naskah-pagination-bar');

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

                    const datasetKey = getFilterDatasetKey(input.dataset.layouterMasukFilter || '');

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

    document.querySelectorAll('[data-layouter-selesai-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-layouter-selesai-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-layouter-selesai-filter]'));
        const resetButton = tableScope.querySelector('[data-layouter-selesai-reset]');
        const pageSizeInput = tableScope.querySelector('[data-layouter-selesai-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-layouter-selesai-row]'));
        const emptyRow = tableScope.querySelector('[data-layouter-selesai-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-layouter-selesai-server-empty]');
        const infoTarget = tableScope.querySelector('[data-layouter-selesai-info]');
        const paginationTarget = tableScope.querySelector('[data-layouter-selesai-pagination]');
        const paginationBar = tableScope.querySelector('.layouter-naskah-pagination-bar');

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

                    const datasetKey = getFilterDatasetKey(input.dataset.layouterSelesaiFilter || '');

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

    document.querySelectorAll('[data-layouter-riwayat-table]').forEach((tableScope) => {
        const searchInput = tableScope.querySelector('[data-layouter-riwayat-search]');
        const filterInputs = Array.from(tableScope.querySelectorAll('[data-layouter-riwayat-filter]'));
        const resetButton = tableScope.querySelector('[data-layouter-riwayat-reset]');
        const pageSizeInput = tableScope.querySelector('[data-layouter-riwayat-page-size]');
        const rows = Array.from(tableScope.querySelectorAll('[data-layouter-riwayat-row]'));
        const emptyRow = tableScope.querySelector('[data-layouter-riwayat-empty]');
        const serverEmptyRow = tableScope.querySelector('[data-layouter-riwayat-server-empty]');
        const infoTarget = tableScope.querySelector('[data-layouter-riwayat-info]');
        const paginationTarget = tableScope.querySelector('[data-layouter-riwayat-pagination]');
        const paginationBar = tableScope.querySelector('.layouter-naskah-pagination-bar');

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

                    const datasetKey = getFilterDatasetKey(input.dataset.layouterRiwayatFilter || '');

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
