<style>
    .flatpickr-monthSelect-months {
        margin: 10px 1px 3px 1px;
        flex-wrap: wrap;
    }

    .flatpickr-monthSelect-month {
        background: none;
        border: 1px solid transparent;
        border-radius: 4px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        color: #393939;
        cursor: pointer;
        display: inline-block;
        font-weight: 400;
        margin: 0.5px;
        justify-content: center;
        padding: 10px;
        position: relative;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        text-align: center;
        width: 33%;
    }

    .flatpickr-monthSelect-month.flatpickr-disabled {
        color: #eee;
    }

    .flatpickr-monthSelect-month.flatpickr-disabled:hover,
    .flatpickr-monthSelect-month.flatpickr-disabled:focus {
        cursor: not-allowed;
        background: none !important;
    }

    [data-bs-theme=dark] .flatpickr-current-month input.cur-year {
        color: #fff;
    }

    [data-bs-theme=dark] .flatpickr-months .flatpickr-prev-month,
    [data-bs-theme=dark] .flatpickr-months .flatpickr-next-month {
        color: #fff;
        fill: #fff;
    }

    [data-bs-theme=dark] .flatpickr-monthSelect-month {
        color: rgba(255, 255, 255, 0.95);
    }

    .flatpickr-monthSelect-month.today {
        border-color: #959ea9;
    }

    .flatpickr-monthSelect-month.inRange,
    .flatpickr-monthSelect-month.inRange.today,
    .flatpickr-monthSelect-month:hover,
    .flatpickr-monthSelect-month:focus {
        background: #e6e6e6;
        cursor: pointer;
        outline: 0;
        border-color: #e6e6e6;
    }

    [data-bs-theme=dark] .flatpickr-monthSelect-month.inRange,
    [data-bs-theme=dark] .flatpickr-monthSelect-month:hover,
    [data-bs-theme=dark] .flatpickr-monthSelect-month:focus {
        background: #646c8c;
        border-color: #646c8c;
    }

    .flatpickr-monthSelect-month.today:hover,
    .flatpickr-monthSelect-month.today:focus {
        background: #959ea9;
        border-color: #959ea9;
        color: #fff;
    }

    .flatpickr-monthSelect-month.selected,
    .flatpickr-monthSelect-month.startRange,
    .flatpickr-monthSelect-month.endRange {
        background-color: #569ff7;
        box-shadow: none;
        color: #fff;
        border-color: #569ff7;
    }

    .flatpickr-monthSelect-month.startRange {
        border-radius: 50px 0 0 50px;
    }

    .flatpickr-monthSelect-month.endRange {
        border-radius: 0 50px 50px 0;
    }

    .flatpickr-monthSelect-month.startRange.endRange {
        border-radius: 50px;
    }

    .flatpickr-monthSelect-month.inRange {
        border-radius: 0;
        box-shadow: -5px 0 0 #e6e6e6, 5px 0 0 #e6e6e6;
    }

    [data-bs-theme=dark] .flatpickr-monthSelect-month.selected,
    [data-bs-theme=dark] .flatpickr-monthSelect-month.startRange,
    [data-bs-theme=dark] .flatpickr-monthSelect-month.endRange {
        background: #80cbc4;
        -webkit-box-shadow: none;
        box-shadow: none;
        color: #fff;
        border-color: #80cbc4;
    }
