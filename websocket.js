const WebSocket = require("ws");
const express = require("express");

const WS_PORT = 8088; // WebSocket di port 8088
const HTTP_PORT = 3000; // HTTP untuk menerima permintaan di port 3000

// Buat server WebSocket
const wss = new WebSocket.Server({ host: "127.0.0.1", port: WS_PORT });
const clients = new Set(); // Menyimpan daftar client yang terhubung

wss.on("listening", () => {
  console.log(`WebSocket server running on ws://127.0.0.1:${WS_PORT}`);
});

wss.on("error", (err) => {
  if (err.code === "EADDRINUSE") {
    console.error(`Port ${WS_PORT} sudah digunakan.`);
    process.exit(1);
  } else {
    console.error("WebSocket Error:", err);
  }
});

wss.on("connection", (socket) => {
  console.log("Client connected");
  clients.add(socket);

  socket.on("message", (message) => {
    console.log(`Received: ${message}`);

    if (message === "ping") {
      socket.send("pong");
      return;
    }

    for (let client of clients) {
      if (client.readyState === WebSocket.OPEN) {
        client.send(`Server received: ${message}`);
      }
    }
  });

  socket.on("close", () => {
    console.log("Client disconnected");
    clients.delete(socket);
  });

  socket.on("error", (err) => {
    console.error("Socket error:", err);
    clients.delete(socket);
  });
});

// --------------------------
// Server HTTP di port 3000
// --------------------------
const app = express();
app.use(express.json()); // Middleware untuk parsing JSON

app.post("/notify", (req, res) => {
  console.log("Received notification request:", req.body);

  // Kirim pesan ke semua client WebSocket
  for (let client of clients) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(JSON.stringify({ type: "notification", data: req.body }));
    }
  }

  res.json({ status: "Notification sent" });
});

app.listen(HTTP_PORT, "127.0.0.1", () => {
  console.log(`HTTP server running on http://127.0.0.1:${HTTP_PORT}`);
});
