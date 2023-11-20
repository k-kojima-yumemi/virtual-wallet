FROM ubuntu:22.04 as awscli

RUN apt-get update \
    && apt-get install -y git unzip curl ca-certificates --no-install-recommends

WORKDIR /aws

RUN curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip" \
    && unzip awscliv2.zip

FROM bref/php-81-fpm:2 as release

RUN yum install -y groff jq && yum clean all && rm -rf /var/cache/yum

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . /var/task
RUN chmod +x "/var/task/docker/secret_manager_wrapper.sh"
RUN composer install --prefer-dist --optimize-autoloader --no-dev

COPY --from=awscli /aws/aws /var/task/aws
RUN /var/task/aws/install

ENTRYPOINT ["/var/task/docker/secret_manager_wrapper.sh"]
CMD ["public/index.php"]
