FROM ubuntu:22.04
RUN apt update && apt install -y tcpdump netcat
WORKDIR /challenge
COPY challenge.pcap .
COPY README.txt .
CMD ["bash"]