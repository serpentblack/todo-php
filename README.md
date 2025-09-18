# ToDo PHP + SQLite + Bootstrap • CI/CD (GitHub Actions + Render)

App mínima **PHP puro** con **Bootstrap** y **SQLite** + pipeline **CI** y **CD**.

## 1) Ejecutar local sin Docker
```bash
composer install
composer test
php -S 0.0.0.0:8082
# abrir http://localhost:8082
```

## 2) Ejecutar con Docker
```bash
docker build -t todo-php .
docker run --rm -e PORT=10000 -p 10000:10000 todo-php
# abrir http://localhost:10000
```

## 3) CI — GitHub Actions
- Lint + PHPUnit en cada push/PR.

## 4) CD — Render (por rama main)
- Crea Web Service (Docker) desde este repo.
- Copia **Deploy Hook** y guárdalo en GitHub como `RENDER_DEPLOY_HOOK_URL`.
- Cada **push a main** dispara el deploy.

## 5) Flujo rápido
```bash
git checkout -b feat/mi-cambio
# edita index.php / src/*
git add .
git commit -m "feat: mi-cambio"
git push -u origin feat/mi-cambio
# PR -> merge a main => CI + Deploy
```
