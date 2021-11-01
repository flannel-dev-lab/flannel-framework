docker build -t flannel-framework .
docker run -it -p 5001:80 --mount src="$(pwd)",target=/app/,type=bind flannel-framework:latest
