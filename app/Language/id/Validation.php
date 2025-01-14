<?php

// app/Language/id/Validation.php
return [
    // Validasi umum
    'required'      => 'Kolom {field} harus diisi.',
    'matches'       => 'Kolom {field} tidak cocok dengan kolom {param}.',
    'min_length'    => 'Kolom {field} harus memiliki minimal {param} karakter.',
    'max_length'    => 'Kolom {field} tidak boleh lebih dari {param} karakter.',
    'exact_length'  => 'Kolom {field} harus memiliki tepat {param} karakter.',
    'numeric'       => 'Kolom {field} harus berisi angka.',
    'is_numeric'    => 'Kolom {field} harus berisi karakter numerik.',
    'integer'       => 'Kolom {field} harus berisi bilangan bulat.',
    'decimal'       => 'Kolom {field} harus berisi angka desimal.',
    'greater_than'  => 'Kolom {field} harus lebih besar dari {param}.',
    'greater_than_equal_to' => 'Kolom {field} harus lebih besar atau sama dengan {param}.',
    'less_than'     => 'Kolom {field} harus kurang dari {param}.',
    'less_than_equal_to' => 'Kolom {field} harus kurang dari atau sama dengan {param}.',
    'in_list'       => 'Kolom {field} harus salah satu dari: {param}.',
    'valid_email'   => 'Kolom {field} harus berisi alamat email yang valid.',
    'valid_url'     => 'Kolom {field} harus berisi URL yang valid.',
    'valid_ip'      => 'Kolom {field} harus berisi alamat IP yang valid.',
    'alpha'         => 'Kolom {field} hanya boleh berisi karakter alfabet.',
    'alpha_numeric' => 'Kolom {field} hanya boleh berisi karakter alfanumerik.',
    'alpha_numeric_punct' => 'Kolom {field} hanya boleh berisi karakter alfanumerik, spasi, dan karakter ~ ! # $ % & * - _ + = | : .',
    'alpha_numeric_space' => 'Kolom {field} hanya boleh berisi karakter alfanumerik dan spasi.',
    'alpha_dash'    => 'Kolom {field} hanya boleh berisi karakter alfanumerik, garis bawah, dan tanda hubung.',
    'is_unique'     => 'Kolom {field} harus berisi nilai unik.',
    'regex_match'   => 'Kolom {field} tidak sesuai dengan format yang benar.',
    'required_with' => 'Kolom {field} diperlukan jika {param} ada.',
    'required_without' => 'Kolom {field} diperlukan jika {param} tidak ada.',
    'uploaded'      => 'Kolom {field} harus berisi file yang diunggah.',
    'max_size'      => 'Kolom {field} harus berisi file dengan ukuran tidak lebih dari {param}.',
    'is_image'      => 'Kolom {field} harus berisi file gambar yang valid.',
    'mime_in'       => 'Kolom {field} harus berisi file dengan tipe mime: {param}.',
];
