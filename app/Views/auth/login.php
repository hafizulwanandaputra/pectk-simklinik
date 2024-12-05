<?= $this->extend('auth/templates/login'); ?>
<?= $this->section('content'); ?>
<main class="form-signin w-100 m-auto">
    <div class="modal d-block" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body-tertiary rounded-4 shadow-lg transparent-blur">
                <div class="modal-body">
                    <?= form_open('check-login', 'id="loginForm"'); ?>
                    <img class="mb-2" src="<?= base_url('/assets/images/logo_pec.png'); ?>" width="96px">
                    <h1 class="h3 mb-2 fw-bold">Kasir dan Farmasi</h1>
                    <h6>Klinik Utama Mata Padang Eye Center Teluk Kuantan</h6>
                    <div class="form-floating">
                        <input type="text" class="form-control username <?= (validation_show_error('username')) ? 'is-invalid' : ''; ?>" id="floatingInput" name="username" placeholder="Username" value="" autocomplete="off" list="username">
                        <datalist id="username">
                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['username'] ?>">
                                <?php endforeach; ?>
                        </datalist>
                        <label for="floatingInput">
                            <div class="d-flex align-items-start">
                                <div style="width: 12px; text-align: center;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="w-100 ms-3">
                                    Nama Pengguna
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control <?= (validation_show_error('password')) ? 'is-invalid' : ''; ?>" id="floatingPassword" name="password" placeholder="Password" autocomplete="off" data-bs-toggle="popover"
                            data-bs-placement="top"
                            data-bs-trigger="manual"
                            data-bs-title="<em>CAPS LOCK</em> AKTIF"
                            data-bs-content="Harap periksa status <em>Caps Lock</em> pada papan tombol (<em>keyboard</em>) Anda.">
                        <label for="floatingPassword">
                            <div class="d-flex align-items-start">
                                <div style="width: 12px; text-align: center;">
                                    <i class="fa-solid fa-key"></i>
                                </div>
                                <div class="w-100 ms-3">
                                    Kata Sandi
                                </div>
                            </div>
                        </label>
                    </div>
                    <input type="hidden" name="url" value="<?= (isset($_GET['redirect'])) ? base_url('/' . urldecode($_GET['redirect'])) : base_url('/home'); ?>">
                    <button id="loginBtn" class="w-100 btn btn-lg btn-primary rounded bg-gradient" type="submit">
                        <i class="fa-solid fa-right-to-bracket"></i> MASUK
                    </button>
                    <?= form_close(); ?>
                </div>
                <p>Lupa kata sandi? Silakan hubungi admin sistem.</p>
                <!-- FOOTER -->
                <div class="modal-footer d-block" style="font-size: 0.75em; border-top: 1px solid var(--bs-border-color-translucent);">
                    <span class="text-center">&copy; 2024 <?= (date('Y') !== "2024") ? "- " . date('Y') : ''; ?> Klinik Utama Mata Padang Eye Center Teluk Kuantan</span>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3">
    <?php if (session()->getFlashdata('msg')) : ?>
        <div id="msgToast" class="toast align-items-center text-bg-success border border-success transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-start">
                <div style="width: 24px; text-align: center;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="w-100 mx-2 text-start">
                    <?= session()->getFlashdata('msg'); ?>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['redirect'])) : ?>
        <div id="redirectToast" class="toast align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-start">
                <div style="width: 24px; text-align: center;">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="w-100 mx-2 text-start">
                    Silakan masuk sebelum mengunjungi "<?= urldecode($_GET['redirect']); ?>"
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div id="errorToast" class="toast align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-start">
                <div style="width: 24px; text-align: center;">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="w-100 mx-2 text-start">
                    <?= session()->getFlashdata('error'); ?>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (validation_show_error('username') || validation_show_error('password')) : ?>
        <div id="validationToast" class="toast align-items-center text-bg-danger border border-danger transparent-blur" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body d-flex align-items-start">
                <div style="width: 24px; text-align: center;">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="w-100 mx-2 text-start">
                    Gagal masuk:<br><?= validation_show_error('username') ?><br><?= validation_show_error('password') ?>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection(); ?>