<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\CustomerCarBrandEnum;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_customer_car')]
#[ORM\Index(name: 'idx_license_plate', columns: ['license_plate'])]
#[ORM\Index(name: 'idx_vin', columns: ['vin'])]
#[ORM\Index(name: 'idx_brand', columns: ['brand'])]
#[ORM\Entity(repositoryClass: CustomerCarRepository::class)]
class CustomerCar
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true)]
    private string $licensePlate;

    #[ORM\Column(type: Types::STRING, enumType: CustomerCarBrandEnum::class, length: 64, nullable: true)]
    private ?CustomerCarBrandEnum $brand = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $model = null;

    #[Assert\Length(max: 17)]
    #[ORM\Column(length: 17, unique: true, nullable: true)]
    private ?string $vin = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cars')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, CustomerCarHistory> */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: CustomerCarHistory::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $history;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'customerCar', targetEntity: Order::class)]
    private Collection $orders;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkDatumPrvniRegistrace = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkDatumPrvniRegistraceVCr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkCisloTypovehoSchvaleni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHomologaceEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloDruh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloDruh2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkKategorie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkTovarniZnacka = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkTyp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVarianta = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVerze = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkObchodniOznaceni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloVyrobce = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkMotorVyrobce = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkMotorTyp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkMotorMaxVykon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPalivo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkMotorZdvihObjem = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloElektricke = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloHybridni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloHybridniTrida = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseEHKOSNEHSES = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmisniUroven = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseKSA = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseCO2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseCO2Specificke = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseSnizeniNedc = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEmiseSnizeniWltp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkSpotrebaMetodika = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkSpotrebaNa100Km = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkSpotreba = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkSpotrebaEl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkDojezdZR = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVyrobceKaroserie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkKaroserieDruh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkKaroserieVyrobniCislo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloKaroserieBarva = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloKaroserieBarvaDoplnkova = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloKaroserieMist = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRozmery = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRozmeryRozvor = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRozchod = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiProvozni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiPripPov = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiPripPovN = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiPripPovBrzdenePV = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiPripPovNebrzdenePV = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiPripPovJS = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostiTestWltp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHmotnostUzitecneZatizeniPrumer = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloSpojZarizNazev = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkNapravyPocetDruh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkNapravyPneuRafky = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHlukStojiciOtacky = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHlukJizda = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkNejvyssiRychlost = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPomerVykonHmotnost = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkInovativniTechnologie = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkStupenDokonceni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkFaktorOdchylkyDe = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkFaktorVerifikaceVf = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkVozidloUcel = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkDalsiZaznamy = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkAlternativniProvedeni = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkCisloTp = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkCisloOrv = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkOrvZadrzeno = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkOrvKeSkartaci = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkOrvOdevzdano = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRzDruh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRzJkVydana = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRzKeSkartaci = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRzOdevzdano = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkRzZadrzena = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkZarazeniVozidla = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPravidelnaTechnickaProhlidkaDo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPredRegistraciProhlidkaDne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPredSchvalenimProhlidkaDne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkEvidencniProhlidkaDne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkHistorickeVozidloProhlidkaDne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkStatusNazev = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPocetVlastniku = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dkPocetProvozovatelu = null;

    public function __construct(
        Uuid $id,
        string $licensePlate,
        DateTimeImmutable $createdAt,
        ?CustomerCarBrandEnum $brand = null,
        ?string $model = null,
        ?string $vin = null,
        ?User $user = null,
    ) {
        $this->id = $id;
        $this->licensePlate = $licensePlate;
        $this->brand = $brand;
        $this->model = $model;
        $this->vin = $vin;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->history = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getBrand(): ?CustomerCarBrandEnum
    {
        return $this->brand;
    }

    public function setBrand(?CustomerCarBrandEnum $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, CustomerCarHistory>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(CustomerCarHistory $history): self
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function applyDataCubeData(array $data): self
    {
        $map = [
            'DatumPrvniRegistrace' => 'dkDatumPrvniRegistrace',
            'DatumPrvniRegistraceVCr' => 'dkDatumPrvniRegistraceVCr',
            'CisloTypovehoSchvaleni' => 'dkCisloTypovehoSchvaleni',
            'HomologaceEs' => 'dkHomologaceEs',
            'VozidloDruh' => 'dkVozidloDruh',
            'VozidloDruh2' => 'dkVozidloDruh2',
            'Kategorie' => 'dkKategorie',
            'TovarniZnacka' => 'dkTovarniZnacka',
            'Typ' => 'dkTyp',
            'Varianta' => 'dkVarianta',
            'Verze' => 'dkVerze',
            'VIN' => 'dkVin',
            'ObchodniOznaceni' => 'dkObchodniOznaceni',
            'VozidloVyrobce' => 'dkVozidloVyrobce',
            'MotorVyrobce' => 'dkMotorVyrobce',
            'MotorTyp' => 'dkMotorTyp',
            'MotorMaxVykon' => 'dkMotorMaxVykon',
            'Palivo' => 'dkPalivo',
            'MotorZdvihObjem' => 'dkMotorZdvihObjem',
            'VozidloElektricke' => 'dkVozidloElektricke',
            'VozidloHybridni' => 'dkVozidloHybridni',
            'VozidloHybridniTrida' => 'dkVozidloHybridniTrida',
            'EmiseEHKOSNEHSES' => 'dkEmiseEHKOSNEHSES',
            'EmisniUroven' => 'dkEmisniUroven',
            'EmiseKSA' => 'dkEmiseKSA',
            'EmiseCO2' => 'dkEmiseCO2',
            'EmiseCO2Specificke' => 'dkEmiseCO2Specificke',
            'EmiseSnizeniNedc' => 'dkEmiseSnizeniNedc',
            'EmiseSnizeniWltp' => 'dkEmiseSnizeniWltp',
            'SpotrebaMetodika' => 'dkSpotrebaMetodika',
            'SpotrebaNa100Km' => 'dkSpotrebaNa100Km',
            'Spotreba' => 'dkSpotreba',
            'SpotrebaEl' => 'dkSpotrebaEl',
            'DojezdZR' => 'dkDojezdZR',
            'VyrobceKaroserie' => 'dkVyrobceKaroserie',
            'KaroserieDruh' => 'dkKaroserieDruh',
            'KaroserieVyrobniCislo' => 'dkKaroserieVyrobniCislo',
            'VozidloKaroserieBarva' => 'dkVozidloKaroserieBarva',
            'VozidloKaroserieBarvaDoplnkova' => 'dkVozidloKaroserieBarvaDoplnkova',
            'VozidloKaroserieMist' => 'dkVozidloKaroserieMist',
            'Rozmery' => 'dkRozmery',
            'RozmeryRozvor' => 'dkRozmeryRozvor',
            'Rozchod' => 'dkRozchod',
            'HmotnostiProvozni' => 'dkHmotnostiProvozni',
            'HmotnostiPripPov' => 'dkHmotnostiPripPov',
            'HmotnostiPripPovN' => 'dkHmotnostiPripPovN',
            'HmotnostiPripPovBrzdenePV' => 'dkHmotnostiPripPovBrzdenePV',
            'HmotnostiPripPovNebrzdenePV' => 'dkHmotnostiPripPovNebrzdenePV',
            'HmotnostiPripPovJS' => 'dkHmotnostiPripPovJS',
            'HmotnostiTestWltp' => 'dkHmotnostiTestWltp',
            'HmotnostUzitecneZatizeniPrumer' => 'dkHmotnostUzitecneZatizeniPrumer',
            'VozidloSpojZarizNazev' => 'dkVozidloSpojZarizNazev',
            'NapravyPocetDruh' => 'dkNapravyPocetDruh',
            'NapravyPneuRafky' => 'dkNapravyPneuRafky',
            'HlukStojiciOtacky' => 'dkHlukStojiciOtacky',
            'HlukJizda' => 'dkHlukJizda',
            'NejvyssiRychlost' => 'dkNejvyssiRychlost',
            'PomerVykonHmotnost' => 'dkPomerVykonHmotnost',
            'InovativniTechnologie' => 'dkInovativniTechnologie',
            'StupenDokonceni' => 'dkStupenDokonceni',
            'FaktorOdchylkyDe' => 'dkFaktorOdchylkyDe',
            'FaktorVerifikaceVf' => 'dkFaktorVerifikaceVf',
            'VozidloUcel' => 'dkVozidloUcel',
            'DalsiZaznamy' => 'dkDalsiZaznamy',
            'AlternativniProvedeni' => 'dkAlternativniProvedeni',
            'CisloTp' => 'dkCisloTp',
            'CisloOrv' => 'dkCisloOrv',
            'OrvZadrzeno' => 'dkOrvZadrzeno',
            'OrvKeSkartaci' => 'dkOrvKeSkartaci',
            'OrvOdevzdano' => 'dkOrvOdevzdano',
            'RzDruh' => 'dkRzDruh',
            'RzJkVydana' => 'dkRzJkVydana',
            'RzKeSkartaci' => 'dkRzKeSkartaci',
            'RzOdevzdano' => 'dkRzOdevzdano',
            'RzZadrzena' => 'dkRzZadrzena',
            'ZarazeniVozidla' => 'dkZarazeniVozidla',
            'PravidelnaTechnickaProhlidkaDo' => 'dkPravidelnaTechnickaProhlidkaDo',
            'PredRegistraciProhlidkaDne' => 'dkPredRegistraciProhlidkaDne',
            'PredSchvalenimProhlidkaDne' => 'dkPredSchvalenimProhlidkaDne',
            'EvidencniProhlidkaDne' => 'dkEvidencniProhlidkaDne',
            'HistorickeVozidloProhlidkaDne' => 'dkHistorickeVozidloProhlidkaDne',
            'StatusNazev' => 'dkStatusNazev',
            'PocetVlastniku' => 'dkPocetVlastniku',
            'PocetProvozovatelu' => 'dkPocetProvozovatelu',
        ];

        foreach ($map as $key => $property) {
            if (!array_key_exists($key, $data)) {
                continue;
            }

            $this->{$property} = $this->normalizeDataCubeValue($data[$key]);
        }

        return $this;
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

    private function normalizeDataCubeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
