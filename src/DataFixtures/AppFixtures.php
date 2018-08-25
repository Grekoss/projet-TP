<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Faker;
use App\DataFixtures\Faker\DataProvider;
use bheller\ImagesGenerator\ImagesGeneratorProvider;

Use App\Entity\Comment;
Use App\Entity\Event;
use App\Entity\EventReporting;
use App\Entity\UserReporting;
use App\Entity\Tag;
use App\Entity\Department;
use App\Entity\Genre;
use App\Entity\Link;
use App\Entity\Region;
use App\Entity\RelationShip;
use App\Entity\User;
use App\Service\Slugger;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Role;
use App\Entity\Participation;
use App\Entity\Visibility;



class AppFixtures extends Fixture
{
    private $slugger;
    private $listDepartments = array();
    private $listUsers = array();
    private $listTags = array();
    private $listEvents = array();
    private $listLink = array();
    private $listVisibilities = array();
    private $encoder;
                       
    public function __construct(Slugger $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager )
    {
        $generator = Faker\Factory::create('fr_FR');

        $generator->addProvider(new DataProvider($generator));
        $generator->addProvider(new ImagesGeneratorProvider($generator));
        
        $populator = new Faker\ORM\Doctrine\Populator($generator, $manager);

        // Création des départements et régions françaises
        // Les régions
        $auvergneRhoneAlpes = New Region();
        $auvergneRhoneAlpes->setName('Auvergne-Rhône-Alpes');
        $manager->persist($auvergneRhoneAlpes);

        $bourgogneFrancheComte = new Region();
        $bourgogneFrancheComte->setName('Bourgogne-Franche-Comté');
        $manager->persist($bourgogneFrancheComte);

        $bretagne = new Region();
        $bretagne->setName('Bretagne');
        $manager->persist($bretagne);

        $centreValdeLoire = new Region();
        $centreValdeLoire->setName('Centre-Val de Loire');
        $manager->persist($centreValdeLoire);

        $corse = new Region();
        $corse->setName('Corse');
        $manager->persist($corse);

        $DomTomGuadeloupe = new Region();
        $DomTomGuadeloupe->setName('Guadeloupe');
        $manager->persist($DomTomGuadeloupe);

        $DomTomMartinique = new Region();
        $DomTomMartinique->setName('Martinique');
        $manager->persist($DomTomMartinique);

        $DomTomGuyane = new Region();
        $DomTomGuyane->setName('Guyane');
        $manager->persist($DomTomGuyane);

        $DomTomReunion = new Region();
        $DomTomReunion->setName('La Réunion');
        $manager->persist($DomTomReunion);

        $DomTomMayotte = new Region();
        $DomTomMayotte->setName('Mayotte');
        $manager->persist($DomTomMayotte);

        $grandEst = new Region();
        $grandEst->setName('Grand Est');
        $manager->persist($grandEst);

        $hautsDeFrance = New Region();
        $hautsDeFrance->setName('Hauts-de-France');
        $manager->persist($hautsDeFrance);

        $ileDeFrance = new Region();
        $ileDeFrance->setName('Ile-de-France');
        $manager->persist($ileDeFrance);

        $nouvelleAquitaine = new Region();
        $nouvelleAquitaine->setName('Nouvelle-Aquitaine');
        $manager->persist($nouvelleAquitaine);

        $normandie = new Region();
        $normandie->setName('Normandie');
        $manager->persist($normandie);

        $occitanie = new Region();
        $occitanie->setName('Occitanie');
        $manager->persist($occitanie);

        $paysDeLaLoire = new Region();
        $paysDeLaLoire->setName('Pays de la Loire');
        $manager->persist($paysDeLaLoire);

        $provenceAlpesCotedAzur = new Region();
        $provenceAlpesCotedAzur->setName('Provence-Alpes-Côte d\'Azur');
        $manager->persist($provenceAlpesCotedAzur);

        $manager->flush();

        // Les départements
        $ain = new Department();
        $ain->setName('Ain')
            ->setNumber('01')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($ain);
        $listDepartments[] = $ain;

        $aisne = new Department();
        $aisne->setName('Aisne')
            ->setNumber('02')
            ->setRegion($hautsDeFrance);
        $manager->persist($aisne);
        $listDepartments[] = $aisne;

        $allier = new Department();
        $allier->setName('Allier')
            ->setNumber('03')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($allier);
        $listDepartments[] = $allier;

        $alpesDeHauteProvence = new Department();
        $alpesDeHauteProvence->setName('Alpes-de-Haute-Provence')
            ->setNumber('04')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($alpesDeHauteProvence);
        $listDepartments[] = $alpesDeHauteProvence;

        $hautesAlpes = new Department();
        $hautesAlpes->setName('Hautes-Alpes')
            ->setNumber('05')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($hautesAlpes);
        $listDepartments[] = $hautesAlpes;

        $alpesMaritimes = new Department();
        $alpesMaritimes->setName('Alpes-Maritimes')
            ->setNumber('06')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($alpesMaritimes);
        $listDepartments[] = $alpesMaritimes;

        $ardeche = new Department();
        $ardeche->setName('Ardèche')
            ->setNumber('07')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($ardeche);
        $listDepartments[] = $ardeche;
         
        $ardennes = new Department();
        $ardennes->setName('Ardennes')
            ->setNumber('08')
            ->setRegion($grandEst);
        $manager->persist($ardennes);
        $listDepartments[] = $ardennes;
        
        $ariege = new Department();
        $ariege->setName('Ariège')
            ->setNumber('09')
            ->setRegion($occitanie);
        $manager->persist($ariege);
        $listDepartments[] = $ariege;
        
        $aube = new Department();
        $aube->setName('Aube')
            ->setNumber('10')
            ->setRegion($grandEst);
        $manager->persist($aube);
        $listDepartments[] = $aube;
        
        $aude = new Department();
        $aude->setName('Aude')
            ->setNumber('11')
            ->setRegion($occitanie);
        $manager->persist($aude);
        $listDepartments[] = $aude;
        
        $aveyron = new Department();
        $aveyron->setName('Aveyron')
            ->setNumber('12')
            ->setRegion($occitanie);
        $manager->persist($aveyron);
        $listDepartments[] = $aveyron;
        
        $bouchesDuRhone = new Department();
        $bouchesDuRhone->setName('Bouches-du-Rhône')
            ->setNumber('13')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($bouchesDuRhone);
        $listDepartments[] = $bouchesDuRhone;
        
        $calvados = new Department();
        $calvados->setName('Calvados')
            ->setNumber('14')
            ->setRegion($normandie);
        $manager->persist($calvados);
        $listDepartments[] = $calvados;
        
        $cantal = new Department();
        $cantal->setName('Cantal')
            ->setNumber('15')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($cantal);
        $listDepartments[] = $cantal;
        
        $charente = new Department();
        $charente->setName('Charente')
            ->setNumber('16')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($charente);
        $listDepartments[] = $charente;
        
        $charenteMaritime = new Department();
        $charenteMaritime->setName('Charente-Maritime')
            ->setNumber('17')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($charenteMaritime);
        $listDepartments[] = $charenteMaritime;
        
        $cher = new Department();
        $cher->setName('Cher')
            ->setNumber('18')
            ->setRegion($centreValdeLoire);
        $manager->persist($cher);
        $listDepartments[] = $cher;
        
        $correze = new Department();
        $correze->setName('Corrèze')
            ->setNumber('19')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($correze);
        $listDepartments[] = $correze;
        
        $corseduSud = new Department();
        $corseduSud->setName('Corse-du-Sud')
            ->setNumber('2A')
            ->setRegion($corse);
        $manager->persist($corseduSud);
        $listDepartments[] = $corseduSud;
         
        $hauteCorse = new Department();
        $hauteCorse->setName('haute-Corse')
            ->setNumber('2B')
            ->setRegion($corse);
        $manager->persist($hauteCorse);
        $listDepartments[] = $hauteCorse;
        
        $cotedOr = new Department();
        $cotedOr->setName('Côte d\'Or')
            ->setNumber('21')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($cotedOr);
        $listDepartments[] = $cotedOr;
         
        $cotesdArmor = new Department();
        $cotesdArmor->setName('Côtes-d\'Amor')
            ->setNumber('22')
            ->setRegion($bretagne);
        $manager->persist($cotesdArmor);
        $listDepartments[] = $cotesdArmor;
        
        $creuse = new Department();
        $creuse->setName('Creuse')
            ->setNumber('23')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($creuse);
        $listDepartments[] = $creuse;

        $dordogne = new Department();
        $dordogne->setName('Dordogne')
            ->setNumber('24')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($dordogne);
        $listDepartments[] = $dordogne;
        
        $doubs = new Department();
        $doubs->setName('Doubs')
            ->setNumber('25')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($doubs);
        $listDepartments[] = $doubs;
        
        $drome = new Department();
        $drome->setName('Drôme')
            ->setNumber('26')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($drome);
        $listDepartments[] = $drome;
        
        $eure = new Department();
        $eure->setName('Eure')
            ->setNumber('27')
            ->setRegion($normandie);
        $manager->persist($eure);
        $listDepartments[] = $eure;
        
        $eureEtLoir = new Department();
        $eureEtLoir->setName('Eure-et-Loir')
            ->setNumber('28')
            ->setRegion($centreValdeLoire);
        $manager->persist($eureEtLoir);
        $listDepartments[] = $eureEtLoir;
        
        $finistere = new Department();
        $finistere->setName('Finistère')
            ->setNumber('29')
            ->setRegion($bretagne);
        $manager->persist($finistere);
        $listDepartments[] = $finistere;
        
        $gard = new Department();
        $gard->setName('Gard')
            ->setNumber('30')
            ->setRegion($occitanie);
        $manager->persist($gard);
        $listDepartments[] = $gard;
        
        $hauteGaronne = new Department();
        $hauteGaronne->setName('Haute-Garonne')
            ->setNumber('31')
            ->setRegion($occitanie);
        $manager->persist($hauteGaronne);
        $listDepartments[] = $hauteGaronne;

        $gers = new Department();
        $gers->setName('Gers')
            ->setNumber('32')
            ->setRegion($occitanie);
        $manager->persist($gers);
        $listDepartments[] = $gers;

        $gironde = new Department();
        $gironde->setName('Gironde')
            ->setNumber('33')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($gironde);
        $listDepartments[] = $gironde;

        $herault = new Department();
        $herault->setName('Hérault')
            ->setNumber('34')
            ->setRegion($occitanie);
        $manager->persist($herault);
        $listDepartments[] = $herault;

        $illeEtVilaine = new Department();
        $illeEtVilaine->setName('Ille-et-Vilaine')
            ->setNumber('35')
            ->setRegion($bretagne);
        $manager->persist($illeEtVilaine);
        $listDepartments[] = $illeEtVilaine;

        $indre = new Department();
        $indre->setName('Indre')
            ->setNumber('36')
            ->setRegion($centreValdeLoire);
        $manager->persist($indre);
        $listDepartments[] = $indre;

        $indreEtLoir = new Department();
        $indreEtLoir->setName('Indre-et-Loire')
            ->setNumber('37')
            ->setRegion($centreValdeLoire);
        $manager->persist($indreEtLoir);
        $listDepartments[] = $indreEtLoir;

        $isere = new Department();
        $isere->setName('Isère')
            ->setNumber('38')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($isere);
        $listDepartments[] = $isere;

        $jura = new Department();
        $jura->setName('Jura')
            ->setNumber('39')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($jura);
        $listDepartments[] = $jura;

        $landes = new Department();
        $landes->setName('Landes')
            ->setNumber('40')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($landes);
        $listDepartments[] = $landes;
        
        $loiretCher = new Department();
        $loiretCher->setName('Loir-et-Cher')
            ->setNumber('41')
            ->setRegion($centreValdeLoire);
        $manager->persist($loiretCher);
        $listDepartments[] = $loiretCher;

        $loire = new Department();
        $loire->setName('Loire')
            ->setNumber('42')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($loire);
        $listDepartments[] = $loire;

        $hauteLoire = new Department();
        $hauteLoire->setName('Haute-Loire')
            ->setNumber('43')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($hauteLoire);
        $listDepartments[] = $hauteLoire;

        $loireAtlantique = new Department();
        $loireAtlantique->setName('Loire-Atlantique')
            ->setNumber('44')
            ->setRegion($paysDeLaLoire);
        $manager->persist($loireAtlantique);
        $listDepartments[] = $loireAtlantique;

        $loiret = new Department();
        $loiret->setName('Loiret')
            ->setNumber('45')
            ->setRegion($centreValdeLoire);
        $manager->persist($loiret);
        $listDepartments[] = $loiret;

        $lot = new Department();
        $lot->setName('Lot')
            ->setNumber('46')
            ->setRegion($occitanie);
        $manager->persist($lot);
        $listDepartments[] = $lot;

        $lotEtGaronne = new Department();
        $lotEtGaronne->setName('Lot-et-Garonne')
            ->setNumber('47')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($lotEtGaronne);
        $listDepartments[] = $lotEtGaronne;

        $lozere = new Department();
        $lozere->setName('Lozère')
            ->setNumber('48')
            ->setRegion($occitanie);
        $manager->persist($lozere);
        $listDepartments[] = $lozere;

        $maineEtLoire = new Department();
        $maineEtLoire->setName('Maine-et-Loire')
            ->setNumber('49')
            ->setRegion($paysDeLaLoire);
        $manager->persist($maineEtLoire);
        $listDepartments[] = $maineEtLoire;

        $manche = new Department();
        $manche->setName('Manche')
            ->setNumber('50')
            ->setRegion($normandie);
        $manager->persist($manche);
        $listDepartments[] = $manche;

        $marne = new Department();
        $marne->setName('Marne')
            ->setNumber('51')
            ->setRegion($grandEst);
        $manager->persist($marne);
        $listDepartments[] = $marne;

        $hauteMarne = new Department();
        $hauteMarne->setName('Haute-Marne')
            ->setNumber('52')
            ->setRegion($grandEst);
        $manager->persist($hauteMarne);
        $listDepartments[] = $hauteMarne;

        $mayenne = new Department();
        $mayenne->setName('Mayenne')
            ->setNumber('53')
            ->setRegion($paysDeLaLoire);
        $manager->persist($mayenne);
        $listDepartments[] = $mayenne;

        $meurtheEtMoselle = new Department();
        $meurtheEtMoselle->setName('Meurthe-et-Moselle')
            ->setNumber('54')
            ->setRegion($grandEst);
        $manager->persist($meurtheEtMoselle);
        $listDepartments[] = $meurtheEtMoselle;

        $meuse = new Department();
        $meuse->setName('Meuse')
            ->setNumber('55')
            ->setRegion($grandEst);
        $manager->persist($meuse);
        $listDepartments[] = $meuse;

        $morbihan = new Department();
        $morbihan->setName('Morbihan')
            ->setNumber('56')
            ->setRegion($bretagne);
        $manager->persist($morbihan);
        $listDepartments[] = $morbihan;

        $moselle = new Department();
        $moselle->setName('Moselle')
            ->setNumber('57')
            ->setRegion($grandEst);
        $manager->persist($moselle);
        $listDepartments[] = $moselle;

        $nievre = new Department();
        $nievre->setName('Nièvre')
            ->setNumber('58')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($nievre);
        $listDepartments[] = $nievre;

        $nord = new Department();
        $nord->setName('Nord')
            ->setNumber('59')
            ->setRegion($hautsDeFrance);
        $manager->persist($nord);
        $listDepartments[] = $nord;

        $oise = new Department();
        $oise->setName('Oise')
            ->setNumber('60')
            ->setRegion($hautsDeFrance);
        $manager->persist($oise);
        $listDepartments[] = $oise;

        $orne = new Department();
        $orne->setName('Orne')
            ->setNumber('61')
            ->setRegion($normandie);
        $manager->persist($orne);
        $listDepartments[] = $orne;

        $pasDeCalais = new Department();
        $pasDeCalais->setName('Pas-de-Calais')
            ->setNumber('62')
            ->setRegion($hautsDeFrance);
        $manager->persist($pasDeCalais);
        $listDepartments[] = $pasDeCalais;

        $puyDeDome = new Department();
        $puyDeDome->setName('Puy-de-Dôme')
            ->setNumber('63')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($puyDeDome);
        $listDepartments[] = $puyDeDome;

        $pyreneesAtlantiques = new Department();
        $pyreneesAtlantiques->setName('Pyrénées-Atlantiques')
            ->setNumber('64')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($pyreneesAtlantiques);
        $listDepartments[] = $pyreneesAtlantiques;

        $hautesPyrenees = new Department();
        $hautesPyrenees->setName('Hautes-Pyrénées')
            ->setNumber('65')
            ->setRegion($occitanie);
        $manager->persist($hautesPyrenees);
        $listDepartments[] = $hautesPyrenees;

        $pyreneesOrientales = new Department();
        $pyreneesOrientales->setName('Pyrénées-Orientales')
            ->setNumber('66')
            ->setRegion($occitanie);
        $manager->persist($pyreneesOrientales);
        $listDepartments[] = $pyreneesOrientales;

        $basRhin = new Department();
        $basRhin->setName('Bas-Rhin')
            ->setNumber('67')
            ->setRegion($grandEst);
        $manager->persist($basRhin);
        $listDepartments[] = $basRhin;

        $hautRhin = new Department();
        $hautRhin->setName('Haut-Rhin')
            ->setNumber('68')
            ->setRegion($grandEst);
        $manager->persist($hautRhin);
        $listDepartments[] = $hautRhin;

        $rhone = new Department();
        $rhone->setName('Rhône')
            ->setNumber('69')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($rhone);
        $listDepartments[] = $rhone;

        $hauteSaone = new Department();
        $hauteSaone->setName('Haute-Saône')
            ->setNumber('70')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($hauteSaone);
        $listDepartments[] = $hauteSaone;

        $saoneEtLoire = new Department();
        $saoneEtLoire->setName('Saône-et-Loire')
            ->setNumber('71')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($saoneEtLoire);
        $listDepartments[] = $saoneEtLoire;

        $sarthe = new Department();
        $sarthe->setName('Sarthe')
            ->setNumber('72')
            ->setRegion($paysDeLaLoire);
        $manager->persist($sarthe);
        $listDepartments[] = $sarthe;

        $savoie = new Department();
        $savoie->setName('Savoie')
            ->setNumber('73')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($savoie);
        $listDepartments[] = $savoie;

        $hauteSavoie = new Department();
        $hauteSavoie->setName('Haute-Savoie')
            ->setNumber('74')
            ->setRegion($auvergneRhoneAlpes);
        $manager->persist($hauteSavoie);
        $listDepartments[] = $hauteSavoie;

        $paris = new Department();
        $paris->setName('Paris')
            ->setNumber('75')
            ->setRegion($ileDeFrance);
        $manager->persist($paris);
        $listDepartments[] = $paris;

        $seineMaritime = new Department();
        $seineMaritime->setName('Seine-Maritime')
            ->setNumber('76')
            ->setRegion($normandie);
        $manager->persist($seineMaritime);
        $listDepartments[] = $seineMaritime;

        $seineEtMarne = new Department();
        $seineEtMarne->setName('Seine-et-Marne')
            ->setNumber('77')
            ->setRegion($ileDeFrance);
        $manager->persist($seineEtMarne);
        $listDepartments[] = $seineEtMarne;

        $yvelines = new Department();
        $yvelines->setName('Yvelines')
            ->setNumber('78')
            ->setRegion($ileDeFrance);
        $manager->persist($yvelines);
        $listDepartments[] = $yvelines;

        $deuxSevres = new Department();
        $deuxSevres->setName('Deux-Sèvres')
            ->setNumber('79')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($deuxSevres);
        $listDepartments[] = $deuxSevres;

        $somme = new Department();
        $somme->setName('Somme')
            ->setNumber('80')
            ->setRegion($hautsDeFrance);
        $manager->persist($somme);
        $listDepartments[] = $somme;

        $tarn = new Department();
        $tarn->setName('tarn')
            ->setNumber('81')
            ->setRegion($occitanie);
        $manager->persist($tarn);
        $listDepartments[] = $tarn;

        $tarnEtGaronne = new Department();
        $tarnEtGaronne->setName('Tarn-et-Garonne')
            ->setNumber('82')
            ->setRegion($occitanie);
        $manager->persist($tarnEtGaronne);
        $listDepartments[] = $tarnEtGaronne;

        $var = new Department();
        $var->setName('Var')
            ->setNumber('83')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($var);
        $listDepartments[] = $var;

        $vaucluse = new Department();
        $vaucluse->setName('Vaucluse')
            ->setNumber('84')
            ->setRegion($provenceAlpesCotedAzur);
        $manager->persist($vaucluse);
        $listDepartments[] = $vaucluse;

        $vendee = new Department();
        $vendee->setName('Vendée')
            ->setNumber('85')
            ->setRegion($paysDeLaLoire);
        $manager->persist($vendee);
        $listDepartments[] = $vendee;

        $vienne = new Department();
        $vienne->setName('Vienne')
            ->setNumber('86')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($vienne);
        $listDepartments[] = $vienne;

        $hauteVienne = new Department();
        $hauteVienne->setName('Haute-Vienne')
            ->setNumber('87')
            ->setRegion($nouvelleAquitaine);
        $manager->persist($hauteVienne);
        $listDepartments[] = $hauteVienne;

        $vosges = new Department();
        $vosges->setName('Vosges')
            ->setNumber('88')
            ->setRegion($grandEst);
        $manager->persist($vosges);
        $listDepartments[] = $vosges;

        $yonne = new Department();
        $yonne->setName('Yonne')
            ->setNumber('89')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($yonne);
        $listDepartments[] = $yonne;

        $territoireDeBelfort = new Department();
        $territoireDeBelfort->setName('Territoire de Belfort')
            ->setNumber('90')
            ->setRegion($bourgogneFrancheComte);
        $manager->persist($territoireDeBelfort);
        $listDepartments[] = $territoireDeBelfort;

        $essonne = new Department();
        $essonne->setName('Essonne')
            ->setNumber('91')
            ->setRegion($grandEst);
        $manager->persist($essonne);
        $listDepartments[] = $essonne;

        $hautsDeSeine = new Department();
        $hautsDeSeine->setName('Hauts-de-Seine')
            ->setNumber('92')
            ->setRegion($ileDeFrance);
        $manager->persist($hautsDeSeine);
        $listDepartments[] = $hautsDeSeine;

        $seineSaintDenis = new Department();
        $seineSaintDenis->setName('Seine-Saint-Denis')
            ->setNumber('93')
            ->setRegion($ileDeFrance);
        $manager->persist($seineSaintDenis);
        $listDepartments[] = $seineSaintDenis;

        $valDeMarne = new Department();
        $valDeMarne->setName('Val-de-Marne')
            ->setNumber('94')
            ->setRegion($ileDeFrance);
        $manager->persist($valDeMarne);
        $listDepartments[] = $valDeMarne;

        $valdOise = new Department();
        $valdOise->setName('Val-d\'Oise')
            ->setNumber('95')
            ->setRegion($ileDeFrance);
        $manager->persist($valdOise);
        $listDepartments[] = $valdOise;

        $guadeloupe = new Department();
        $guadeloupe->setName('Guadeloupe')
            ->setNumber('971')
            ->setRegion($DomTomGuadeloupe);
        $manager->persist($guadeloupe);
        $listDepartments[] = $guadeloupe;

        $martinique = new Department();
        $martinique->setName('Martinique')
            ->setNumber('972')
            ->setRegion($DomTomMartinique);
        $manager->persist($martinique);
        $listDepartments[] = $martinique;

        $guyane = new Department();
        $guyane->setName('Guyane')
            ->setNumber('973')
            ->setRegion($DomTomGuyane);
        $manager->persist($guyane);
        $listDepartments[] = $guyane;

        $reunion = new Department();
        $reunion->setName('La Réunion')
            ->setNumber('974')
            ->setRegion($DomTomReunion);
        $manager->persist($reunion);
        $listDepartments[] = $reunion;

        $mayotte = new Department();
        $mayotte->setName('Mayotte')
            ->setNumber('976')
            ->setRegion($DomTomMayotte);
        $manager->persist($mayotte);
        $listDepartments[] = $mayotte;

        $manager->flush();

        // Création des Roles
        $roleAdmin = new Role();
        $roleAdmin->setName('ROLE_ADMIN');
        $roleAdmin->setLabel('Administrateur');
        
        $roleModerator = new Role();
        $roleModerator->setName('ROLE_MODERATOR');
        $roleModerator->setLabel('Modérateur');
        
        $roleUser = new Role();
        $roleUser->setName('ROLE_USER');
        $roleUser->setLabel('Membre');
        
        $manager->persist($roleAdmin);
        $manager->persist($roleModerator);
        $manager->persist($roleUser);
       // $manager->flush();
        

        // Création des genres (F/H)
        $man = new Genre();
        $man->setName('Homme');
        $woman = new Genre();
        $woman->setName('Femme');
        $manager->persist($man);
        $manager->persist($woman);
        $manager->flush();

        //User Admin
        $admin = new User();
        $admin ->setGenre($man)
        ->setUsername('admin')
        ->setLastname('admin')
        ->setFirstname('admin')
        ->setEmail('admin@fakelist.fr')
        ->setPassword($this->encoder->encodePassword($admin,'admin'))
        ->setRole($roleAdmin)
        ->setCity($generator->city)
        ->setZipcode($generator->postcode)
        ->setDepartment($listDepartments[array_rand($listDepartments)])
        ->setBirthDate($generator->dateTime($max = '-18 year'))
        ->setSlug($this->slugger->slugify($admin->getUsername()))
        ->setAvatar($generator->imageGenerator('public/assets/images/users/', 165, 165, 'jpeg', false, $admin->getUsername(), '#000000', '#FF0000'));
        $manager->persist($admin);
        $manager->flush();

        //User Moderator
        $modo = new User();
        $modo ->setGenre($woman)
        ->setUsername('modo')
        ->setLastname('modo')
        ->setFirstname('modo')
        ->setEmail('modo@fakelist.fr')
        ->setPassword($this->encoder->encodePassword($modo,'modo'))
        ->setRole($roleModerator)
        ->setCity($generator->city)
        ->setZipcode($generator->postcode)
        ->setDepartment($listDepartments[array_rand($listDepartments)])
        ->setBirthDate($generator->dateTime($max = '-18 year'))
        ->setSlug($this->slugger->slugify($modo->getUsername()))
        ->setAvatar($generator->imageGenerator('public/assets/images/users/', 165, 165, 'jpeg', false, $modo->getUsername(), '#000000', '#FF0000'));
        $manager->persist($modo);
        $manager->flush();
        
        //25 users (women); password = user
        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user
                ->setGenre($woman)
                ->setUsername($generator->unique()->userName)
                ->setLastname($generator->lastname())
                ->setFirstname($generator->firstName($gender = 'female'))
                ->setEmail($user->getUsername().'@fakelist.fr')
                ->setPassword($this->encoder->encodePassword($user,'user'))
                ->setRole($roleUser)
                ->setCity($generator->city)
                ->setZipcode($generator->postcode)
                ->setDepartment($listDepartments[array_rand($listDepartments)])
                ->setBirthDate($generator->dateTime($max = '-18 year'))
                ->setSlug($this->slugger->slugify($user->getUsername()))
                ->setAvatar($generator->imageGenerator('public/assets/images/users/', 165, 165, 'jpeg', false, $user->getUsername(), $generator->hexcolor(), '#000000'));
            $manager->persist($user);
            $manager->flush();
            $listUsers[] = $user;
        }

