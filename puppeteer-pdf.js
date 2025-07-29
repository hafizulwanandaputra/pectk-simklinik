const express = require("express");
const bodyParser = require("body-parser");
const { Cluster } = require("puppeteer-cluster");
const fs = require("fs");
const path = require("path");
const os = require("os");
const { execSync } = require("child_process");

// 🔍 Cari path Chrome/Chromium berdasarkan OS
function findChromePath() {
  const platform = os.platform();

  if (platform === "win32") {
    const candidates = [
      "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe",
      "C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe",
    ];
    for (const chromePath of candidates) {
      if (fs.existsSync(chromePath)) return chromePath;
    }
  }

  if (platform === "darwin") {
    const macPath =
      "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome";
    if (fs.existsSync(macPath)) return macPath;
  }

  if (platform === "linux") {
    try {
      const chrome = execSync(
        "which google-chrome || which chromium || which chromium-browser"
      )
        .toString()
        .trim();
      if (fs.existsSync(chrome)) return chrome;
    } catch {
      return null;
    }
  }

  return null;
}

const chromePath = findChromePath();

if (!chromePath) {
  console.error("❌ Tidak bisa menemukan Google Chrome atau Chromium.");
  process.exit(1);
}

console.log("📦 Menggunakan browser:", chromePath);

// 🚀 Mulai cluster Puppeteer
(async () => {
  const app = express();
  app.use(bodyParser.json({ limit: "10mb" }));

  const cluster = await Cluster.launch({
    concurrency: Cluster.CONCURRENCY_CONTEXT,
    maxConcurrency: 4,
    puppeteerOptions: {
      headless: "new",
      executablePath: chromePath,
      args: ["--no-sandbox", "--disable-setuid-sandbox"],
    },
  });

  // 🎯 Tugas membuat PDF dari HTML
  await cluster.task(async ({ page, data }) => {
    try {
      const { html, outputFilename, paper = {} } = data;

      console.log(`⚙️ Membuat PDF di ${outputFilename}`);
      await page.setContent(html, { waitUntil: "networkidle0" });

      const pdfOptions = {
        path: outputFilename,
        printBackground: true,
      };

      if (paper.format) {
        pdfOptions.format = paper.format;
      } else if (paper.width && paper.height) {
        pdfOptions.width = paper.width;
        pdfOptions.height = paper.height;
      }

      if (typeof paper.landscape === "boolean") {
        pdfOptions.landscape = paper.landscape;
      }

      // Tambahkan margin jika tersedia
      if (paper.margin) {
        pdfOptions.margin = {
          top: paper.margin.top || "0cm",
          right: paper.margin.right || "0cm",
          bottom: paper.margin.bottom || "0cm",
          left: paper.margin.left || "0cm",
        };
      }

      await page.pdf(pdfOptions);
      console.log(`✅ Selesai: ${outputFilename}`);
    } catch (err) {
      console.error("🛑 Gagal membuat PDF:", err);
      throw err;
    }
  });

  // 🌐 Endpoint HTTP untuk menerima HTML dan merespon nama file PDF
  app.post("/generate-pdf", async (req, res) => {
    const { html, filename, paper } = req.body;
    const outputDir = path.join(__dirname, "writable/temp/");
    const outputPath = path.join(outputDir, filename);

    try {
      if (!fs.existsSync(outputDir)) {
        fs.mkdirSync(outputDir, { recursive: true });
      }

      await cluster.execute({ html, outputFilename: outputPath, paper });

      if (!fs.existsSync(outputPath)) {
        return res.status(500).json({
          success: false,
          error: "PDF tidak ditemukan setelah proses selesai.",
          path: outputPath,
        });
      }

      res.json({ success: true, file: filename });
    } catch (err) {
      res.status(500).json({
        success: false,
        error: err.message,
        stack: err.stack,
      });
    }
  });

  app.listen(3001, () => console.log("🚀 PDF Worker listening on port 3001"));
})();
