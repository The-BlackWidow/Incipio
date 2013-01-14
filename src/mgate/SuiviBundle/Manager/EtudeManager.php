<?php

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Manager\BaseManager;
use mgate\SuiviBundle\Entity\Etude as Etude;

class EtudeManager extends \Twig_Extension {

    protected $em;
    protected $tva;

    public function __construct(EntityManager $em, $tva) {
        $this->em = $em;
        $this->tva = $tva;
    }

    // Pour utiliser les fonctions depuis twig
    public function getName() {
        return 'mgate_EtudeManager';
    }

    // Pour utiliser les fonctions depuis twig
    public function getFunctions() {
        return array(
            'getRefEtude' => new \Twig_Function_Method($this, 'getRefEtude')
        );
    }

    /**
     * Get montant total HT
     */
    public function getTotalJEHHT(Etude $etude) {
        $total = 0;
        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getNbrJEH() * $phase->getPrixJEH();
        }

        return $total;
    }

    /**
     * Get montant total HT
     */
    public function getTotalHT(Etude $etude) {
        $total = $etude->getFraisDossier() + $this->getTotalJEHHT($etude);

        return $total;
    }

    /**
     * Get montant total TTC
     */
    public function getTotalTTC(Etude $etude) {
        return round($this->getTotalHT($etude) * (1 + $this->tva),2);
    }

    /**
     * Get nombre de JEH
     */
    public function getNbrJEH(Etude $etude) {
        $total = 0;

        foreach ($etude->getPhases() as $phase) {
            $total += $phase->getNbrJEH();
        }
        return $total;
    }

    /**
     * Get référence de l'etude
     */
    public function getRefEtude(Etude $etude) {
        return "[M-GaTE]" . (string) ($etude->getMandat() * 100 + $etude->getNum());
    }

    /**
     * Get référence document
     */
    public function getRefDoc(Etude $etude, $doc, $version) {
        return $this->getRefEtude($etude) . "-" . $doc . "-" . $version; //TODO faire les autres type de docs, genre RM
    }

    /**
     * Get nouveau numéro d'etude, pour valeur par defaut dans formulaire
     */
    public function getNouveauNumero($mandat = 5) {
        $qb = $this->em->createQueryBuilder();

        $query = $qb->select('e.num')
                ->from('mgateSuiviBundle:Etude', 'e')
                ->andWhere('e.mandat = :mandat')
                ->setParameter('mandat', $mandat)
                ->orderBy('e.num', 'DESC');

        $value = $query->getQuery()->setMaxResults(1)->getOneOrNullResult();
        if ($value)
            return $value['num'] + 1;
        else
            return 1;
    }

    public function getDateLancement(Etude $etude) {
        $dateDebut = array();
        $phases = $etude->getPhases();

        foreach ($phases as $phase)
            array_push($dateDebut, $phase->getDateDebut());
        
        return min($dateDebut);
    }
    
    public function getDateFin(Etude $etude) {
        $dateFin = array();
        $phases = $etude->getPhases();

        foreach ($phases as $p) {
           $dateDebut = clone $p->getDateDebut(); //WARN $a = $b : $a pointe vers le même objet que $b...
           array_push($dateFin, $dateDebut->modify('+'.$p->getDelai() . ' day'));
           unset($dateDebut);
        }
        return max($dateFin);
    }
    
        public function getDelaiEtude(Etude $etude)
    {
        return $this->getDateFin($etude)->diff($this->getDateLancement($etude));
    }

    public function getRepository() {
        return $this->em->getRepository('mgateSuiviBundle:Etude');
    }

}