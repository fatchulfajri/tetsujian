<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoreboard Modern</title>
    <style>
        /* Menggunakan font yang modern dan bersih */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            margin: 0;
        }

        h2 {
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            font-size: 2rem;
            position: relative;
        }

        /* Memberikan garis bawah estetik pada judul */
        h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 4px;
            background: #3498db;
            margin: 8px auto 0 auto;
            border-radius: 2px;
        }

        /* Styling Kontainer Tabel */
        .table-container {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
            background: white;
            width: 100%;
            max-width: 600px;
        }

        /* Styling Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
            padding: 15px 20px;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid #edf2f7;
            font-size: 1rem;
        }

        /* Efek Zebra (Baris Belang-Belang) */
        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Efek Hover saat baris ditunjuk mouse */
        tr:hover {
            background-color: #f1f5f9;
            transition: background-color 0.2s ease;
        }

        /* Desain khusus untuk baris pertama (Juara 1) */
        tr:first-child td {
            font-weight: bold;
            color: #27ae60;
        }
    </style>
</head>
<body>

<h2>Scoreboard</h2>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Team</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $q = $conn->query("
            SELECT u.team, SUM(s.score) total
            FROM submissions s JOIN users u ON s.user_id=u.id
            GROUP BY u.team ORDER BY total DESC
        ");

        while ($r = $q->fetch_assoc()) {
            $team = htmlspecialchars($r['team']);
            $total = htmlspecialchars($r['total']);
            
            echo "<tr>
                    <td>{$team}</td>
                    <td><strong>{$total}</strong></td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>
</div>

</body>
</html>