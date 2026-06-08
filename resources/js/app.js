import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((toggleButton) => {
        const field = toggleButton.closest('.password-toggle-field');
        const input = field?.querySelector('[data-password-toggle-input]');
        const hiddenIcon = toggleButton.querySelector('[data-password-toggle-hidden-icon]');
        const visibleIcon = toggleButton.querySelector('[data-password-toggle-visible-icon]');

        if (!input) {
            return;
        }

        toggleButton.addEventListener('click', () => {
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            toggleButton.setAttribute('aria-pressed', (!isVisible).toString());
            toggleButton.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');

            if (hiddenIcon && visibleIcon) {
                hiddenIcon.hidden = !isVisible;
                visibleIcon.hidden = isVisible;
            }

            input.focus();
        });
    });

    document.querySelectorAll('[data-flash-auto-hide]').forEach((flashMessage) => {
        window.setTimeout(() => {
            flashMessage.classList.add('flash-message-is-hiding');

            window.setTimeout(() => {
                flashMessage.remove();
            }, 260);
        }, 3000);
    });

    const confirmModal = document.querySelector('[data-confirm-modal]');

    if (confirmModal) {
        const titleTarget = confirmModal.querySelector('[data-confirm-title]');
        const messageTarget = confirmModal.querySelector('[data-confirm-message]');
        const confirmButton = confirmModal.querySelector('[data-confirm-confirm]');
        const cancelButtons = confirmModal.querySelectorAll('[data-confirm-cancel]');
        let pendingForm = null;
        let infoModalOpen = false;

        const defaultTitle = titleTarget?.textContent?.trim() || 'Konfirmasi Aksi';
        const defaultMessage = messageTarget?.textContent?.trim() || 'Apakah Anda yakin ingin melanjutkan aksi ini?';

        const getConfirmLabel = (form) => {
            if (form.dataset.confirmActionLabel) {
                return form.dataset.confirmActionLabel;
            }

            if (form.classList.contains('js-confirm-delete')) {
                return 'Hapus';
            }

            if (form.classList.contains('js-confirm-logout')) {
                return 'Keluar';
            }

            return 'Ya, Lanjutkan';
        };

        const openConfirmModal = (form) => {
            pendingForm = form;
            infoModalOpen = false;

            if (titleTarget) {
                titleTarget.textContent = form.dataset.confirmTitle || defaultTitle;
            }

            if (messageTarget) {
                messageTarget.textContent = form.dataset.confirmMessage || defaultMessage;
            }

            if (confirmButton) {
                confirmButton.textContent = getConfirmLabel(form);
            }

            confirmModal.hidden = false;
            document.body.classList.add('confirm-modal-open');
            confirmButton?.focus();
        };

        const openInfoModal = (button) => {
            pendingForm = null;
            infoModalOpen = true;

            if (titleTarget) {
                titleTarget.textContent = button.dataset.previewUnavailableTitle || 'Preview tidak tersedia';
            }

            if (messageTarget) {
                messageTarget.textContent = button.dataset.previewUnavailableMessage
                    || 'Preview hanya tersedia untuk file PDF. Silakan download file DOCX/DOC untuk melihat isi dokumen.';
            }

            if (confirmButton) {
                confirmButton.textContent = 'Mengerti';
            }

            confirmModal.hidden = false;
            document.body.classList.add('confirm-modal-open');
            confirmButton?.focus();
        };

        const closeConfirmModal = () => {
            confirmModal.hidden = true;
            document.body.classList.remove('confirm-modal-open');
            pendingForm = null;
            infoModalOpen = false;
        };

        document.querySelectorAll('.js-confirm-delete, .js-confirm-logout').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === 'true') {
                    delete form.dataset.confirmed;
                    return;
                }

                event.preventDefault();
                openConfirmModal(form);
            });
        });

        confirmButton?.addEventListener('click', () => {
            if (!pendingForm) {
                if (infoModalOpen) {
                    closeConfirmModal();
                }

                return;
            }

            const form = pendingForm;
            closeConfirmModal();
            form.dataset.confirmed = 'true';

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });

        cancelButtons.forEach((button) => {
            button.addEventListener('click', closeConfirmModal);
        });

        document.querySelectorAll('[data-preview-unavailable]').forEach((button) => {
            button.addEventListener('click', () => openInfoModal(button));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !confirmModal.hidden) {
                closeConfirmModal();
            }
        });
    }

    document.querySelectorAll('[data-notification-page]').forEach((notificationPage) => {
        const searchInput = notificationPage.querySelector('[data-notification-search]');
        const filterButtons = Array.from(notificationPage.querySelectorAll('[data-notification-filter]'));
        const notificationItems = Array.from(notificationPage.querySelectorAll('[data-notification-item]'));
        const emptyState = notificationPage.querySelector('[data-notification-empty]');
        let activeFilter = 'all';

        if (!notificationItems.length) {
            return;
        }

        const normalize = (value) => (value || '').toString().toLowerCase().trim();

        const applyFilters = () => {
            const query = normalize(searchInput?.value);
            let visibleCount = 0;

            notificationItems.forEach((item) => {
                const matchesSearch = !query || normalize(item.dataset.search).includes(query);
                const readState = item.dataset.read === '1' ? 'read' : 'unread';
                const matchesStatus = activeFilter === 'all' || readState === activeFilter;
                const isVisible = matchesSearch && matchesStatus;

                item.classList.toggle('is-hidden', !isVisible);

                if (isVisible) {
                    visibleCount += 1;
                }
            });

            emptyState?.classList.toggle('is-hidden', visibleCount > 0);
        };

        searchInput?.addEventListener('input', applyFilters);
        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                activeFilter = button.dataset.notificationFilter || 'all';
                filterButtons.forEach((filterButton) => {
                    filterButton.classList.toggle('is-active', filterButton === button);
                });
                applyFilters();
            });
        });
    });
});
