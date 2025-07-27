FROM php:8.3-cli
COPY ./src /usr/src/grocytomealie
WORKDIR /usr/src/grocytomealie
CMD [ "php", "./index.php" ]
