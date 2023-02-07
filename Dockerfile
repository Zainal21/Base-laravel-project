FROM registry.gitlab.com/oxycreative/php:8.1
LABEL maintainer="Muhamad Zainal Arifin <muhamad.arifin@djelas.id>"

COPY . /tmp/appbuild

RUN rsync -ah \
        --exclude /.bash_history \
        --exclude /.buildpacks \
        --exclude /.composer \
        --exclude /.env \
        --exclude /.heroku \
        --exclude /.profile.d \
        --exclude /.release \
        --exclude /vendor \
        /tmp/appbuild/ /app --delete \
    && chown -R herokuishuser:herokuishuser /app \
    && /exec composer install \
    && rm -rf /tmp/appbuild

CMD ["/start","web"]