# # from flask import Flask, request, render_template_string

# # app = Flask(__name__)

# # # ===== DAFTAR FLAG & SCORE =====
# # VALID_FLAGS = {
# #     "CTF{basic_sql_injection}": 100,
# #     "CTF{stored_xss}": 150,
# #     "CTF{pcap_analysis_easy}": 150,
# #     "CTF{crypto_caesar_base64}": 100,
# #     "CTF{simple_reverse_elf}": 200
# # }

# # scores = {}
# # submitted_flags = set()

# # HTML = """
# # <!DOCTYPE html>
# # <html>
# # <head>
# #     <title>Cyber UAS CTF Scoreboard</title>
# #     <style>
# #         body { font-family: Arial; margin: 40px; }
# #         table { border-collapse: collapse; width: 50%; }
# #         th, td { border: 1px solid #333; padding: 8px; }
# #         th { background: #eee; }
# #     </style>
# # </head>
# # <body>

# # <h2>Cyber UAS CTF</h2>

# # <h3>Submit Flag</h3>
# # <form method="POST">
# #     Nama Tim:<br>
# #     <input name="team" required><br><br>

# #     Flag:<br>
# #     <input name="flag" size="40" required><br><br>

# #     <button type="submit">Submit</button>
# # </form>

# # <p><b>{{ message }}</b></p>

# # <h3>Scoreboard</h3>
# # <table>
# # <tr>
# #     <th>Tim</th>
# #     <th>Score</th>
# # </tr>
# # {% for team, score in scores %}
# # <tr>
# #     <td>{{ team }}</td>
# #     <td>{{ score }}</td>
# # </tr>
# # {% endfor %}
# # </table>

# # </body>
# # </html>
# # """

# # @app.route("/", methods=["GET", "POST"])
# # def index():
# #     message = ""

# #     if request.method == "POST":
# #         team = request.form["team"]
# #         flag = request.form["flag"]

# #         if flag in submitted_flags:
# #             message = "Flag sudah pernah dikirim"
# #         elif flag in VALID_FLAGS:
# #             scores[team] = scores.get(team, 0) + VALID_FLAGS[flag]
# #             submitted_flags.add(flag)
# #             message = "Selamat Flag BENAR!"
# #         else:
# #             message = "Mohon Maaf Flag SALAH"

# #     sorted_scores = sorted(scores.items(), key=lambda x: x[1], reverse=True)
# #     return render_template_string(HTML, scores=sorted_scores, message=message)

# # # ===== RUN =====
# # app.run(host="0.0.0.0", port=5000)

# from flask import Flask, request, render_template_string, redirect
# import pymysql
# import datetime
# import hashlib

# app = Flask(__name__)

# # ================= KONFIG DOSEN =================
# DOSEN_PASSWORD = "admin123"
# CTF_END = datetime.datetime(2026, 5, 30, 12, 0, 0) 

# # ================= FLAG & SKOR =================
# BASE_FLAGS = {
#     "SQLI": ("CTF{basic_sql_injection}", 100),
#     "XSS": ("CTF{stored_xss}", 150),
#     "PCAP": ("CTF{pcap_analysis_easy}", 150),
#     "CRYPTO": ("CTF{crypto_caesar_base64}", 100),
#     "REVERSE": ("CTF{simple_reverse_elf}", 200)
# }

# db = pymysql.connect(
#     host="db",         
#     user="root",
#     password="root",
#     database="cyber",
#     port=3306,
#     autocommit=True
# )

# def gen_flag(nim, base_flag):
#     return hashlib.md5((nim + base_flag).encode()).hexdigest()

# def calc_grade(score):
#     if score >= 600: return "A"
#     if score >= 450: return "B"
#     if score >= 300: return "C"
#     return "D"

# HTML_LOGIN = """
# <h2>Login Mahasiswa</h2>
# <form method="POST">
# NIM:<br>
# <input name="nim"><br><br>
# <button>Login</button>
# </form>
# {{ msg }}
# """

# HTML_MAIN = """
# <h2>CTF UAS – {{ nim }}</h2>

# <form method="POST">
# Challenge:<br>
# <input name="chal"><br>
# Flag:<br>
# <input name="flag" size="40"><br><br>
# <button>Submit</button>
# </form>

# <p>{{ msg }}</p>

# <h3>Score: {{ score }}</h3>
# <h3>Grade: {{ grade }}</h3>

# <a href="/logout">Logout</a>
# """

# HTML_DOSEN = """
# <h2>Dashboard Dosen</h2>
# <table border=1>
# <tr><th>NIM</th><th>Score</th><th>Grade</th></tr>
# {% for r in rows %}
# <tr><td>{{r[0]}}</td><td>{{r[1]}}</td><td>{{r[2]}}</td></tr>
# {% endfor %}
# </table>

# <form method="POST">
# Password:
# <input type="password" name="password">
# <button>RESET</button>
# </form>
# """

# sessions = {}

# @app.route("/", methods=["GET","POST"])
# def login():
#     msg = ""
#     if request.method == "POST":
#         nim = request.form["nim"]
#         cur = db.cursor()
#         cur.execute("SELECT * FROM students WHERE nim=%s", (nim,))
#         if cur.fetchone():
#             sessions[nim] = True
#             return redirect("/ctf?nim="+nim)
#         msg = "NIM tidak terdaftar"
#     return render_template_string(HTML_LOGIN, msg=msg)

# @app.route("/ctf", methods=["GET","POST"])
# def ctf():
#     nim = request.args.get("nim")
#     if nim not in sessions:
#         return redirect("/")

#     msg = ""
#     now = datetime.datetime.now()
#     if now > CTF_END:
#         msg = " Waktu ujian habis"

#     cur = db.cursor()
#     cur.execute("SELECT score FROM scores WHERE nim=%s", (nim,))
#     row = cur.fetchone()
#     score = row[0] if row else 0
#     grade = calc_grade(score)

#     if request.method == "POST" and now <= CTF_END:
#         chal = request.form["chal"].upper()
#         flag = request.form["flag"]

#         if chal in BASE_FLAGS:
#             base, point = BASE_FLAGS[chal]
#             if flag == gen_flag(nim, base):
#                 cur.execute(
#                     "INSERT INTO submissions(nim,challenge,time,score) VALUES(%s,%s,NOW(),%s)",
#                     (nim, chal, point)
#                 )
#                 cur.execute(
#                     "INSERT INTO scores VALUES(%s,%s,%s) ON DUPLICATE KEY UPDATE score=score+%s",
#                     (nim, point, calc_grade(score+point), point)
#                 )
#                 db.commit()
#                 msg = " Flag benar"
#             else:
#                 msg = " Flag salah"

#     return render_template_string(HTML_MAIN, nim=nim, score=score, grade=grade, msg=msg)

# @app.route("/dosen", methods=["GET","POST"])
# def dosen():
#     cur = db.cursor()
#     cur.execute("SELECT * FROM scores")
#     rows = cur.fetchall()

#     if request.method == "POST":
#         if request.form["password"] == DOSEN_PASSWORD:
#             cur.execute("DELETE FROM scores")
#             cur.execute("DELETE FROM submissions")
#             db.commit()
#             return "RESET OK"
#         return "Password salah"

#     return render_template_string(HTML_DOSEN, rows=rows)

# @app.route("/logout")
# def logout():
#     sessions.clear()
#     return redirect("/")

# app.run(host="0.0.0.0", port=5000)