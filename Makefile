up:
	docker compose up -d redis workspace
start:
	docker compose up -d
stop:
	docker compose stop
ps:
	docker compose ps
in:
	docker compose exec app bash