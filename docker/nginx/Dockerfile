FROM nginx:stable

ADD nginx.conf /etc/nginx/
ADD mpt.conf /etc/nginx/sites-available/

RUN usermod -u 1000 www-data

CMD ["nginx"]

EXPOSE 80 443
