<?php
session_start();
require_once("connection.php");

// Oturum ID (gerçek sistemde session'dan alınmalı)
$session_id = $_SESSION['session_id'] ?? 1;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Moderatör Paneli</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f0f0f0; }
    .tabs { display: flex; background: #fff; border-bottom: 1px solid #ccc; }
    .tab { padding: 14px 24px; cursor: pointer; border-right: 1px solid #ddd; }
    .tab.active { background: #f47c2c; color: white; font-weight: bold; }
    .tab-content { display: none; padding: 20px; background: #fff; }
    .tab-content.active { display: block; }
    .chart-container { width: 80%; margin: auto; }
    table { margin: 40px auto; border-collapse: collapse; width: 60%; }
    th, td { padding: 10px 15px; border: 1px solid #ccc; text-align: center; }
    th { background-color: #eee; }
    ul#messageList { list-style: none; padding: 0; width: 80%; margin: 20px auto; }
    ul#messageList li { background: #f9f9f9; margin-bottom: 10px; padding: 10px; border-radius: 5px; display: flex; justify-content: space-between; }
    button { background: #f47c2c; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; }
  </style>
</head>
<body>

<!-- SEKME MENÜSÜ -->
<div class="tabs">
  <div class="tab active" onclick="showTab('panic')">Panic</div>
  <div class="tab" onclick="showTab('chatwall')">Chatwall</div>
  <div class="tab" onclick="showTab('quiz')">Quiz</div>
  <div class="tab" onclick="showTab('session')">Session</div>
</div>

<!-- PANIC SEKME İÇERİĞİ -->
<div id="panic" class="tab-content active">
  <h1>Panik Geri Bildirimleri (Oturum: <?= htmlspecialchars($session_id) ?>)</h1>
  <div class="chart-container">
    <canvas id="panicChart"></canvas>
  </div>
  <table id="feedbackTable">
    <tr><th>Geri Bildirim Türü</th><th>Adet</th></tr>
    <tr><td>Çok hızlı</td><td id="too_fast">0</td></tr>
    <tr><td>Çok yavaş</td><td id="too_slow">0</td></tr>
    <tr><td>Çok sessiz</td><td id="too_quiet">0</td></tr>
    <tr><td>Örnek verin</td><td id="example">0</td></tr>
    <tr><td>Son slayt tekrar</td><td id="last_slide">0</td></tr>
    <tr><td>Panik</td><td id="panic">0</td></tr>
  </table>
</div>

<!-- CHATWALL SEKME İÇERİĞİ -->
<div id="chatwall" class="tab-content">
  <h2>Chatwall Mesajları (Oturum: <?= htmlspecialchars($session_id) ?>)</h2>
  <ul id="messageList"></ul>
</div>

<!-- DİĞER SEKME BOŞLUKLARI -->
<div id="quiz" class="tab-content"><p>Quiz gelecektir.</p></div>
<div id="session" class="tab-content"><p>Session yönetimi gelecektir.</p></div>

<!-- SCRIPT -->
<script>
function showTab(tabId) {
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
  document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

// === PANIC CHART ===
const sessionId = <?= json_encode($session_id) ?>;
const chartCtx = document.getElementById('panicChart').getContext('2d');
const panicChart = new Chart(chartCtx, {
  type: 'bar',
  data: {
    labels: ['Çok hızlı', 'Çok yavaş', 'Çok sessiz', 'Örnek verin', 'Son slayt tekrar', 'Panik'],
    datasets: [{
      label: 'Geri Bildirim Sayısı',
      data: [0, 0, 0, 0, 0, 0],
      backgroundColor: '#f47c2c'
    }]
  },
  options: {
    scales: { y: { beginAtZero: true, stepSize: 1 } }
  }
});

function updatePanicData() {
  fetch(`get_feedback_data.php?session_id=${sessionId}`)
    .then(res => res.json())
    .then(data => {
      if (data.error) return;
      const types = ['too_fast', 'too_slow', 'too_quiet', 'example', 'last_slide', 'panic'];
      const chartData = [];

      types.forEach(type => {
        const count = data[type] || 0;
        chartData.push(count);
        document.getElementById(type).textContent = count;
      });

      panicChart.data.datasets[0].data = chartData;
      panicChart.update();
    });
}

// === CHATWALL ===
function getChatMessages() {
  fetch(`getChatMessages.php?session_id=${sessionId}`)
    .then(res => res.json())
    .then(messages => {
      const list = document.getElementById("messageList");
      list.innerHTML = "";

      messages.forEach(msg => {
        const li = document.createElement("li");
        li.innerHTML = `
          <span>${msg.message}</span>
          <button onclick="deleteMessage(${msg.id}, this)">Sil</button>
        `;
        list.appendChild(li);
      });
    });
}

function deleteMessage(id, button) {
  fetch("deleteMessage.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${id}`
  })
  .then(res => res.json())
  .then(result => {
    if (result.success) {
      button.parentElement.innerHTML = "<em>Mesaj silindi</em>";
    }
  });
}

setInterval(() => {
  updatePanicData();
  getChatMessages();
}, 1000);

updatePanicData();
getChatMessages();
</script>

</body>
</html>