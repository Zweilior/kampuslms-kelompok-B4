<footer class="eduspace-footer" aria-label="Informasi dan kontak EduSpace">
    <div class="eduspace-footer__main">
        <section class="eduspace-footer__about" aria-labelledby="eduspace-footer-brand">
            <a class="eduspace-footer__brand" id="eduspace-footer-brand" href="{{ route('tentang') }}">
                <span class="material-symbols-outlined" aria-hidden="true">school</span>
                <span>EduSpace</span>
            </a>
            <p>
                Ruang belajar digital yang membantu mahasiswa dan dosen mengelola kegiatan
                akademik dengan lebih mudah, teratur, dan terhubung.
            </p>
        </section>

        <section class="eduspace-footer__address" aria-labelledby="eduspace-footer-address">
            <div class="eduspace-footer__section-heading">
                <span class="material-symbols-outlined" aria-hidden="true">location_on</span>
                <h2 id="eduspace-footer-address">Alamat</h2>
            </div>
            <p class="eduspace-footer__address-copy">
                Jl. Soekarno Hatta KM 15, Karang Joang, Balikpapan Utara, Kalimantan Timur
            </p>
        </section>

        <section class="eduspace-footer__contact" aria-labelledby="eduspace-footer-contact-title">
            <div class="eduspace-footer__section-heading">
                <span class="material-symbols-outlined" aria-hidden="true">contact_page</span>
                <h2 id="eduspace-footer-contact-title">Kontak Admin</h2>
            </div>
            <a class="eduspace-footer__contact-method" href="mailto:admin@kampus.lms.test">
                <span class="material-symbols-outlined" aria-hidden="true">mail</span>
                <span>admin@kampus.lms.test</span>
            </a>
            <div class="eduspace-footer__contact-method" aria-label="WhatsApp: 08xx-xxxx-xxxx">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.5 11.7a8.5 8.5 0 0 1-12.6 7.4L3 20.3l1.2-4.7a8.5 8.5 0 1 1 16.3-3.9Z" />
                    <path d="M8.2 7.8c.2-.4.4-.4.7-.4h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2.1.4-.1.6l-.5.6c-.2.2-.2.3 0 .6.4.7 1 1.3 1.7 1.7.3.2.4.2.6 0l.7-.8c.2-.2.4-.2.6-.1l1.6.8c.2.1.4.2.4.4 0 .3-.2 1.1-.7 1.5-.5.5-1.2.7-1.9.5-.8-.2-2-.7-3.3-1.8-1-.9-1.8-2-2-2.9-.2-.8 0-1.6.5-2.3.1-.2.3-.4.5-.5Z" />
                </svg>
                <span>081231945678</span>
            </div>
        </section>
    </div>

    <div class="eduspace-footer__bottom">
        <span>© {{ date('Y') }} EduSpace</span>
        <span>Institut Teknologi Kalimantan</span>
    </div>
</footer>
