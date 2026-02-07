<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum CustomerCarBrandEnum: string
{
    public const array VALUES = [
        'abarth',
        'acura',
        'aixam',
        'alfa_romeo',
        'alpine',
        'aston_martin',
        'audi',
        'austin',
        'bentley',
        'bmw',
        'borgward',
        'brilliance',
        'bugatti',
        'buick',
        'byd',
        'cadillac',
        'caterham',
        'changan',
        'chery',
        'chevrolet',
        'chrysler',
        'citroen',
        'cupra',
        'dacia',
        'daewoo',
        'daihatsu',
        'daimler',
        'datsun',
        'dodge',
        'ds_automobiles',
        'ferrari',
        'fiat',
        'fisker',
        'ford',
        'foton',
        'geely',
        'genesis',
        'gmc',
        'great_wall',
        'honda',
        'hummer',
        'hyundai',
        'infiniti',
        'isuzu',
        'iveco',
        'jaguar',
        'jeep',
        'kia',
        'ktm',
        'lada',
        'lamborghini',
        'lancia',
        'land_rover',
        'lexus',
        'lincoln',
        'lotus',
        'lucid',
        'mahindra',
        'man',
        'maserati',
        'maxus',
        'maybach',
        'mazda',
        'mclaren',
        'mercedes_benz',
        'mercury',
        'mg',
        'mini',
        'mitsubishi',
        'morgan',
        'nissan',
        'opel',
        'pagani',
        'peugeot',
        'polestar',
        'pontiac',
        'porsche',
        'proton',
        'ram',
        'renault',
        'rimac',
        'rolls_royce',
        'rover',
        'saab',
        'scania',
        'seat',
        'skoda',
        'smart',
        'ssangyong',
        'subaru',
        'suzuki',
        'tata',
        'tesla',
        'toyota',
        'vauxhall',
        'volkswagen',
        'volvo',
        'wartburg',
        'wiesmann',
        'zastava',
        'zaz',
        'zenvo',
        'unassigned',
    ];

    case ABARTH = 'abarth';
    case ACURA = 'acura';
    case AIXAM = 'aixam';
    case ALFA_ROMEO = 'alfa_romeo';
    case ALPINE = 'alpine';
    case ASTON_MARTIN = 'aston_martin';
    case AUDI = 'audi';
    case AUSTIN = 'austin';
    case BENTLEY = 'bentley';
    case BMW = 'bmw';
    case BORGWARD = 'borgward';
    case BRILLIANCE = 'brilliance';
    case BUGATTI = 'bugatti';
    case BUICK = 'buick';
    case BYD = 'byd';
    case CADILLAC = 'cadillac';
    case CATERHAM = 'caterham';
    case CHANGAN = 'changan';
    case CHERY = 'chery';
    case CHEVROLET = 'chevrolet';
    case CHRYSLER = 'chrysler';
    case CITROEN = 'citroen';
    case CUPRA = 'cupra';
    case DACIA = 'dacia';
    case DAEWOO = 'daewoo';
    case DAIHATSU = 'daihatsu';
    case DAIMLER = 'daimler';
    case DATSUN = 'datsun';
    case DODGE = 'dodge';
    case DS_AUTOMOBILES = 'ds_automobiles';
    case FERRARI = 'ferrari';
    case FIAT = 'fiat';
    case FISKER = 'fisker';
    case FORD = 'ford';
    case FOTON = 'foton';
    case GEELY = 'geely';
    case GENESIS = 'genesis';
    case GMC = 'gmc';
    case GREAT_WALL = 'great_wall';
    case HONDA = 'honda';
    case HUMMER = 'hummer';
    case HYUNDAI = 'hyundai';
    case INFINITI = 'infiniti';
    case ISUZU = 'isuzu';
    case IVECO = 'iveco';
    case JAGUAR = 'jaguar';
    case JEEP = 'jeep';
    case KIA = 'kia';
    case KTM = 'ktm';
    case LADA = 'lada';
    case LAMBORGHINI = 'lamborghini';
    case LANCIA = 'lancia';
    case LAND_ROVER = 'land_rover';
    case LEXUS = 'lexus';
    case LINCOLN = 'lincoln';
    case LOTUS = 'lotus';
    case LUCID = 'lucid';
    case MAHINDRA = 'mahindra';
    case MAN = 'man';
    case MASERATI = 'maserati';
    case MAXUS = 'maxus';
    case MAYBACH = 'maybach';
    case MAZDA = 'mazda';
    case MCLAREN = 'mclaren';
    case MERCEDES_BENZ = 'mercedes_benz';
    case MERCURY = 'mercury';
    case MG = 'mg';
    case MINI = 'mini';
    case MITSUBISHI = 'mitsubishi';
    case MORGAN = 'morgan';
    case NISSAN = 'nissan';
    case OPEL = 'opel';
    case PAGANI = 'pagani';
    case PEUGEOT = 'peugeot';
    case POLESTAR = 'polestar';
    case PONTIAC = 'pontiac';
    case PORSCHE = 'porsche';
    case PROTON = 'proton';
    case RAM = 'ram';
    case RENAULT = 'renault';
    case RIMAC = 'rimac';
    case ROLLS_ROYCE = 'rolls_royce';
    case ROVER = 'rover';
    case SAAB = 'saab';
    case SCANIA = 'scania';
    case SEAT = 'seat';
    case SKODA = 'skoda';
    case SMART = 'smart';
    case SSANGYONG = 'ssangyong';
    case SUBARU = 'subaru';
    case SUZUKI = 'suzuki';
    case TATA = 'tata';
    case TESLA = 'tesla';
    case TOYOTA = 'toyota';
    case VAUXHALL = 'vauxhall';
    case VOLKSWAGEN = 'volkswagen';
    case VOLVO = 'volvo';
    case WARTBURG = 'wartburg';
    case WIESMANN = 'wiesmann';
    case ZASTAVA = 'zastava';
    case ZAZ = 'zaz';
    case ZENVO = 'zenvo';
    case UNASSIGNED = 'unassigned';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