</style>
<style>
    :root {
        font-variant-numeric: proportional-nums;

        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
        --bs-border-radius: 0.5rem;
        --bs-border-radius-sm: 0.5rem;
        --bs-border-radius-lg: 0.75rem;
        --bs-border-radius-xl: 1rem;
        --bs-border-radius-xxl: 2rem;
        --bs-border-radius-2xl: var(--bs-border-radius-xxl);
        --bs-border-radius-pill: 50rem;
    }

    .bg-body-hwpweb {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-tertiary-bg-rgb), var(--bs-bg-opacity)) !important;
    }

    .table-body-hwpweb {
        --bs-table-bg: var(--bs-tertiary-bg);
    }

    iframe {
        color-scheme: light;
    }

    .jawi {
        font-family: "NKL Dubai";
    }

    .form-control::-webkit-file-upload-button {
        background-image: var(--bs-gradient);
    }

    .form-control::file-selector-button {
        background-image: var(--bs-gradient);
    }

    .btn-check:checked.bg+.btn.bg-gradient,
    :not(.btn-check)+.btn:active.bg-gradient,
    .btn:first-child:active.bg-gradient,
    .btn.active.bg-gradient,
    .btn.show.bg-gradient {
        --bs-gradient: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
    }

    .page-link:active.bg-gradient {
        --bs-gradient: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
    }

    .navbar-toggler:active.bg-gradient {
        --bs-gradient: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
    }

    .form-control:active::-webkit-file-upload-button {
        background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
    }

    .form-control:active::file-selector-button {
        background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
    }

    .form-switch .form-check-input:checked {
        background-image: var(--bs-gradient), var(--bs-form-switch-bg);
    }

    .form-switch .form-check-input:active {
        background-image: linear-gradient(180deg,
                rgba(0, 0, 0, 0.25) 0%,
                rgba(0, 0, 0, 0) 10px,
                rgba(255, 255, 255, 0.25) 100%),
            var(--bs-form-switch-bg);
    }

    .form-check-input:checked {
        background-image: var(--bs-gradient), var(--bs-form-check-bg-image);
    }

    .form-check-input:active {
        background-image: linear-gradient(180deg,
                rgba(0, 0, 0, 0.25) 0%,
                rgba(0, 0, 0, 0) 10px,
                rgba(255, 255, 255, 0.25) 100%),
            var(--bs-form-check-bg-image);
    }

    div.dt-buttons {
        display: flex;
    }

    div.dataTables_wrapper div.dataTables_filter {
        text-align: center;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        justify-content: center;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-top: 0;
        font-size: 1rem;
    }

    .nomor {
        font-variant-numeric: tabular-nums;
        -moz-appearance: textfield;
    }

    .nomor::-webkit-outer-spin-button,
    .nomor::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .videoWrapper {
        position: relative;
        padding-bottom: 56.25%;
        /* 16:9 */
        height: 0;
    }

    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .feather {
        width: 16px;
        height: 16px;
        margin-bottom: 2px;
    }

    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 7625%) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }

    .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, 0.1);
        border: solid rgba(0, 0, 0, 0.15);
        border-width: 1px 0;
        box-shadow: inset 0 0.5em 1.5em rgba(0, 0, 0, 0.1),
            inset 0 0.125em 0.5em rgba(0, 0, 0, 0.15);
    }

    .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
    }

    .bi {
        vertical-align: -0.125em;
        fill: currentColor;
    }

    .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
    }

    .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-success:hover,
    .btn-outline-success:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-info:hover,
    .btn-outline-info:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-warning:hover,
    .btn-outline-warning:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-danger:hover,
    .btn-outline-danger:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-light:hover,
    .btn-outline-light:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-outline-dark:hover,
    .btn-outline-dark:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
    }

    .btn-body {
        --bs-btn-color: #000;
        --bs-btn-bg: #f8f9fa;
        --bs-btn-border-color: #dee2e6;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #d3d4d5;
        --bs-btn-hover-border-color: #dee2e6;
        --bs-btn-focus-shadow-rgb: 211, 212, 213;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #c6c7c8;
        --bs-btn-active-border-color: #dee2e6;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #000;
        --bs-btn-disabled-bg: #f8f9fa;
        --bs-btn-disabled-border-color: #dee2e6;
    }

    .btn-outline-body {
        --bs-btn-color: #212529;
        --bs-btn-border-color: #212529;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #212529;
        --bs-btn-hover-border-color: #212529;
        --bs-btn-focus-shadow-rgb: 33, 37, 41;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #212529;
        --bs-btn-active-border-color: #212529;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #212529;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #212529;
        --bs-gradient: none;
    }

    .btn-outline-body:hover,
    .btn-outline-body:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
        ;
    }

    .modal-backdrop {
        --bs-backdrop-opacity: 0.25;
    }

    .offcanvas-backdrop.show {
        opacity: 0.25;
    }

    .fade {
        transition: opacity 0.25s ease-out;
    }

    .modal.fade .modal-dialog {
        transition: transform 0.25s ease-out;
        transform: scale(1.05);
    }

    .modal.fade .modal-dialog.modal-fullscreen {
        transition: transform 0.25s ease-out;
        transform: scale(1);
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    .modal-fullscreen {
        width: 100vw;
        max-width: none;
        height: 100%;
        margin: 0;
    }

    .modal-fullscreen .modal-content {
        height: 100%;
        border: 0;
        border-radius: 0;
    }

    .modal-fullscreen .modal-header,
    .modal-fullscreen .modal-footer {
        border-radius: 0;
    }

    .modal-fullscreen .modal-body {
        overflow-y: auto;
    }

    @media (max-width: 575.98px) {
        .modal.fade .modal-dialog.modal-fullscreen-sm-down {
            transition: transform 0.25s ease-out;
            transform: scale(1);
        }
    }

    @media (max-width: 767.98px) {
        .modal.fade .modal-dialog.modal-fullscreen-md-down {
            transition: transform 0.25s ease-out;
            transform: scale(1);
        }
    }

    @media (max-width: 991.98px) {
        .modal.fade .modal-dialog.modal-fullscreen-lg-down {
            transition: transform 0.25s ease-out;
            transform: scale(1);
        }
    }

    @media (max-width: 1199.98px) {
        .modal.fade .modal-dialog.modal-fullscreen-xl-down {
            transition: transform 0.25s ease-out;
            transform: scale(1);
        }
    }

    @media (max-width: 1399.98px) {
        .modal.fade .modal-dialog.modal-fullscreen-xxl-down {
            transition: transform 0.25s ease-out;
            transform: scale(1);
        }
    }

    .dropdown-menu {
        padding: 0.5rem 0.5rem;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-image: var(--bs-gradient);
        color: var(--bs-dropdown-link-hover-color);
        background-color: var(--bs-dropdown-link-hover-bg);
        box-shadow: inset 0 0 0 1px var(--bs-dropdown-border-color);
        border-radius: var(--bs-border-radius);
    }

    .dropdown-item.active {
        background-image: var(--bs-gradient);
        color: var(--bs-dropdown-link-active-color);
        text-decoration: none;
        background-color: var(--bs-dropdown-link-active-bg);
        box-shadow: inset 0 0 0 1px var(--bs-primary);
        border-radius: var(--bs-border-radius);
    }

    .dropdown-item:active {
        background-image: linear-gradient(180deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0));
        color: var(--bs-dropdown-link-active-color);
        text-decoration: none;
        background-color: var(--bs-dropdown-link-active-bg);
        box-shadow: inset 0 0 0 1px var(--bs-primary);
        border-radius: var(--bs-border-radius);
    }

    .transparent-blur {
        --bs-bg-opacity: 1;
        backdrop-filter: none;
    }

    @media (prefers-reduced-transparency: no-preference) {
        .transparent-blur {
            --bs-bg-opacity: 0.75;
            backdrop-filter: blur(10px);
        }
    }

    .btn-close-black {
        filter: none;
    }

    [data-bs-theme="dark"] .btn-close.btn-close-black {
        filter: none;
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-image: var(--bs-gradient);
    }

    [data-bs-theme=dark] .bg-body-hwpweb {
        --bs-bg-opacity: 1;
        background-color: rgba(var(--bs-body-bg-rgb), var(--bs-bg-opacity)) !important;
    }

    [data-bs-theme=dark] .card {
        --bs-card-bg: var(--bs-tertiary-bg);
    }

    [data-bs-theme=dark] .accordion {
        --bs-accordion-bg: var(--bs-tertiary-bg);
    }

    [data-bs-theme=dark] .accordion-bg-body {
        --bs-accordion-bg: var(--bs-body-bg);
    }

    [data-bs-theme=dark] .table {
        --bs-table-bg: var(--bs-tertiary-bg);
    }

    [data-bs-theme=dark] .table-body-hwpweb {
        --bs-table-bg: var(--bs-body-bg);
    }

    [data-bs-theme=dark] .form-control {
        background-color: #000000;
    }

    [data-bs-theme=dark] .form-control:disabled {
        background-color: var(--bs-secondary-bg);
        opacity: 1;
    }

    [data-bs-theme=dark] .form-select {
        background-color: #000000;
    }

    [data-bs-theme=dark] .form-select:disabled {
        background-color: var(--bs-secondary-bg);
    }

    [data-bs-theme=dark] .form-check-input {
        --bs-form-check-bg: #000000;
    }

    [data-bs-theme=dark] .list-group {
        --bs-list-group-bg: var(--bs-tertiary-bg);
        --bs-list-group-action-hover-bg: var(--bs-secondary-bg);
        --bs-list-group-action-active-bg: #495057;
        --bs-list-group-disabled-bg: var(--bs-tertiary-bg);
    }

    [data-bs-theme=dark] .btn-body {
        --bs-btn-color: #fff;
        --bs-btn-bg: #212529;
        --bs-btn-border-color: #495057;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #424649;
        --bs-btn-hover-border-color: #495057;
        --bs-btn-focus-shadow-rgb: 66, 70, 73;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #4d5154;
        --bs-btn-active-border-color: #495057;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #fff;
        --bs-btn-disabled-bg: #212529;
        --bs-btn-disabled-border-color: #495057;
    }

    [data-bs-theme=dark] .btn-outline-body {
        --bs-btn-color: #f8f9fa;
        --bs-btn-border-color: #f8f9fa;
        --bs-btn-hover-color: #000;
        --bs-btn-hover-bg: #f8f9fa;
        --bs-btn-hover-border-color: #f8f9fa;
        --bs-btn-focus-shadow-rgb: 248, 249, 250;
        --bs-btn-active-color: #000;
        --bs-btn-active-bg: #f8f9fa;
        --bs-btn-active-border-color: #f8f9fa;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #f8f9fa;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #f8f9fa;
        --bs-gradient: none;
    }

    [data-bs-theme=dark] .btn-outline-body:hover,
    [data-bs-theme=dark] .btn-outline-body:focus-visible {
        --bs-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
        ;
    }
</style>