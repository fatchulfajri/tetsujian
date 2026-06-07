FROM ubuntu:22.04
RUN apt update && apt install -y sudo
RUN useradd -m student
RUN echo "student ALL=(ALL) NOPASSWD:/bin/bash" >> /etc/sudoers
USER student
WORKDIR /home/student