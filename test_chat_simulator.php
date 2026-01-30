#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Simulátor chatovacích scénářů pro testování AI asistenta
 * Testuje 20 různých konverzačních flowů zákazníků
 */

function createSession(string $baseUrl): ?array
{
    $ch = curl_init("$baseUrl/oil-service/chat/sessions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['language' => 'cs-CZ']));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "❌ Chyba při vytváření session: HTTP $httpCode\n";
        return null;
    }

    return json_decode($response, true);
}

function sendMessage(string $baseUrl, string $sessionId, string $message): ?array
{
    $ch = curl_init("$baseUrl/oil-service/chat/sessions/$sessionId/messages");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'language' => 'cs-CZ',
        'message' => $message
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo "❌ Chyba při odesílání zprávy: HTTP $httpCode\n";
        return null;
    }

    return json_decode($response, true);
}

function runScenario(int $scenarioNum, string $baseUrl, array $conversation): void
{
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "📋 SCÉNÁŘ #$scenarioNum: {$conversation['title']}\n";
    echo str_repeat('=', 80) . "\n\n";

    $session = createSession($baseUrl);
    if (!$session) {
        return;
    }

    $sessionId = $session['sessionId'] ?? null;
    if (!$sessionId) {
        echo "❌ Chyba: Nepodařilo se získat sessionId\n";
        return;
    }

    echo "✅ Session vytvořena: $sessionId\n";
    echo "🤖 Asistent: {$session['greeting']}\n\n";

    foreach ($conversation['messages'] as $i => $userMessage) {
        echo "👤 Zákazník: $userMessage\n";

        $response = sendMessage($baseUrl, $sessionId, $userMessage);
        if (!$response) {
            echo "❌ Konverzace přerušena kvůli chybě\n";
            break;
        }

        $assistantMessage = $response['message']['content'] ?? '(žádná odpověď)';
        echo "🤖 Asistent: $assistantMessage\n\n";

        // Malá pauza mezi zprávami
        usleep(500000); // 0.5s
    }

    echo "✅ Konverzace ukončena\n";
}

