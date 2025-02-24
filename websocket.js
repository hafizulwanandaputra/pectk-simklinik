const WebSocket = require("ws");

const PORT = 8088;
const server = new WebSocket.Server({ host: "0.0.0.0", port: PORT });
const clients = new Set(); // Menyimpan daftar client yang terhubung

server.on("listening", () => {
  console.log(`WebSocket server running on port ${PORT}`);
});

server.on("error", (err) => {
  if (err.code === "EADDRINUSE") {
    console.error(
      `Port ${PORT} sudah digunakan. Pastikan tidak ada server lain yang berjalan di port ini.`
    );
    process.exit(1);
  } else {
    console.error("Error:", err);
  }
});

server.on("connection", (socket) => {
  console.log("Client connected");
  clients.add(socket); // Tambahkan client ke daftar

  socket.on("message", (message) => {
    console.log(`Received: ${message}`);

    // Jika client mengirim "ping", balas dengan "pong"
    if (message === "ping") {
      socket.send("pong");
      return;
    }

    // Broadcast pesan ke semua client
    for (let client of clients) {
      if (client.readyState === WebSocket.OPEN) {
        client.send(`Server received: ${message}`);
      }
    }
  });

  socket.on("close", () => {
    console.log("Client disconnected");
    clients.delete(socket); // Hapus client dari daftar
  });

  // Tangani error pada masing-masing socket
  socket.on("error", (err) => {
    console.error("Socket error:", err);
    clients.delete(socket);
  });
});

console.log(`WebSocket server running on ws://localhost:${PORT}`);
