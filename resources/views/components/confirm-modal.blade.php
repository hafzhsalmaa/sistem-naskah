<div class="confirm-modal" data-confirm-modal hidden>
    <div class="confirm-modal__overlay" data-confirm-cancel></div>

    <section
        class="confirm-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-modal-title"
        aria-describedby="confirm-modal-message"
    >
        <div class="confirm-modal__body">
            <div class="confirm-modal__icon" aria-hidden="true">
                !
            </div>

            <div>
                <h2 id="confirm-modal-title" class="confirm-modal__title" data-confirm-title>
                    Konfirmasi Aksi
                </h2>
                <p id="confirm-modal-message" class="confirm-modal__message" data-confirm-message>
                    Apakah Anda yakin ingin melanjutkan aksi ini?
                </p>
            </div>
        </div>

        <div class="confirm-modal__actions">
            <button type="button" class="confirm-modal__button confirm-modal__button--secondary" data-confirm-cancel>
                Batal
            </button>
            <button type="button" class="confirm-modal__button confirm-modal__button--primary" data-confirm-confirm>
                Ya, Lanjutkan
            </button>
        </div>
    </section>
</div>
