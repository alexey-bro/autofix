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


.PHONY: local-up local-down local-stop prod-up prod-down prod-stop