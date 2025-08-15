const WebSocket = require("ws");
const express = require("express");

const WS_PORT = 8090; // WebSocket di port 8090
const HTTP_PORT = 3000; // HTTP untuk menerima perintah refresh
const PING_INTERVAL = 30000; // Kirim ping setiap 30 detik

// --------------------------
// Buat server WebSocket
// --------------------------
const wss = new WebSocket.Server({ host: "127.0.0.1", port: WS_PORT });
const clients = new Set(); // Menyimpan daftar klien yang terhubung

wss.on("listening", () => {
  console.log(`✅ WebSocket server running on ws://127.0.0.1:${WS_PORT}`);
});

wss.on("error", (err) => {
  console.error("❌ WebSocket Error:", err);
});

wss.on("connection", (socket) => {
  console.log("🔗 Client connected");
  clients.add(socket);

  socket.on("message", (message) => {
    console.log(`📩 Received: ${message}`);

    if (message === "ping") {
      socket.send("pong");
      return;
    }

    // Broadcast ulang pesan ke semua klien
    broadcast({ update: false, message: message });
  });

  socket.on("close", () => {
    console.log("❌ Client disconnected");
    clients.delete(socket);
  });

  socket.on("error", (err) => {
    console.error("⚠️ Socket error:", err);
    clients.delete(socket);
  });
});

// --------------------------
// Fungsi untuk mengirim pesan ke semua klien
// --------------------------
function broadcast(message) {
  for (let client of clients) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify(message));
    }
  }
}

// --------------------------
// Ping otomatis untuk menjaga koneksi tetap hidup
// --------------------------
setInterval(() => {
  for (let client of clients) {
    if (client.readyState === WebSocket.OPEN) {
      client.ping(); // Kirim ping ke klien
    }
  }
}, PING_INTERVAL);

// --------------------------
// Server HTTP untuk mengirim notifikasi
// --------------------------
const app = express();
app.use(express.json());

// Endpoint untuk memicu update atau hapus data di klien
app.post("/notify", (req, res) => {
  const { action = "update", data = null } = req.body;

  console.log(`📢 Received ${action} request`);
  console.log("📦 Data:", data);

  if (action === "update") {
    broadcast({ update: true });
  } else if (action === "update_resep") {
    broadcast({ update_resep: true });
  } else if (action === "update_transaksi") {
    broadcast({ update_transaksi: true });
  } else if (action === "delete") {
    broadcast({ delete: true });
  } else if (action === "panggil_antrean") {
    broadcast({ panggil_antrean: true, data: data });
  } else {
    return res.status(400).json({ status: "Invalid action" });
  }

  res.json({ status: `${action} triggered` });
});

// Jalankan server HTTP
app.listen(HTTP_PORT, "127.0.0.1", () => {
  console.log(`✅ HTTP server running on http://127.0.0.1:${HTTP_PORT}`);
});
