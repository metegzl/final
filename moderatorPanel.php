<?php
session_start();
require_once("connection.php");

$session_id = $_SESSION['session_id'] ?? 1;

if (isset($_GET['api']) && $_GET['api'] === 'users') {
  $result = $conn->query("SELECT uye_id, uye_adi, uye_soyadi, uye_mail, is_admin FROM uyeler ORDER BY uye_id DESC");
  $users = [];

  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }

  header('Content-Type: application/json');
  echo json_encode($users);
  exit;
}

if (isset($_POST['delete_user_id'])) {
  $id = intval($_POST['delete_user_id']);
  $success = $conn->query("DELETE FROM uyeler WHERE uye_id = $id");
  echo json_encode(['success' => $success]);
  exit;
}

if (isset($_POST['update_user'])) {
  $data = json_decode($_POST['update_user'], true);
  $id = intval($data['uye_id']);
  $adi = $conn->real_escape_string($data['uye_adi']);
  $soyadi = $conn->real_escape_string($data['uye_soyadi']);
  $mail = $conn->real_escape_string($data['uye_mail']);
  $is_admin = intval($data['is_admin']);

  $success = $conn->query("UPDATE uyeler SET uye_adi='$adi', uye_soyadi='$soyadi', uye_mail='$mail', is_admin=$is_admin WHERE uye_id=$id");
  echo json_encode(['success' => $success]);
  exit;
}
?>
<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <title>Moderatör Paneli</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f0f0f0;
    }

    .tabs-container {
      display: flex;
      align-items: center;
      background: #fff;
      border-bottom: 1px solid #ccc;
    }

    .tabs {
      display: flex;
    }

    .tab {
      padding: 14px 24px;
      cursor: pointer;
      border-right: 1px solid #ddd;
    }

    .tab.active {
      background: #f47c2c;
      color: white;
      font-weight: bold;
    }

    .tab-content {
      display: none;
      padding: 20px;
      background: #fff;
    }

    .tab-content.active {
      display: block;
    }

    table {
      margin: 40px auto;
      border-collapse: collapse;
      width: 60%;
    }

    th,
    td {
      padding: 10px 15px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background-color: #eee;
    }

    ul#messageList {
      list-style: none;
      padding: 0;
      width: 80%;
      margin: 20px auto;
    }

    ul#messageList li {
      background: #f9f9f9;
      margin-bottom: 10px;
      padding: 10px;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
    }

    button {
      background: #f47c2c;
      color: white;
      border: none;
      padding: 6px 12px;
      cursor: pointer;
      border-radius: 4px;
    }

    .saveBtn,
    .cancelBtn {
      display: none;
      margin-left: 3px;
    }

    .edit-mode input,
    .edit-mode select {
      width: 80px;
    }

    .edit-mode [data-field="uye_mail"] input {
      width: 220px !important;
    }

    .header-actions {
      margin-left: auto;
      display: flex;
      align-items: center;
      padding-right: 32px;
    }

    .go-home-btn {
      background: #2e8b57;
      color: #fff;
      border: none;
      padding: 9px 24px;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      margin-left: 18px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .go-home-btn:hover {
      background: #226743;
    }
  </style>
</head>

<body>

  <div class="tabs-container">
    <div class="tabs">
      <div class="tab active" onclick="showTab('chatwall')">Chatwall</div>
      <div class="tab" onclick="showTab('quiz')">Quiz</div>
      <div class="tab" onclick="showTab('users')">Kullanıcılar</div>
    </div>
    <div class="header-actions">
      <button class="go-home-btn" onclick="window.location.href='anaSayfa.php'">Ana Sayfaya Git</button>
    </div>
  </div>

  <div id="chatwall" class="tab-content active">
    <h2>Chatwall Mesajları (Oturum: <?= htmlspecialchars($session_id) ?>)</h2>
    <ul id="messageList"></ul>
  </div>

  <div id="quiz" class="tab-content">
    <p>Quiz gelecektir.</p>
  </div>

  <div id="users" class="tab-content">
    <h2>Kayıtlı Kullanıcılar</h2>
    <table id="usersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Ad</th>
          <th>Soyad</th>
          <th>Email</th>
          <th>Admin?</th>
          <th>İşlem</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script>
    function showTab(tabId) {
      document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
      document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');
      document.getElementById(tabId).classList.add('active');

      if (tabId === "users") {
        loadUsers();
      }
    }

    const sessionId = <?= json_encode($session_id) ?>;

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
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
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
      getChatMessages();
    }, 1000);

    getChatMessages();

    function loadUsers() {
      fetch('?api=users')
        .then(res => res.json())
        .then(users => {
          const tbody = document.querySelector("#usersTable tbody");
          tbody.innerHTML = '';
          users.forEach(user => {
            tbody.innerHTML += `
                            <tr data-id="${user.uye_id}">
                                <td>${user.uye_id}</td>
                                <td><span class="editable" data-field="uye_adi">${user.uye_adi}</span></td>
                                <td><span class="editable" data-field="uye_soyadi">${user.uye_soyadi}</span></td>
                                <td><span class="editable" data-field="uye_mail">${user.uye_mail}</span></td>
                                <td><span class="editable" data-field="is_admin">${user.is_admin == 1 ? "Evet" : "Hayır"}</span></td>
                                <td>
                                    <button onclick="deleteUser(${user.uye_id}, this)">Sil</button>
                                    <button onclick="editUser(this)">Düzenle</button>
                                    <button class="saveBtn" onclick="saveUser(${user.uye_id}, this)" style="display:none;">Kaydet</button>
                                    <button class="cancelBtn" onclick="cancelEdit(this)" style="display:none;">Vazgeç</button>
                                </td>
                            </tr>
                        `;
          });
        });
    }

    function deleteUser(id, btn) {
      if (!confirm("Kullanıcı silinsin mi?")) return;
      fetch("", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: "delete_user_id=" + id
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            btn.closest("tr").remove();
          } else {
            alert("Silinemedi.");
          }
        });
    }

    function editUser(btn) {
      const tr = btn.closest('tr');
      tr.classList.add('edit-mode');

      tr.querySelectorAll('.editable').forEach(span => {
        const field = span.dataset.field;
        let val = span.textContent;

        if (field === "is_admin") {
          span.innerHTML = `
                        <select>
                            <option value="0"${val.trim() == "Hayır" ? " selected" : ""}>Hayır</option>
                            <option value="1"${val.trim() == "Evet" ? " selected" : ""}>Evet</option>
                        </select>
                    `;
        } else {
          span.innerHTML = `<input type="text" value="${val}">`;
        }
      });

      tr.querySelector('[data-field="uye_mail"] input').style.width = "220px";
      tr.querySelectorAll(".saveBtn, .cancelBtn").forEach(b => b.style.display = "inline-block");
      btn.style.display = "none";
    }

    function cancelEdit(btn) {
      loadUsers();
    }

    function saveUser(id, btn) {
      const tr = btn.closest('tr');
      const fields = {};

      tr.querySelectorAll('.editable').forEach(span => {
        const field = span.dataset.field;
        if (field === "is_admin") {
          fields[field] = span.querySelector('select').value;
        } else {
          fields[field] = span.querySelector('input').value;
        }
      });

      fields.uye_id = id;

      fetch("", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: "update_user=" + encodeURIComponent(JSON.stringify(fields))
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            loadUsers();
          } else {
            alert("Güncellenemedi!");
          }
        });
    }
  </script>
</body>

</html>