// Definice 20 různých scénářů
$scenarios = [
    [
        'title' => 'Standardní objednávka - rychlá',
        'messages' => [
            'Ahoj, potřebuju výměnu oleje',
            'Jan Novák, 777123456, jan@email.cz, Škoda Octavia, 3A12345, Praha 9, Vysočanská 123',
            'Zítra dopoledne',
        ]
    ],
    [
        'title' => 'Zákazník s dotazy na cenu',
        'messages' => [
            'Kolik stojí výměna oleje?',
            'A co je v té ceně zahrnuté?',
            'Dobře, chci objednat. Petr Svoboda, 608111222, petr@seznam.cz',
            'VW Golf 7, 2AB9876, Letňany, U stadionu 45',
            'Pozítří odpoledne',
        ]
    ],
    [
        'title' => 'Dotaz na dostupnost termínů',
        'messages' => [
            'Máte volno tento čtvrtek?',
            'A co pátek?',
            'OK, pátek ráno mi vyhovuje. Martin Dvořák, 721333444, martin@gmail.com, BMW 320d, 4CD5678, Černý Most, Bryksova 12',
        ]
    ],
    [
        'title' => 'Zákazník s poznámkou a speciálními požadavky',
        'messages' => [
            'Dobrý den, potřebuju výměnu oleje a chci použít vlastní olej Castrol',
            'Rozumím. Jana Veselá, 603222111, jana@centrum.cz, Toyota Yaris, 7EF3456, Prosek, Na Proseku 890',
            'Co nejdřív, jaké máte volno?',
            'Dobře, ten první termín beru',
        ]
    ],
    [
        'title' => 'Firemní objednávka',
        'messages' => [
            'Zdravím, objednávám pro firmu',
            'ABC s.r.o., IČO 12345678, DIČ CZ12345678',
            'Kontakt: Tomáš Procházka, 777888999, tomas@abc.cz',
            'Mercedes E220, 1PQ9999, Praha 10, Strašnická 567',
            'Příští úterý v poledne',
        ]
    ],
    [
        'title' => 'Zákazník nejistý ohledně SPZ',
        'messages' => [
            'Chci výměnu oleje ale nevim přesně SPZ',
            'Moment, jdu se podívat... je to 5GH1234',
            'Lukáš Novotný, 731444555, lukas@email.cz, Ford Focus, adresa Libeň, Zenklova 23',
            'Za týden ve středu ráno',
        ]
    ],
    [
        'title' => 'Dotaz na další služby',
        'messages' => [
            'Nabízíte i další služby kromě oleje?',
            'Výměna kabinového filtru kolik stojí?',
            'OK, chci obě služby. Pavel Král, 720555666, pavel@seznam.cz, Audi A4, 8IJ5678, Hloubětín, Pod Šancemi 44',
            'Nejbližší možný termín',
        ]
    ],
    [
        'title' => 'Zákazník mění termín během konverzace',
        'messages' => [
            'Potřebuju výměnu oleje',
            'Michaela Horáková, 604777888, michaela@gmail.com, Hyundai i30, 2KL3456',
            'Adresa je Praha 8, Ďáblická 155',
            'Čtvrtek odpoledne',
            'Vlastně ne, radši pátek dopoledne',
        ]
    ],
    [
        'title' => 'Zákazník ptající se na typ oleje',
        'messages' => [
            'Jaký olej používáte?',
            'A kolik je potřeba litrů pro Škodu Fabia?',
            'Dobře. Radek Malý, 777999000, radek@email.cz, Škoda Fabia, 6MN7890, Kobylisy, Klapkova 88',
            'Pondělí odpoledne',
        ]
    ],
    [
        'title' => 'Zákazník s urgentní potřebou',
        'messages' => [
            'URGENT! Potřebuju výměnu dnes nebo zítra!',
            'OK, tak co nejdřív možné',
            'Jakub Černý, 608123456, jakub@centrum.cz, Renault Megane, 9OP1234, Prosek, Lovosická 67',
            'Beru první možný termín',
        ]
    ],
    [
        'title' => 'Zákazník se ptá na provozovnu',
        'messages' => [
            'Máte nějakou provozovnu kam můžu přijet?',
            'A kam přesně dojíždíte?',
            'Super, jezdíte do Říčan?',
            'Pavel Nový, 721111222, pavel@email.cz, Peugeot 308, 3QR5678, Říčany, Masarykovo náměstí 15',
            'Čtvrtek v poledne',
        ]
    ],
    [
        'title' => 'Zákazník s více vozy',
        'messages' => [
            'Mám dvě auta, dá se objednat výměna pro obě najednou?',
            'OK, začneme první. Marek Svoboda, 603999888, marek@gmail.com',
            'První je VW Passat, 4ST7890, druhý Toyota Corolla, 5UV1234',
            'Adresa Čakovice, Mratínská 99',
            'Pátek dopoledne',
        ]
    ],
    [
        'title' => 'Zákazník zapomněl email',
        'messages' => [
            'Potřebuju výměnu oleje',
            'Adam Veselý, 777333444, Mazda 6, 7WX3456, Letňany, Tupolevova 11',
            'Středa odpoledne',
            'Sorry, zapomněl jsem email - adam@seznam.cz',
        ]
    ],
    [
        'title' => 'Zákazník ptající se na platbu',
        'messages' => [
            'Jak se platí? Na místě?',
            'Berete karty?',
            'OK. Kateřina Dvořáková, 604555777, katerina@email.cz, Opel Astra, 8YZ9012, Střížkov, Travná 22',
            'Úterý v poledne',
        ]
    ],
    [
        'title' => 'Velmi stručný zákazník',
        'messages' => [
            'Výměna oleje',
            'Milan Horák, 721888999',
            'milan@centrum.cz',
            'Seat Leon, 1AB2345',
            'Prosek, Vysočanská 234',
            'Čtvrtek ráno',
        ]
    ],
    [
        'title' => 'Zákazník s dlouhým popisem',
        'messages' => [
            'Dobrý den, jmenuji se Eva Malá a mám problém s autem. Kontrolka mi svítí že potřebuju výměnu oleje a nevim co s tím. Měla bych to řešit rychle nebo můžu počkat?',
            'OK, tak chci objednat. Mám Škodu Superb, SPZ 9CD5678, bydlím v Praze 9 na adrese Černý Most, Kpt. Stránského 123',
            'Kontakt 603444666, email eva@email.cz',
            'Kdy máte nejbližší volno?',
            'První termín beru',
        ]
    ],
    [
        'title' => 'Zákazník kombinující olej s dalšími službami',
        'messages' => [
            'Potřebuju komplet servis - olej, filtry, brzdy zkontrolovat',
            'Jaké filtry děláte?',
            'OK chci olej + kabinový filtr + olejový filtr',
            'Roman Svoboda, 720111333, roman@gmail.com, Honda Civic, 2EF6789, Libeň, Palmovka, U Libeňského pivovaru 6',
            'Pátek odpoledne',
        ]
    ],
    [
        'title' => 'Zákazník zkoušející limity dosahu',
        'messages' => [
            'Jezdíte až do Říčan? To je asi 25km od Prahy',
            'A co Čelákovice?',
            'OK, tak Říčany. Petr Bílý, 777444555, petr@seznam.cz, Dacia Duster, 3GH7890, Říčany, Kollárova 44',
            'Pondělí odpoledne',
        ]
    ],
    [
        'title' => 'Zákazník měnící požadavky',
        'messages' => [
            'Chci jen výměnu oleje',
            'Vlastně chci i kabinový filtr',
            'A máte i péči o klimatizaci?',
            'Tak to všechno chci',
            'Vladimír Novák, 604222333, vladimir@email.cz, Nissan Qashqai, 6IJ1234, Kobylisy, Střelničná 77',
            'Středa ráno',
        ]
    ],
    [
        'title' => 'Zákazník s otázkami po objednávce',
        'messages' => [
            'Potřebuju výměnu oleje co nejdřív',
            'Monika Králová, 731666777, monika@centrum.cz, Citroen C4, 4KL4567, Vysočany, Poděbradská 188',
            'První možný termín',
            'Jak dlouho to trvá?',
            'A máte i nějaké další produkty na auto?',
            'Co je autokosmetika?',
        ]
    ],
];

// Spuštění všech scénářů
$baseUrl = 'http://localhost:8000';

echo "\n🚀 SPOUŠTÍM SIMULACI 20 CHATOVACÍCH SCÉNÁŘŮ\n";
echo "📍 API URL: $baseUrl\n";
echo "⏰ Začátek: " . date('Y-m-d H:i:s') . "\n";

foreach ($scenarios as $i => $scenario) {
    runScenario($i + 1, $baseUrl, $scenario);

    // Pauza mezi scénáři
    if ($i < count($scenarios) - 1) {
        sleep(1);
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ SIMULACE DOKONČENA\n";
echo "⏰ Konec: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat('=', 80) . "\n\n";
