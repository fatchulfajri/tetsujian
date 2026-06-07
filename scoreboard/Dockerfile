FROM python:3.10
WORKDIR /app
COPY app.py .
RUN pip install flask
RUN pip install flask pymysql
CMD ["python","app.py"]