        //25 users (men); password = user
        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user
                ->setGenre($man)
                ->setUsername($generator->unique()->userName)
                ->setLastname($generator->lastname())
                ->setFirstname($generator->firstName($gender = 'male'))
                ->setEmail($user->getUsername() . '@fakelist.fr')
                ->setPassword($this->encoder->encodePassword($user, 'user'))
                ->setRole($roleUser)
                ->setCity($generator->city)
                ->setZipcode($generator->postcode)
                ->setDepartment($listDepartments[array_rand($listDepartments)])
                ->setBirthDate($generator->dateTime($max = '-18 year'))
                ->setSlug($this->slugger->slugify($user->getUsername()))
                ->setAvatar($generator->imageGenerator('public/assets/images/users/', 165, 165, 'jpeg', false, $user->getUsername(), $generator->hexcolor(), '#000000'));
            $manager->persist($user);
            $manager->flush();
            $listUsers[] = $user;
        }

        
        // Création des Tags
        for ($i = 0; $i < 7; $i++){
            $tag = new Tag;
            $tag
                ->setName($generator->unique()->tagName())
                ->setBackgroundColor($generator->hexcolor())
                ->setTextColor($generator->hexcolor())
                ->setSlug($this->slugger->slugify($tag->getName()));
            $manager->persist($tag);
            $manager->flush();
            $listTags[] = $tag;
        }

        // Création des Visiblités (Tout le monde, Amis, certains amis)
        $titles = ['Tout le monde', 'Amis'];
        for($i=0; $i<count($titles); $i++){
            $visibility = new Visibility();
            $visibility->setTitle($titles[$i]);
            $manager->persist($visibility);
            $manager->flush();
            $listVisibilities[] = $visibility;
        }

        // Création des Events
        for ($i = 0; $i < 100; $i++){
            $event = new Event;
            $event
                ->setName($generator->sentence($nbWords = 6, $variableNbWords = true))
                ->setZipcode($generator->postcode())
                ->setCity($generator->city())
                ->setDateAt($generator->dateTimeBetween($startDate = '+ 1 month', $endDate = '+ 2 years', $timezone = null))
                ->setTimeAt($generator->dateTime())
                ->setDescription($generator->paragraph($nbSentences = 6, $variableNbSentences = true))
                ->setDepartment($listDepartments[array_rand($listDepartments)])
                ->setOrganize($listUsers[array_rand($listUsers)])
                ->setParticipantsLimit($generator->numberBetween($min=1, $max=20))
                ->setSlug($this->slugger->slugify($event->getName()))
                // info : $generator->imageGenerator($dir, $width, $height, $format, $fullPath, $text$, $backgroundColor, $textColor)
                ->setPhoto($generator->imageGenerator('public/assets/images/events/' , 640, 432, 'jpeg', false, $event->getName(), $generator->hexcolor(), '#000000' ))
                ->setVisibility($listVisibilities[array_rand($listVisibilities)])
                ->setJoinTimeLimit($generator->dateTimeBetween($startDate = $event->getDateAt()->modify('-7 day'), $endDate = $event->getDateAt(), $timezone = null))
                ->setMinAge($generator->numberBetween(18,50))
                ->setMaxAge($generator->numberBetween($event->getMinAge()+5,85))
                ;
                
            $manager->persist($event);
            $manager->flush();
            $listEvents[] = $event;
        }

        // Création de participant aux evenements
        foreach($listEvents as $event) {
            shuffle($listUsers);
            $participation = new Participation();
                $participation->setEvent($event);
                $participation->setParticipant($event->getOrganize());
                $manager->persist($participation);
                $manager->flush();

            // Random pour choisir le nombre de participants à un event
            $rand = rand(0, $event->getParticipantsLimit());
            for ($i = 0; $i < $rand ; $i++) {
                $participation = new Participation();
                $participation->setEvent($event);
                $participation->setParticipant($listUsers[$i]);
                $manager->persist($participation);
                $manager->flush();
            }
        }
        
        // Attribution des Tags pour les Events
        foreach($listEvents as $event) {
            shuffle($listTags);

            // Random pour choisir le nombre de Tag pour un Event
            $randTag = rand(1, 4);
            for ($i = 0; $i < $randTag; $i++) {
                $event->addTag($listTags[$i]);
            }
        }
    
        // Création des Rapport sur Events
        for($i = 0; $i < 10; $i++) {
            $eventReporting = new EventReporting;
            $eventReporting->setDescription($generator->text($maxNbChars = (rand(50, 1000))))
                           ->setEvent($listEvents[array_rand($listEvents)])
                           ->setUser($listUsers[array_rand($listUsers)]);
            $manager->persist($eventReporting);
            $manager->flush();                        
        }                                                            

        // Création des rapport sur les users
        for($i = 0; $i < 10; $i++){
            $userReport = new UserReporting;
            $userReport->setDescription($generator->paragraph($nbSentences = 3, $variableNbSentences = true))
                       ->setUser($listUsers[array_rand($listUsers)])
                       ->setAccusedUser($listUsers[array_rand($listUsers)]);
            $manager->persist($userReport);
            $manager->flush();
        }
        
        // Création commentaire
        for ($i = 0; $i < 25; $i++){
            $comment = new Comment;
            $comment->setDescription($generator->sentence($nbWords = 10, $variableNbWords = true))
                    ->setEvent($listEvents[array_rand($listEvents)])
                    ->setUser($listUsers[array_rand($listUsers)]);
            $manager->persist($comment);
            $manager->flush();
        }

        // Création des Link (Ami/Liste noire)
        $friend = new Link();
        $friend->setName('friend');
        $listLink[] = $friend;
        $blackListed = new Link();
        $blackListed->setName('blackListed');
        $listLink[] = $blackListed;
        $manager->persist($friend);
        $manager->persist($blackListed);
        $manager->flush();

       // Friends/BlacList
        for ($i = 0 ; $i < 50 ; $i++) 
        {
            $relationShip = new RelationShip();
            $relationShip->setUserMain($listUsers[array_rand($listUsers)])
                         ->setLink($listLink[array_rand($listLink)])
                         ->setUserConcerned($listUsers[array_rand($listUsers)]);
            $manager->persist($relationShip);
            $manager->flush();
        }
                                                        
        $manager->flush();
    }
}