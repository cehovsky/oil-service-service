<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: '1A2 3456')]
    private string $licensePlate;

    #[OA\Property(example: 'skoda', nullable: true)]
    private ?string $brand;

    #[OA\Property(example: 'Octavia', nullable: true)]
    private ?string $model;

    #[OA\Property(example: 'TMBEFF654V7529422', nullable: true)]
    private ?string $vin;

    #[OA\Property(ref: new Model(type: OilServiceUserDTO::class), nullable: true)]
    private ?OilServiceUserDTO $user;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(nullable: true)]
    private ?string $dkDatumPrvniRegistrace;

    #[OA\Property(nullable: true)]
    private ?string $dkDatumPrvniRegistraceVCr;

    #[OA\Property(nullable: true)]
    private ?string $dkCisloTypovehoSchvaleni;

    #[OA\Property(nullable: true)]
    private ?string $dkHomologaceEs;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloDruh;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloDruh2;

    #[OA\Property(nullable: true)]
    private ?string $dkKategorie;

    #[OA\Property(nullable: true)]
    private ?string $dkTovarniZnacka;

    #[OA\Property(nullable: true)]
    private ?string $dkTyp;

    #[OA\Property(nullable: true)]
    private ?string $dkVarianta;

    #[OA\Property(nullable: true)]
    private ?string $dkVerze;

    #[OA\Property(nullable: true)]
    private ?string $dkVin;

    #[OA\Property(nullable: true)]
    private ?string $dkObchodniOznaceni;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloVyrobce;

    #[OA\Property(nullable: true)]
    private ?string $dkMotorVyrobce;

    #[OA\Property(nullable: true)]
    private ?string $dkMotorTyp;

    #[OA\Property(nullable: true)]
    private ?string $dkMotorMaxVykon;

    #[OA\Property(nullable: true)]
    private ?string $dkPalivo;

    #[OA\Property(nullable: true)]
    private ?string $dkMotorZdvihObjem;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloElektricke;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloHybridni;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloHybridniTrida;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseEHKOSNEHSES;

    #[OA\Property(nullable: true)]
    private ?string $dkEmisniUroven;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseKSA;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseCO2;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseCO2Specificke;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseSnizeniNedc;

    #[OA\Property(nullable: true)]
    private ?string $dkEmiseSnizeniWltp;

    #[OA\Property(nullable: true)]
    private ?string $dkSpotrebaMetodika;

    #[OA\Property(nullable: true)]
    private ?string $dkSpotrebaNa100Km;

    #[OA\Property(nullable: true)]
    private ?string $dkSpotreba;

    #[OA\Property(nullable: true)]
    private ?string $dkSpotrebaEl;

    #[OA\Property(nullable: true)]
    private ?string $dkDojezdZR;

    #[OA\Property(nullable: true)]
    private ?string $dkVyrobceKaroserie;

    #[OA\Property(nullable: true)]
    private ?string $dkKaroserieDruh;

    #[OA\Property(nullable: true)]
    private ?string $dkKaroserieVyrobniCislo;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloKaroserieBarva;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloKaroserieBarvaDoplnkova;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloKaroserieMist;

    #[OA\Property(nullable: true)]
    private ?string $dkRozmery;

    #[OA\Property(nullable: true)]
    private ?string $dkRozmeryRozvor;

    #[OA\Property(nullable: true)]
    private ?string $dkRozchod;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiProvozni;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiPripPov;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiPripPovN;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiPripPovBrzdenePV;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiPripPovNebrzdenePV;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiPripPovJS;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostiTestWltp;

    #[OA\Property(nullable: true)]
    private ?string $dkHmotnostUzitecneZatizeniPrumer;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloSpojZarizNazev;

    #[OA\Property(nullable: true)]
    private ?string $dkNapravyPocetDruh;

    #[OA\Property(nullable: true)]
    private ?string $dkNapravyPneuRafky;

    #[OA\Property(nullable: true)]
    private ?string $dkHlukStojiciOtacky;

    #[OA\Property(nullable: true)]
    private ?string $dkHlukJizda;

    #[OA\Property(nullable: true)]
    private ?string $dkNejvyssiRychlost;

    #[OA\Property(nullable: true)]
    private ?string $dkPomerVykonHmotnost;

    #[OA\Property(nullable: true)]
    private ?string $dkInovativniTechnologie;

    #[OA\Property(nullable: true)]
    private ?string $dkStupenDokonceni;

    #[OA\Property(nullable: true)]
    private ?string $dkFaktorOdchylkyDe;

    #[OA\Property(nullable: true)]
    private ?string $dkFaktorVerifikaceVf;

    #[OA\Property(nullable: true)]
    private ?string $dkVozidloUcel;

    #[OA\Property(nullable: true)]
    private ?string $dkDalsiZaznamy;

    #[OA\Property(nullable: true)]
    private ?string $dkAlternativniProvedeni;

    #[OA\Property(nullable: true)]
    private ?string $dkCisloTp;

    #[OA\Property(nullable: true)]
    private ?string $dkCisloOrv;

    #[OA\Property(nullable: true)]
    private ?string $dkOrvZadrzeno;

    #[OA\Property(nullable: true)]
    private ?string $dkOrvKeSkartaci;

    #[OA\Property(nullable: true)]
    private ?string $dkOrvOdevzdano;

    #[OA\Property(nullable: true)]
    private ?string $dkRzDruh;

    #[OA\Property(nullable: true)]
    private ?string $dkRzJkVydana;

    #[OA\Property(nullable: true)]
    private ?string $dkRzKeSkartaci;

    #[OA\Property(nullable: true)]
    private ?string $dkRzOdevzdano;

    #[OA\Property(nullable: true)]
    private ?string $dkRzZadrzena;

    #[OA\Property(nullable: true)]
    private ?string $dkZarazeniVozidla;

    #[OA\Property(nullable: true)]
    private ?string $dkPravidelnaTechnickaProhlidkaDo;

    #[OA\Property(nullable: true)]
    private ?string $dkPredRegistraciProhlidkaDne;

    #[OA\Property(nullable: true)]
    private ?string $dkPredSchvalenimProhlidkaDne;

    #[OA\Property(nullable: true)]
    private ?string $dkEvidencniProhlidkaDne;

    #[OA\Property(nullable: true)]
    private ?string $dkHistorickeVozidloProhlidkaDne;

    #[OA\Property(nullable: true)]
    private ?string $dkStatusNazev;

    #[OA\Property(nullable: true)]
    private ?string $dkPocetVlastniku;

    #[OA\Property(nullable: true)]
    private ?string $dkPocetProvozovatelu;

    public function __construct(
        string $id,
        string $licensePlate,
        ?string $brand,
        ?string $model,
        ?string $vin,
        ?OilServiceUserDTO $user,
        string $createdAt,
        ?string $dkDatumPrvniRegistrace,
        ?string $dkDatumPrvniRegistraceVCr,
        ?string $dkCisloTypovehoSchvaleni,
        ?string $dkHomologaceEs,
        ?string $dkVozidloDruh,
        ?string $dkVozidloDruh2,
        ?string $dkKategorie,
        ?string $dkTovarniZnacka,
        ?string $dkTyp,
        ?string $dkVarianta,
        ?string $dkVerze,
        ?string $dkVin,
        ?string $dkObchodniOznaceni,
        ?string $dkVozidloVyrobce,
        ?string $dkMotorVyrobce,
        ?string $dkMotorTyp,
        ?string $dkMotorMaxVykon,
        ?string $dkPalivo,
        ?string $dkMotorZdvihObjem,
        ?string $dkVozidloElektricke,
        ?string $dkVozidloHybridni,
        ?string $dkVozidloHybridniTrida,
        ?string $dkEmiseEHKOSNEHSES,
        ?string $dkEmisniUroven,
        ?string $dkEmiseKSA,
        ?string $dkEmiseCO2,
        ?string $dkEmiseCO2Specificke,
        ?string $dkEmiseSnizeniNedc,
        ?string $dkEmiseSnizeniWltp,
        ?string $dkSpotrebaMetodika,
        ?string $dkSpotrebaNa100Km,
        ?string $dkSpotreba,
        ?string $dkSpotrebaEl,
        ?string $dkDojezdZR,
        ?string $dkVyrobceKaroserie,
        ?string $dkKaroserieDruh,
        ?string $dkKaroserieVyrobniCislo,
        ?string $dkVozidloKaroserieBarva,
        ?string $dkVozidloKaroserieBarvaDoplnkova,
        ?string $dkVozidloKaroserieMist,
        ?string $dkRozmery,
        ?string $dkRozmeryRozvor,
        ?string $dkRozchod,
        ?string $dkHmotnostiProvozni,
        ?string $dkHmotnostiPripPov,
        ?string $dkHmotnostiPripPovN,
        ?string $dkHmotnostiPripPovBrzdenePV,
        ?string $dkHmotnostiPripPovNebrzdenePV,
        ?string $dkHmotnostiPripPovJS,
        ?string $dkHmotnostiTestWltp,
        ?string $dkHmotnostUzitecneZatizeniPrumer,
        ?string $dkVozidloSpojZarizNazev,
        ?string $dkNapravyPocetDruh,
        ?string $dkNapravyPneuRafky,
        ?string $dkHlukStojiciOtacky,
        ?string $dkHlukJizda,
        ?string $dkNejvyssiRychlost,
        ?string $dkPomerVykonHmotnost,
        ?string $dkInovativniTechnologie,
        ?string $dkStupenDokonceni,
        ?string $dkFaktorOdchylkyDe,
        ?string $dkFaktorVerifikaceVf,
        ?string $dkVozidloUcel,
        ?string $dkDalsiZaznamy,
        ?string $dkAlternativniProvedeni,
        ?string $dkCisloTp,
        ?string $dkCisloOrv,
        ?string $dkOrvZadrzeno,
        ?string $dkOrvKeSkartaci,
        ?string $dkOrvOdevzdano,
        ?string $dkRzDruh,
        ?string $dkRzJkVydana,
        ?string $dkRzKeSkartaci,
        ?string $dkRzOdevzdano,
        ?string $dkRzZadrzena,
        ?string $dkZarazeniVozidla,
        ?string $dkPravidelnaTechnickaProhlidkaDo,
        ?string $dkPredRegistraciProhlidkaDne,
        ?string $dkPredSchvalenimProhlidkaDne,
        ?string $dkEvidencniProhlidkaDne,
        ?string $dkHistorickeVozidloProhlidkaDne,
        ?string $dkStatusNazev,
        ?string $dkPocetVlastniku,
        ?string $dkPocetProvozovatelu,
    ) {
        $this->id = $id;
        $this->licensePlate = $licensePlate;
        $this->brand = $brand;
        $this->model = $model;
        $this->vin = $vin;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->dkDatumPrvniRegistrace = $dkDatumPrvniRegistrace;
        $this->dkDatumPrvniRegistraceVCr = $dkDatumPrvniRegistraceVCr;
        $this->dkCisloTypovehoSchvaleni = $dkCisloTypovehoSchvaleni;
        $this->dkHomologaceEs = $dkHomologaceEs;
        $this->dkVozidloDruh = $dkVozidloDruh;
        $this->dkVozidloDruh2 = $dkVozidloDruh2;
        $this->dkKategorie = $dkKategorie;
        $this->dkTovarniZnacka = $dkTovarniZnacka;
        $this->dkTyp = $dkTyp;
        $this->dkVarianta = $dkVarianta;
        $this->dkVerze = $dkVerze;
        $this->dkVin = $dkVin;
        $this->dkObchodniOznaceni = $dkObchodniOznaceni;
        $this->dkVozidloVyrobce = $dkVozidloVyrobce;
        $this->dkMotorVyrobce = $dkMotorVyrobce;
        $this->dkMotorTyp = $dkMotorTyp;
        $this->dkMotorMaxVykon = $dkMotorMaxVykon;
        $this->dkPalivo = $dkPalivo;
        $this->dkMotorZdvihObjem = $dkMotorZdvihObjem;
        $this->dkVozidloElektricke = $dkVozidloElektricke;
        $this->dkVozidloHybridni = $dkVozidloHybridni;
        $this->dkVozidloHybridniTrida = $dkVozidloHybridniTrida;
        $this->dkEmiseEHKOSNEHSES = $dkEmiseEHKOSNEHSES;
        $this->dkEmisniUroven = $dkEmisniUroven;
        $this->dkEmiseKSA = $dkEmiseKSA;
        $this->dkEmiseCO2 = $dkEmiseCO2;
        $this->dkEmiseCO2Specificke = $dkEmiseCO2Specificke;
        $this->dkEmiseSnizeniNedc = $dkEmiseSnizeniNedc;
        $this->dkEmiseSnizeniWltp = $dkEmiseSnizeniWltp;
        $this->dkSpotrebaMetodika = $dkSpotrebaMetodika;
        $this->dkSpotrebaNa100Km = $dkSpotrebaNa100Km;
        $this->dkSpotreba = $dkSpotreba;
        $this->dkSpotrebaEl = $dkSpotrebaEl;
        $this->dkDojezdZR = $dkDojezdZR;
        $this->dkVyrobceKaroserie = $dkVyrobceKaroserie;
        $this->dkKaroserieDruh = $dkKaroserieDruh;
        $this->dkKaroserieVyrobniCislo = $dkKaroserieVyrobniCislo;
        $this->dkVozidloKaroserieBarva = $dkVozidloKaroserieBarva;
        $this->dkVozidloKaroserieBarvaDoplnkova = $dkVozidloKaroserieBarvaDoplnkova;
        $this->dkVozidloKaroserieMist = $dkVozidloKaroserieMist;
        $this->dkRozmery = $dkRozmery;
        $this->dkRozmeryRozvor = $dkRozmeryRozvor;
        $this->dkRozchod = $dkRozchod;
        $this->dkHmotnostiProvozni = $dkHmotnostiProvozni;
        $this->dkHmotnostiPripPov = $dkHmotnostiPripPov;
        $this->dkHmotnostiPripPovN = $dkHmotnostiPripPovN;
        $this->dkHmotnostiPripPovBrzdenePV = $dkHmotnostiPripPovBrzdenePV;
        $this->dkHmotnostiPripPovNebrzdenePV = $dkHmotnostiPripPovNebrzdenePV;
        $this->dkHmotnostiPripPovJS = $dkHmotnostiPripPovJS;
        $this->dkHmotnostiTestWltp = $dkHmotnostiTestWltp;
        $this->dkHmotnostUzitecneZatizeniPrumer = $dkHmotnostUzitecneZatizeniPrumer;
        $this->dkVozidloSpojZarizNazev = $dkVozidloSpojZarizNazev;
        $this->dkNapravyPocetDruh = $dkNapravyPocetDruh;
        $this->dkNapravyPneuRafky = $dkNapravyPneuRafky;
        $this->dkHlukStojiciOtacky = $dkHlukStojiciOtacky;
        $this->dkHlukJizda = $dkHlukJizda;
        $this->dkNejvyssiRychlost = $dkNejvyssiRychlost;
        $this->dkPomerVykonHmotnost = $dkPomerVykonHmotnost;
        $this->dkInovativniTechnologie = $dkInovativniTechnologie;
        $this->dkStupenDokonceni = $dkStupenDokonceni;
        $this->dkFaktorOdchylkyDe = $dkFaktorOdchylkyDe;
        $this->dkFaktorVerifikaceVf = $dkFaktorVerifikaceVf;
        $this->dkVozidloUcel = $dkVozidloUcel;
        $this->dkDalsiZaznamy = $dkDalsiZaznamy;
        $this->dkAlternativniProvedeni = $dkAlternativniProvedeni;
        $this->dkCisloTp = $dkCisloTp;
        $this->dkCisloOrv = $dkCisloOrv;
        $this->dkOrvZadrzeno = $dkOrvZadrzeno;
        $this->dkOrvKeSkartaci = $dkOrvKeSkartaci;
        $this->dkOrvOdevzdano = $dkOrvOdevzdano;
        $this->dkRzDruh = $dkRzDruh;
        $this->dkRzJkVydana = $dkRzJkVydana;
        $this->dkRzKeSkartaci = $dkRzKeSkartaci;
        $this->dkRzOdevzdano = $dkRzOdevzdano;
        $this->dkRzZadrzena = $dkRzZadrzena;
        $this->dkZarazeniVozidla = $dkZarazeniVozidla;
        $this->dkPravidelnaTechnickaProhlidkaDo = $dkPravidelnaTechnickaProhlidkaDo;
        $this->dkPredRegistraciProhlidkaDne = $dkPredRegistraciProhlidkaDne;
        $this->dkPredSchvalenimProhlidkaDne = $dkPredSchvalenimProhlidkaDne;
        $this->dkEvidencniProhlidkaDne = $dkEvidencniProhlidkaDne;
        $this->dkHistorickeVozidloProhlidkaDne = $dkHistorickeVozidloProhlidkaDne;
        $this->dkStatusNazev = $dkStatusNazev;
        $this->dkPocetVlastniku = $dkPocetVlastniku;
        $this->dkPocetProvozovatelu = $dkPocetProvozovatelu;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function getUser(): ?OilServiceUserDTO
    {
        return $this->user;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getDkDatumPrvniRegistrace(): ?string
    {
        return $this->dkDatumPrvniRegistrace;
    }

    public function getDkDatumPrvniRegistraceVCr(): ?string
    {
        return $this->dkDatumPrvniRegistraceVCr;
    }

    public function getDkCisloTypovehoSchvaleni(): ?string
    {
        return $this->dkCisloTypovehoSchvaleni;
    }

    public function getDkHomologaceEs(): ?string
    {
        return $this->dkHomologaceEs;
    }

    public function getDkVozidloDruh(): ?string
    {
        return $this->dkVozidloDruh;
    }

    public function getDkVozidloDruh2(): ?string
    {
        return $this->dkVozidloDruh2;
    }

    public function getDkKategorie(): ?string
    {
        return $this->dkKategorie;
    }

    public function getDkTovarniZnacka(): ?string
    {
        return $this->dkTovarniZnacka;
    }

    public function getDkTyp(): ?string
    {
        return $this->dkTyp;
    }

    public function getDkVarianta(): ?string
    {
        return $this->dkVarianta;
    }

    public function getDkVerze(): ?string
    {
        return $this->dkVerze;
    }

    public function getDkVin(): ?string
    {
        return $this->dkVin;
    }

    public function getDkObchodniOznaceni(): ?string
    {
        return $this->dkObchodniOznaceni;
    }

    public function getDkVozidloVyrobce(): ?string
    {
        return $this->dkVozidloVyrobce;
    }

    public function getDkMotorVyrobce(): ?string
    {
        return $this->dkMotorVyrobce;
    }

    public function getDkMotorTyp(): ?string
    {
        return $this->dkMotorTyp;
    }

    public function getDkMotorMaxVykon(): ?string
    {
        return $this->dkMotorMaxVykon;
    }

    public function getDkPalivo(): ?string
    {
        return $this->dkPalivo;
    }

    public function getDkMotorZdvihObjem(): ?string
    {
        return $this->dkMotorZdvihObjem;
    }

    public function getDkVozidloElektricke(): ?string
    {
        return $this->dkVozidloElektricke;
    }

    public function getDkVozidloHybridni(): ?string
    {
        return $this->dkVozidloHybridni;
    }

    public function getDkVozidloHybridniTrida(): ?string
    {
        return $this->dkVozidloHybridniTrida;
    }

    public function getDkEmiseEHKOSNEHSES(): ?string
    {
        return $this->dkEmiseEHKOSNEHSES;
    }

    public function getDkEmisniUroven(): ?string
    {
        return $this->dkEmisniUroven;
    }

    public function getDkEmiseKSA(): ?string
    {
        return $this->dkEmiseKSA;
    }

    public function getDkEmiseCO2(): ?string
    {
        return $this->dkEmiseCO2;
    }

    public function getDkEmiseCO2Specificke(): ?string
    {
        return $this->dkEmiseCO2Specificke;
    }

    public function getDkEmiseSnizeniNedc(): ?string
    {
        return $this->dkEmiseSnizeniNedc;
    }

    public function getDkEmiseSnizeniWltp(): ?string
    {
        return $this->dkEmiseSnizeniWltp;
    }

    public function getDkSpotrebaMetodika(): ?string
    {
        return $this->dkSpotrebaMetodika;
    }

    public function getDkSpotrebaNa100Km(): ?string
    {
        return $this->dkSpotrebaNa100Km;
    }

    public function getDkSpotreba(): ?string
    {
        return $this->dkSpotreba;
    }

    public function getDkSpotrebaEl(): ?string
    {
        return $this->dkSpotrebaEl;
    }

    public function getDkDojezdZR(): ?string
    {
        return $this->dkDojezdZR;
    }

    public function getDkVyrobceKaroserie(): ?string
    {
        return $this->dkVyrobceKaroserie;
    }

    public function getDkKaroserieDruh(): ?string
    {
        return $this->dkKaroserieDruh;
    }

    public function getDkKaroserieVyrobniCislo(): ?string
    {
        return $this->dkKaroserieVyrobniCislo;
    }

    public function getDkVozidloKaroserieBarva(): ?string
    {
        return $this->dkVozidloKaroserieBarva;
    }

    public function getDkVozidloKaroserieBarvaDoplnkova(): ?string
    {
        return $this->dkVozidloKaroserieBarvaDoplnkova;
    }

    public function getDkVozidloKaroserieMist(): ?string
    {
        return $this->dkVozidloKaroserieMist;
    }

    public function getDkRozmery(): ?string
    {
        return $this->dkRozmery;
    }

    public function getDkRozmeryRozvor(): ?string
    {
        return $this->dkRozmeryRozvor;
    }

    public function getDkRozchod(): ?string
    {
        return $this->dkRozchod;
    }

    public function getDkHmotnostiProvozni(): ?string
    {
        return $this->dkHmotnostiProvozni;
    }

    public function getDkHmotnostiPripPov(): ?string
    {
        return $this->dkHmotnostiPripPov;
    }

    public function getDkHmotnostiPripPovN(): ?string
    {
        return $this->dkHmotnostiPripPovN;
    }

    public function getDkHmotnostiPripPovBrzdenePV(): ?string
    {
        return $this->dkHmotnostiPripPovBrzdenePV;
    }

    public function getDkHmotnostiPripPovNebrzdenePV(): ?string
    {
        return $this->dkHmotnostiPripPovNebrzdenePV;
    }

    public function getDkHmotnostiPripPovJS(): ?string
    {
        return $this->dkHmotnostiPripPovJS;
    }

    public function getDkHmotnostiTestWltp(): ?string
    {
        return $this->dkHmotnostiTestWltp;
    }

    public function getDkHmotnostUzitecneZatizeniPrumer(): ?string
    {
        return $this->dkHmotnostUzitecneZatizeniPrumer;
    }

    public function getDkVozidloSpojZarizNazev(): ?string
    {
        return $this->dkVozidloSpojZarizNazev;
    }

    public function getDkNapravyPocetDruh(): ?string
    {
        return $this->dkNapravyPocetDruh;
    }

    public function getDkNapravyPneuRafky(): ?string
    {
        return $this->dkNapravyPneuRafky;
    }

    public function getDkHlukStojiciOtacky(): ?string
    {
        return $this->dkHlukStojiciOtacky;
    }

    public function getDkHlukJizda(): ?string
    {
        return $this->dkHlukJizda;
    }

    public function getDkNejvyssiRychlost(): ?string
    {
        return $this->dkNejvyssiRychlost;
    }

    public function getDkPomerVykonHmotnost(): ?string
    {
        return $this->dkPomerVykonHmotnost;
    }

    public function getDkInovativniTechnologie(): ?string
    {
        return $this->dkInovativniTechnologie;
    }

    public function getDkStupenDokonceni(): ?string
    {
        return $this->dkStupenDokonceni;
    }

    public function getDkFaktorOdchylkyDe(): ?string
    {
        return $this->dkFaktorOdchylkyDe;
    }

    public function getDkFaktorVerifikaceVf(): ?string
    {
        return $this->dkFaktorVerifikaceVf;
    }

    public function getDkVozidloUcel(): ?string
    {
        return $this->dkVozidloUcel;
    }

    public function getDkDalsiZaznamy(): ?string
    {
        return $this->dkDalsiZaznamy;
    }

    public function getDkAlternativniProvedeni(): ?string
    {
        return $this->dkAlternativniProvedeni;
    }

    public function getDkCisloTp(): ?string
    {
        return $this->dkCisloTp;
    }

    public function getDkCisloOrv(): ?string
    {
        return $this->dkCisloOrv;
    }

    public function getDkOrvZadrzeno(): ?string
    {
        return $this->dkOrvZadrzeno;
    }

    public function getDkOrvKeSkartaci(): ?string
    {
        return $this->dkOrvKeSkartaci;
    }

    public function getDkOrvOdevzdano(): ?string
    {
        return $this->dkOrvOdevzdano;
    }

    public function getDkRzDruh(): ?string
    {
        return $this->dkRzDruh;
    }

    public function getDkRzJkVydana(): ?string
    {
        return $this->dkRzJkVydana;
    }

    public function getDkRzKeSkartaci(): ?string
    {
        return $this->dkRzKeSkartaci;
    }

    public function getDkRzOdevzdano(): ?string
    {
        return $this->dkRzOdevzdano;
    }

    public function getDkRzZadrzena(): ?string
    {
        return $this->dkRzZadrzena;
    }

    public function getDkZarazeniVozidla(): ?string
    {
        return $this->dkZarazeniVozidla;
    }

    public function getDkPravidelnaTechnickaProhlidkaDo(): ?string
    {
        return $this->dkPravidelnaTechnickaProhlidkaDo;
    }

    public function getDkPredRegistraciProhlidkaDne(): ?string
    {
        return $this->dkPredRegistraciProhlidkaDne;
    }

    public function getDkPredSchvalenimProhlidkaDne(): ?string
    {
        return $this->dkPredSchvalenimProhlidkaDne;
    }

    public function getDkEvidencniProhlidkaDne(): ?string
    {
        return $this->dkEvidencniProhlidkaDne;
    }

    public function getDkHistorickeVozidloProhlidkaDne(): ?string
    {
        return $this->dkHistorickeVozidloProhlidkaDne;
    }

    public function getDkStatusNazev(): ?string
    {
        return $this->dkStatusNazev;
    }

    public function getDkPocetVlastniku(): ?string
    {
        return $this->dkPocetVlastniku;
    }

    public function getDkPocetProvozovatelu(): ?string
    {
        return $this->dkPocetProvozovatelu;
    }
}
