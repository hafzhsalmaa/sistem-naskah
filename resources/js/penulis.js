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

    document.querySelectorAll('[data-jenjang-select]').forEach((jenjangSelect) => {
        const form = jenjangSelect.closest('form');
        const kelasSelect = form?.querySelector('[data-kelas-select]');

        if (!kelasSelect) {
            return;
        }

        const kelasByJenjang = {
            SD: ['1', '2', '3', '4', '5', '6'],
            MI: ['1', '2', '3', '4', '5', '6'],
            SMP: ['7', '8', '9'],
            MTS: ['7', '8', '9'],
            SMA: ['10', '11', '12'],
            MA: ['10', '11', '12'],
            SMK: ['10', '11', '12'],
        };

        const appendOption = (value, label) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            kelasSelect.appendChild(option);

            return option;
        };

        const renderKelasOptions = () => {
            const jenjang = jenjangSelect.value;
            const kelasOptions = kelasByJenjang[jenjang] || [];
            const selectedKelas = kelasSelect.dataset.selectedKelas || '';

            kelasSelect.innerHTML = '';

            if (!kelasOptions.length) {
                appendOption('', 'Pilih jenjang naskah terlebih dahulu');
                kelasSelect.disabled = true;
                return;
            }

            appendOption('', 'Pilih kelas');
            kelasOptions.forEach((kelas) => {
                const option = appendOption(kelas, kelas);

                if (kelas === selectedKelas) {
                    option.selected = true;
                }
            });

            kelasSelect.disabled = false;
        };

        jenjangSelect.addEventListener('change', () => {
            kelasSelect.dataset.selectedKelas = '';
            renderKelasOptions();
        });

        renderKelasOptions();
    });

    document.querySelectorAll('.penulis-donut-chart').forEach((chart) => {
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

    document.querySelectorAll('[data-penulis-naskah-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-penulis-naskah-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-penulis-naskah-filter]'));
        const resetButton = tableSection.querySelector('[data-penulis-naskah-reset]');
        const rows = Array.from(tableSection.querySelectorAll('[data-penulis-naskah-row]'));
        const emptyRow = tableSection.querySelector('[data-penulis-naskah-empty]');
        const paginationBar = tableSection.querySelector('[data-penulis-naskah-pagination-bar]');
        const paginationTarget = tableSection.querySelector('[data-penulis-naskah-pagination]');
        const infoTarget = tableSection.querySelector('[data-penulis-naskah-info]');
        const pageSize = 5;
        let currentPage = 1;
        let filteredRows = rows;

        if (!rows.length) {
            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();
        const datasetKeyFor = (filterName) => `filter${filterName.charAt(0).toUpperCase()}${filterName.slice(1)}`;

        const renderPagination = () => {
            if (!paginationTarget) {
                return;
            }

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
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);

            const startIndex = (currentPage - 1) * pageSize;
            const endIndex = startIndex + pageSize;
            const visibleRows = filteredRows.slice(startIndex, endIndex);

            rows.forEach((row) => row.classList.add('hidden'));
            visibleRows.forEach((row) => row.classList.remove('hidden'));

            emptyRow?.classList.toggle('hidden', filteredRows.length > 0);

            if (infoTarget) {
                if (!filteredRows.length) {
                    infoTarget.textContent = 'Menampilkan 0 dari 0 data';
                } else {
                    infoTarget.textContent = `Menampilkan ${startIndex + 1}-${Math.min(endIndex, filteredRows.length)} dari ${filteredRows.length} data`;
                }
            }

            paginationBar?.classList.toggle('is-hidden', filteredRows.length <= pageSize);
            renderPagination();
        };

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            const activeFilters = filterInputs
                .map((input) => ({
                    key: datasetKeyFor(input.dataset.penulisNaskahFilter || ''),
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
        resetButton?.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
            }

            filterInputs.forEach((input) => {
                input.value = '';
            });

            applyFilters();
        });

        renderRows();
    });

    document.querySelectorAll('[data-penulis-riwayat-table]').forEach((tableSection) => {
        const searchInput = tableSection.querySelector('[data-penulis-riwayat-search]');
        const filterInputs = Array.from(tableSection.querySelectorAll('[data-penulis-riwayat-filter]'));
        const resetButton = tableSection.querySelector('[data-penulis-riwayat-reset]');
        const pageSizeInput = tableSection.querySelector('[data-penulis-riwayat-page-size]');
        const rows = Array.from(tableSection.querySelectorAll('[data-penulis-riwayat-row]'));
        const emptyRow = tableSection.querySelector('[data-penulis-riwayat-empty]');
        const infoTarget = tableSection.querySelector('[data-penulis-riwayat-info]');
        const paginationTarget = tableSection.querySelector('[data-penulis-riwayat-pagination]');
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

            emptyRow?.classList.toggle('hidden', filteredRows.length > 0);

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
                    key: datasetKeyFor(input.dataset.penulisRiwayatFilter || ''),
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
