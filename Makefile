init:
	docker run -w /var/parser -v ${PWD}:/var/parser php:7.4.24-cli-alpine3.14 \
		php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
		php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
		php composer-setup.php && \
		php -r "unlink('composer-setup.php');" && \
		php composer.phar install && \
		rm composer.phar
run:
	docker run --rm -w /var/parser -v ${PWD}:/var/parser php:7.4.24-cli-alpine3.14 php index.php parse:fifa_index
