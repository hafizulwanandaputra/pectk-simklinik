<style>
    .select2-dropdown {
        box-shadow: var(--bs-box-shadow-sm);
    }

    .select2-container--bootstrap-5 .select2-selection {
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
    }

    .form-select-sm~.select2-container--bootstrap-5 .select2-selection {
        border-radius: var(--bs-border-radius);
    }

    .select2-container--bootstrap-5.select2-container--disabled .select2-selection,
    .select2-container--bootstrap-5.select2-container--disabled.select2-container--focus .select2-selection {
        color: var(--bs-body-color);
        background-color: var(--bs-secondary-bg);
        border-color: var(--bs-border-color);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--selected,
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option[aria-selected=true]:not(.select2-results__option--highlighted) {
        color: var(--bs-white) !important;
        background-color: var(--bs-primary) !important;
    }

    .is-invalid+.select2-container--bootstrap-5 .select2-selection,
    .was-validated select:invalid+.select2-container--bootstrap-5 .select2-selection {
        border-color: var(--bs-form-invalid-border-color);
    }

    .is-invalid+.select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .is-invalid+.select2-container--bootstrap-5.select2-container--open .select2-selection,
    .was-validated select:invalid+.select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .was-validated select:invalid+.select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: var(--bs-form-invalid-border-color);
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-danger-rgb), 0.25);
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        border-color: var(--bs-border-color);
        border-radius: var(--bs-border-radius);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
        color: var(--bs-body-color);
        background-color: var(--bs-body-bg);
        border: var(--bs-border-width) solid var(--bs-border-color);
        border-radius: var(--bs-border-radius);
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option.select2-results__option--highlighted {
        color: var(--bs-body-color);
        background-color: var(--bs-secondary-bg);
    }

    [data-bs-theme=dark] .select2-container--bootstrap-5 .select2-selection--single {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23dee2e6' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: var(--bs-body-color);
    }

    @media (prefers-color-scheme: dark) {
        .select2-container--bootstrap-5 .select2-selection--single {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23dee2e6' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }
    }
</style>