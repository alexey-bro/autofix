# Makefile for Docker Nginx PHP Composer MySQL

include .env

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  local-up								local-up docker"
	@echo "  local-down								local-down docker"
	@echo "  local-stop								local-stop docker"
	@echo "  prod-up								prod-up docker"
	@echo "  prod-down								prod-down docker"
	@echo "  prod-stop								prod-down stop"
	@echo "  cert-issue-with-www					Выпуск сертификата через Certbot в Docker (webroot режим), включая поддомен www"
	@echo "  cert-issue								Обновление сертификата"
	@echo "  cert-renew								Выпуск сертификата через Certbot в Docker (webroot режим)"
	@echo "  cert-list								Проверка сертификатов"
	@echo "  cert-dry-run							Тест обновления (без реального выпуска)"

local-up:
	docker compose -f docker-compose.yml -f docker-compose.local.yml up -d

local-down:
	docker compose -f docker-compose.yml -f docker-compose.local.yml down

local-stop:
	docker compose -f docker-compose.yml -f docker-compose.local.yml stop

prod-up:
	docker compose -f docker-compose.yml -f docker-compose.production.yml up -d

prod-down:
	docker compose -f docker-compose.yml -f docker-compose.production.yml down

prod-stop:
	docker compose -f docker-compose.yml -f docker-compose.production.yml stop


DOMAIN ?= yourdomain.com
EMAIL  ?= your@email.com
WEBROOT_PATH = ./docker/certbot/www
CERTS_PATH   = ./docker/certbot/certs

# Выпуск сертификата через Certbot в Docker (webroot режим)
cert-issue-with-www:
	@echo "📋 Создаём папки для certbot..."
	mkdir -p $(WEBROOT_PATH) $(CERTS_PATH)
	@echo "🔐 Выпускаем сертификат для $(DOMAIN)..."
	docker run --rm \
		-v $(PWD)/$(CERTS_PATH):/etc/letsencrypt \
		-v $(PWD)/$(WEBROOT_PATH):/var/www/certbot \
		certbot/certbot certonly \
			--webroot \
			--webroot-path=/var/www/certbot \
			--email $(EMAIL) \
			--agree-tos \
			--no-eff-email \
			-d $(DOMAIN) \
			-d www.$(DOMAIN)
	@echo "✅ Сертификат успешно выпущен!"

# Выпуск сертификата через Certbot в Docker (webroot режим)
cert-issue:
	@echo "📋 Создаём папки для certbot..."
	mkdir -p $(WEBROOT_PATH) $(CERTS_PATH)
	@echo "🔐 Выпускаем сертификат для $(DOMAIN)..."
	docker run --rm \
		-v $(PWD)/$(CERTS_PATH):/etc/letsencrypt \
		-v $(PWD)/$(WEBROOT_PATH):/var/www/certbot \
		certbot/certbot certonly \
			--webroot \
			--webroot-path=/var/www/certbot \
			--email $(EMAIL) \
			--agree-tos \
			--no-eff-email \
			-d $(DOMAIN)
	@echo "✅ Сертификат успешно выпущен!"


# Обновление сертификата
cert-renew:
	@echo "🔄 Обновляем сертификат..."
	docker run --rm \
		-v $(PWD)/$(CERTS_PATH):/etc/letsencrypt \
		-v $(PWD)/$(WEBROOT_PATH):/var/www/certbot \
		certbot/certbot renew --quiet
	@echo "🔁 Перезапускаем Nginx..."
	docker compose restart nginx
	@echo "✅ Сертификат обновлён!"

# Проверка сертификатов
cert-list:
	docker run --rm \
		-v $(PWD)/$(CERTS_PATH):/etc/letsencrypt \
		certbot/certbot certificates

# Тест обновления (без реального выпуска)
cert-dry-run:
	docker run --rm \
		-v $(PWD)/$(CERTS_PATH):/etc/letsencrypt \
		-v $(PWD)/$(WEBROOT_PATH):/var/www/certbot \
		certbot/certbot renew --dry-run


.PHONY: local-up local-down local-stop prod-up prod-down prod-stop