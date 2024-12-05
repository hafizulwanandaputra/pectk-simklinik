<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title; ?></title>
    <link rel="manifest" href="<?= base_url(); ?>/manifest.json">
    <meta name="theme-color" content="#dbf4f1" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#456f6a" media="(prefers-color-scheme: dark)">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="<?= base_url(); ?>favicon.png" rel="icon" />
    <link href="<?= base_url(); ?>favicon.png" rel="apple-touch-icon" />
    <link href="https://getbootstrap.com/docs/5.3/examples/sign-in/sign-in.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/fontawesome/css/all.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/css/main.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/css/JawiDubai.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Color+Emoji&family=Noto+Sans+Arabic:wdth,wght@62.5..100,100..900&family=Noto+Sans+Mono:wdth,wght@62.5..100,100..900&family=Noto+Sans:ital,wdth,wght@0,62.5..100,100..900;1,62.5..100,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.typekit.net/dew1xab.css">
    <link href="<?= base_url(); ?>assets_public/fonts/inter-hwp/inter-hwp.css" rel="stylesheet">
    <link href="<?= base_url(); ?>assets_public/fonts/base-font.css" rel="stylesheet">
    <style>
        .form-signin .username {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('<?= base_url('/assets/images/pec.jpg'); ?>');
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
            background-color: #dbf4f1;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0)), url('<?= base_url('/assets/images/pec-dark.jpg'); ?>');
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
                position: relative;
                background-color: #456f6a;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>

<body class="d-flex align-items-center py-4 text-center" id="background">

    <?= $this->renderSection('content'); ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= base_url(); ?>assets_public/fontawesome/js/all.js"></script>
    <script>
        $(document).ready(function() {
            const passwordInput = $('#floatingPassword');
            const popover = new bootstrap.Popover(passwordInput[0], {
                html: true, // Mengaktifkan dukungan HTML di dalam konten popover
            });

            let capsLockActive = false; // Status Caps Lock sebelumnya

            passwordInput.on('focus', function() {
                $(document).on('keydown', function(event) {
                    const currentCapsLock = event.originalEvent.getModifierState('CapsLock');

                    // Jika status Caps Lock berubah
                    if (currentCapsLock !== capsLockActive) {
                        capsLockActive = currentCapsLock; // Perbarui status

                        if (capsLockActive) {
                            popover.show();
                        } else {
                            popover.hide();
                        }
                    }
                });
            });

            passwordInput.on('blur', function() {
                popover.hide(); // Sembunyikan popover saat kehilangan fokus
                $(document).off('keydown'); // Lepas listener saat blur
                capsLockActive = false; // Reset status
            });

            // Mengecek apakah elemen dengan id 'redirectToast' ada di dalam dokumen
            if ($('#redirectToast').length) {
                var redirectToast = new bootstrap.Toast($('#redirectToast')[0]);
                redirectToast.show(); // Menampilkan toast redirect
            }

            // Mengecek apakah elemen dengan id 'msgToast' ada di dalam dokumen
            if ($('#msgToast').length) {
                var msgToast = new bootstrap.Toast($('#msgToast')[0]);
                msgToast.show(); // Menampilkan toast pesan
            }

            // Mengecek apakah elemen dengan id 'errorToast' ada di dalam dokumen
            if ($('#errorToast').length) {
                var errorToast = new bootstrap.Toast($('#errorToast')[0]);
                errorToast.show(); // Menampilkan toast error
            }

            // Mengecek apakah elemen dengan id 'validationToast' ada di dalam dokumen
            if ($('#validationToast').length) {
                var validationToast = new bootstrap.Toast($('#validationToast')[0]);
                validationToast.show(); // Menampilkan toast validasi
            }

            // Mengatur waktu untuk menyembunyikan toast setelah 5 detik
            setTimeout(function() {
                // Mengecek kembali untuk menyembunyikan setiap toast jika ada
                if ($('#redirectToast').length) {
                    var redirectToast = new bootstrap.Toast($('#redirectToast')[0]);
                    redirectToast.hide(); // Menyembunyikan toast redirect
                }

                if ($('#msgToast').length) {
                    var msgToast = new bootstrap.Toast($('#msgToast')[0]);
                    msgToast.hide(); // Menyembunyikan toast pesan
                }

                if ($('#errorToast').length) {
                    var errorToast = new bootstrap.Toast($('#errorToast')[0]);
                    errorToast.hide(); // Menyembunyikan toast error
                }

                if ($('#validationToast').length) {
                    var validationToast = new bootstrap.Toast($('#validationToast')[0]);
                    validationToast.hide(); // Menyembunyikan toast validasi
                }
            }, 5000); // Durasi waktu untuk menyembunyikan toast (5000 ms)

            // Menghapus kelas 'is-invalid' dan menyembunyikan pesan invalid ketika input diubah
            $('input.form-control').on('input', function() {
                $(this).removeClass('is-invalid'); // Menghapus kelas 'is-invalid'
                $(this).siblings('.invalid-feedback').hide(); // Menyembunyikan pesan feedback invalid
            });

            // Menangani event klik pada tombol login
            $(document).on('click', '#loginBtn', function(e) {
                e.preventDefault(); // Mencegah aksi default tombol
                $('#loginForm').submit(); // Mengirimkan form login
                $('input').prop('disabled', true).removeClass('is-invalid'); // Menonaktifkan semua input dan menghapus kelas 'is-invalid'
                $('#loginBtn').prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            <span role="status">SILAKAN TUNGGU...</span>
        `); // Menampilkan spinner dan teks 'SILAKAN TUNGGU...' pada tombol login
            });
        });
    </script>
    <script>
        /*!
         * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
         * Copyright 2011-2023 The Bootstrap Authors
         * Licensed under the Creative Commons Attribution 3.0 Unported License.
         */

        (() => {
            'use strict'

            const getStoredTheme = () => localStorage.getItem('theme')
            const setStoredTheme = theme => localStorage.setItem('theme', theme)

            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme()
                if (storedTheme) {
                    return storedTheme
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            }

            const setTheme = theme => {
                if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark')
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme)
                }
            }

            setTheme(getPreferredTheme())

            const showActiveTheme = (theme, focus = false) => {
                const themeSwitcher = document.querySelector('#bd-theme')

                if (!themeSwitcher) {
                    return
                }

                const themeSwitcherText = document.querySelector('#bd-theme-text')
                const activeThemeIcon = document.querySelector('.theme-icon-active use')
                const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
                const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')

                document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
                    element.classList.remove('active')
                    element.setAttribute('aria-pressed', 'false')
                })

                btnToActive.classList.add('active')
                btnToActive.setAttribute('aria-pressed', 'true')
                activeThemeIcon.setAttribute('href', svgOfActiveBtn)
                const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
                themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)

                if (focus) {
                    themeSwitcher.focus()
                }
            }

            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                const storedTheme = getStoredTheme()
                if (storedTheme !== 'light' && storedTheme !== 'dark') {
                    setTheme(getPreferredTheme())
                }
            })

            window.addEventListener('DOMContentLoaded', () => {
                showActiveTheme(getPreferredTheme())

                document.querySelectorAll('[data-bs-theme-value]')
                    .forEach(toggle => {
                        toggle.addEventListener('click', () => {
                            const theme = toggle.getAttribute('data-bs-theme-value')
                            setStoredTheme(theme)
                            setTheme(theme)
                            showActiveTheme(theme, true)
                        })
                    })
            })
        })()
    </script>
</body>

</html>