const puppeteer = require('puppeteer');
const fs = require('fs');

(async () => {
    const args = process.argv.slice(2);
    if (args.length < 5) {
        console.error('Gunakan: node puppeteer-pdf.js <inputHTML> <outputPDF> <width> <height> <topMargin> <rightMargin> <bottomMargin> <leftMargin>');
        process.exit(1);
    }

    const inputHTML = args[0];
    const outputPDF = args[1];
    const width = args[2];  // Lebar halaman dari argumen
    const height = args[3]; // Tinggi halaman dari argumen
    const topMargin = args[4];    // Margin atas
    const rightMargin = args[5];  // Margin kanan
    const bottomMargin = args[6]; // Margin bawah
    const leftMargin = args[7];   // Margin kiri

    if (!fs.existsSync(inputHTML)) {
        console.error(`File HTML tidak ditemukan: ${inputHTML}`);
        process.exit(1);
    }

    const browser = await puppeteer.launch({ headless: "new" });
    const page = await browser.newPage();

    // Load file HTML ke dalam Puppeteer
    await page.goto(`file://${inputHTML}`, { waitUntil: 'networkidle2' });

    // Buat PDF dari halaman yang dirender dengan ukuran dan margin dinamis
    await page.pdf({
        path: outputPDF,         // Output PDF
        width: width,            // Lebar dinamis dari argumen
        height: height,          // Tinggi dinamis dari argumen
        printBackground: true,   // Mencetak latar belakang
        margin: {                // Margin dinamis dari argumen
            top: topMargin,      // Margin atas
            right: rightMargin,  // Margin kanan
            bottom: bottomMargin,// Margin bawah
            left: leftMargin     // Margin kiri
        }
    });

    await browser.close();
})();
