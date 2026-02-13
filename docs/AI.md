# Oil Service Service — AI kontext

## Co je tato aplikace
Backend (PHP 8.5 + Symfony 7.4) pro celý ekosystém Oil Service. Poskytuje API a aplikační logiku pro administraci, webový prodej i mobilní appku techniků.

Doména řeší mobilní výměnu oleje, objednávky, plánování tras, evidenci skladu a bezpečné zpracování odpadu (vyjetý olej a související materiály).

## Kdy sahat do tohoto projektu
- Když se mění business logika, workflow objednávek nebo validace dat.
- Když se přidávají/upravují API endpointy pro frontend, web nebo mobilní appku.
- Když se řeší autentizace, práva uživatelů, soubory, geokódování nebo integrace.
- Když se mění schéma databáze a migrace.

## Základní struktura projektu
- `src/Modules/` — aplikační moduly podle domény (`Auth`, `CarApp`, `CarDatabase`, `Files`, `OilService`, `Users`, `Warehouse`).
- `src/Domain/` — sdílené doménové stavební bloky (validace, typy, výjimky, HTTP, Doctrine helpery).
- `src/Auth`, `src/OilService`, `src/Warehouse`, ... — doménové oblasti s konkrétní implementací.
- `config/` — Symfony konfigurace, služby, routy, balíčky.
- `migrations/` — Doctrine migrace schématu databáze.
- `tests/` — testy backendu.
- `public/` — veřejný vstup (`index.php`) a statické soubory.

## Technologický kontext
- Runtime: PHP `>=8.5`
- Framework: Symfony `7.4.*`
- Data: Doctrine ORM + Doctrine Migrations
- API dokumentace: Nelmio (OpenAPI)

## Praktické poznámky pro AI
- Tento projekt ber jako source of truth pro doménová pravidla.
- API kontrakty ověřuj proti `api.doc.swagger.yaml` v klientech.
- Migrace negeneruj ručně; používej `php85 bin/console doctrine:migrations:diff`.
