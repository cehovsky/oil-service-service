# Skill: Database Workflow (Doctrine)

## Účel
Zajistit konzistentní a bezpečný postup změn databázového schématu v backendu.

## Tvrdá pravidla
- Nikdy negeneruj migrace ručně.
- Při změně Doctrine entit vždy vygeneruj migraci výhradně příkazem:
  - `php85 bin/console doctrine:migrations:diff`
- Po vygenerování zkontroluj diff a případně uprav pouze minimálně nutné části.
- Migraci aplikuj standardně přes Doctrine migrate command.

## Standardní postup
1. Proveď změny v entitách/mapování.
2. Vygeneruj migraci pomocí `php85 bin/console doctrine:migrations:diff`.
3. Zkontroluj obsah v `migrations/Version*.php`.
4. Spusť migraci v lokálním prostředí.
5. Ověř, že změna funguje v API flow.

## Zakázané postupy
- Nevytvářej `migrations/Version*.php` ručně od nuly.
- Nepřepisuj existující starší migrace, pokud to není explicitně požadováno.
